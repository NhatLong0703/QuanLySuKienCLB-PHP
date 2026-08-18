<?php
class ClubController extends BaseController {
    private $clubRepo;
    public function __construct() { $this->clubRepo = new ClubRepository(); }

    public function index() { 
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 6);
        $status = $_GET['status'] ?? null;
        if ($status === 'all') $status = null;
        
        return $this->json(['status'=>'success','data'=>$this->clubRepo->getAll($page, $limit, $status)]); 
    }

    public function show() {
        $c = $this->clubRepo->findById($_GET['id']??0);
        if (!$c) return $this->json(['status'=>'error','message'=>'Khong tim thay CLB'],404);
        return $this->json(['status'=>'success','data'=>$c]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $d = $this->getInputData();
        if (empty($d['name'])) return $this->json(['status'=>'error','message'=>'Thieu ten CLB'],400);
        
        $d['created_by'] = $user['id'];
        $d['status'] = $d['status'] ?? 'active';
        $d['description'] = $d['description'] ?? '';
        
        $imagePath = $this->uploadImage('image', 'clubs');
        if ($imagePath) $d['image'] = $imagePath;
        
        $id = $this->clubRepo->create($d);
        $this->logAudit($user['id'], 'Create Club', 'clubs', $id, 'Created club: ' . $d['name']);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)],201);
    }

    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $c = $this->clubRepo->findById($id);
        if (!$c) return $this->json(['status'=>'error','message'=>'Khong tim thay CLB'],404);

        $d = $this->getInputData();
        $allowed = ['name','description','status'];
        $update = array_intersect_key($d, array_flip($allowed));
        
        $imagePath = $this->uploadImage('image', 'clubs');
        if ($imagePath) $update['image'] = $imagePath;
        
        $this->clubRepo->update($id, $update);
        $this->logAudit($user['id'], 'Update Club', 'clubs', $id, 'Updated club ID: ' . $id);
        return $this->json(['status'=>'success','data'=>$this->clubRepo->findById($id)]);
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $c = $this->clubRepo->findById($id);
        if (!$c) return $this->json(['status'=>'error','message'=>'Khong tim thay CLB'],404);

        $this->clubRepo->delete($id);
        $this->logAudit($user['id'], 'Delete Club', 'clubs', $id, 'Deleted club ID: ' . $id);
        return $this->json(['status'=>'success','message'=>'Da xoa CLB']);
    }
}
