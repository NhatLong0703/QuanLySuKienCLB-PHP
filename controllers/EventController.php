<?php
class EventController extends BaseController {
    private $eventRepo;
    public function __construct() { $this->eventRepo = new EventRepository(); }

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
        $d = $this->getJsonInput();
        $required = ['club_id','title','start_time','end_time','registration_deadline','capacity','created_by'];
        foreach($required as $f) if(empty($d[$f])) return $this->json(['status'=>'error','message'=>"Thieu truong: $f"],400);
        $d['status'] = $d['status'] ?? 'draft';
        $d['description'] = $d['description'] ?? '';
        $d['location'] = $d['location'] ?? '';
        $id = $this->eventRepo->create($d);
        return $this->json(['status'=>'success','message'=>'Tao su kien thanh cong','data'=>$this->eventRepo->findById($id)],201);
    }

    // PUT /api/event/update?id=X
    public function update() {
        if ($_SERVER['REQUEST_METHOD']!=='PUT') return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $d = $this->getJsonInput();
        $allowed = ['title','description','location','start_time','end_time','registration_deadline','capacity','status'];
        $update = array_intersect_key($d, array_flip($allowed));
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co du lieu can cap nhat'],400);
        $this->eventRepo->update($id, $update);
        return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong','data'=>$this->eventRepo->findById($id)]);
    }

    // DELETE /api/event/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $this->eventRepo->delete($id);
        return $this->json(['status'=>'success','message'=>'Da xoa su kien']);
    }
}
