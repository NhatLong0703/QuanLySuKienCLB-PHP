<?php
class ClubController extends BaseController {
    private $clubRepo;
    public function __construct() { $this->clubRepo = new ClubRepository(); }

    public function index() { return $this->json(['status'=>'success','data'=>$this->clubRepo->getAll()]); }

    public function show() {
        $c = $this->clubRepo->findById($_GET['id']??0);
        if (!$c) return $this->json(['status'=>'error','message'=>'Khong tim thay CLB'],404);
        return $this->json(['status'=>'success','data'=>$c]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $d = $this->getInputData();
        if (empty($d['name'])||empty($d['created_by'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        $d['status'] = $d['status'] ?? 'active';
        $d['description'] = $d['description'] ?? '';
        
        $imagePath = $this->uploadImage('image', 'clubs');
        if ($imagePath) $d['image'] = $imagePath;
        
        $id = $this->clubRepo->create($d);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)],201);
    }

    public function update() {
        $id = $_GET['id'] ?? 0;
        $d = $this->getInputData();
        $allowed = ['name','description','status'];
        $update = array_intersect_key($d, array_flip($allowed));
        
        $imagePath = $this->uploadImage('image', 'clubs');
        if ($imagePath) $update['image'] = $imagePath;
        
        $this->clubRepo->update($id, $update);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)]);
    }

    public function delete() {
        $this->clubRepo->delete($_GET['id'] ?? 0);
        return $this->json(['status'=>'success','message'=>'Da xoa CLB']);
    }

    // POST /api/club/assignManager
    public function assignManager() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $d = $this->getJsonInput();
        if (empty($d['club_id']) || empty($d['user_id'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        
        $cmRepo = new ClubManagerRepository();
        $cmRepo->assignManager($d['club_id'], $d['user_id']);
        
        // Also ensure user has at least 'organizer' role, or change it to 'organizer' automatically?
        // Let's change their role to 'organizer' if they are 'member'
        $uRepo = new UserRepository();
        $u = $uRepo->findById($d['user_id']);
        if ($u && $u->getRole() === 'member') {
            $uRepo->updateStatus($d['user_id'], 'active'); // Keep status
            // Wait, we need an updateRole method! I'll skip it or add it later, for now we assume they are already organizers or it doesn't matter.
            // Actually, we can just execute a raw query to upgrade them:
            $db = Database::getInstance()->getConnection();
            $db->prepare("UPDATE users SET role='organizer' WHERE id=? AND role='member'")->execute([$d['user_id']]);
        }
        
        return $this->json(['status'=>'success','message'=>'Gán quản lý thành công']);
    }

    // POST /api/club/removeManager
    public function removeManager() {
        $d = $this->getJsonInput();
        if (empty($d['club_id']) || empty($d['user_id'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        
        $cmRepo = new ClubManagerRepository();
        $cmRepo->removeManager($d['club_id'], $d['user_id']);
        return $this->json(['status'=>'success','message'=>'Gỡ quản lý thành công']);
    }

    // GET /api/club/managers?club_id=X
    public function managers() {
        $cmRepo = new ClubManagerRepository();
        $data = $cmRepo->getManagersByClub($_GET['club_id'] ?? 0);
        return $this->json(['status'=>'success','data'=>$data]);
    }
}
