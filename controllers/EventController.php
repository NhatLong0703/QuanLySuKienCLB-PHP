<?php
class EventController extends BaseController {
    private $eventRepo;
    private $auditRepo;
    private $notiRepo;
    public function __construct() { 
        $this->eventRepo = new EventRepository(); 
        $this->auditRepo = new AuditLogRepository();
        $this->notiRepo = new NotificationRepository();
    }

    // GET /api/event/index
    public function index() {
        $filters = ['status'=>$_GET['status']??'','keyword'=>$_GET['keyword']??'','start_date'=>$_GET['start_date']??'','end_date'=>$_GET['end_date']??'','sort_by'=>$_GET['sort_by']??'start_time','club_id'=>$_GET['club_id']??''];
        return $this->json(['status'=>'success','data'=>$this->eventRepo->getAll($filters)]);
    }

    // GET /api/event/show?id=X
    public function show() {
        $ev = $this->eventRepo->findById($_GET['id']??0);
        if (!$ev) return $this->json(['status'=>'error','message'=>'Khong tim thay su kien'],404);
        return $this->json(['status'=>'success','data'=>$ev]);
    }

    // POST /api/event/create
    public function create() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $d = $this->getInputData();
        $required = ['club_id','title','start_time','end_time','registration_deadline','capacity','created_by'];
        foreach($required as $f) if(empty($d[$f])) return $this->json(['status'=>'error','message'=>"Thieu truong: $f"],400);
        $d['status'] = $d['status'] ?? 'draft';
        $d['description'] = $d['description'] ?? '';
        $d['location'] = $d['location'] ?? '';
        
        $imagePath = $this->uploadImage('image', 'events');
        if ($imagePath) $d['image'] = $imagePath;
        
        $id = $this->eventRepo->create($d);
        $this->auditRepo->log($d['created_by'], 'CREATE_EVENT', 'events', $id, $d);
        $this->notiRepo->create(['club_id'=>$d['club_id'], 'event_id'=>$id, 'title'=>"Sự kiện mới: ".$d['title'], 'content'=>"Một sự kiện mới vừa được tạo, đăng ký ngay!", 'created_by'=>$d['created_by']]);
        return $this->json(['status'=>'success','message'=>'Tao su kien thanh cong','data'=>$this->eventRepo->findById($id)],201);
    }

    // PUT /api/event/update?id=X
    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $d = $this->getInputData();
        $allowed = ['title','club_id','description','location','start_time','end_time','registration_deadline','capacity','status'];
        $update = array_intersect_key($d, array_flip($allowed));
        
        $imagePath = $this->uploadImage('image', 'events');
        if ($imagePath) $update['image'] = $imagePath;
        
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co du lieu can cap nhat'],400);
        $this->eventRepo->update($id, $update);
        $this->auditRepo->log($this->getCurrentUser()['id'] ?? 0, 'UPDATE_EVENT', 'events', $id, $update);
        return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong','data'=>$this->eventRepo->findById($id)]);
    }

    // DELETE /api/event/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $this->eventRepo->delete($id);
        $this->auditRepo->log($this->getCurrentUser()['id'] ?? 0, 'DELETE_EVENT', 'events', $id);
        return $this->json(['status'=>'success','message'=>'Da xoa su kien']);
    }

    // PUT /api/event/toggleStatus?id=X
    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD']!=='PUT') return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $d = $this->getJsonInput();
        if (empty($d['status'])) return $this->json(['status'=>'error', 'message'=>'Thieu status'], 400);
        $this->eventRepo->update($id, ['status' => $d['status']]);
        $this->auditRepo->log($this->getCurrentUser()['id'] ?? 0, 'TOGGLE_STATUS_EVENT', 'events', $id, ['status'=>$d['status']]);
        if($d['status'] === 'cancelled') {
            $this->notiRepo->create(['club_id'=>null, 'event_id'=>$id, 'title'=>"Sự kiện bị hủy", 'content'=>"Sự kiện đã bị hủy", 'created_by'=>$this->getCurrentUser()['id'] ?? 0]);
        }
        return $this->json(['status'=>'success','message'=>'Cap nhat trang thai thanh cong']);
    }
}
