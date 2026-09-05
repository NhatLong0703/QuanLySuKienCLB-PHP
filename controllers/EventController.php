<?php
class EventController extends BaseController {
    private $eventRepo;
    public function __construct() { $this->eventRepo = new EventRepository(); }

    // GET /api/event/index
    public function index() {
        $filters = [
            'status'=>$_GET['status']??'',
            'keyword'=>$_GET['keyword']??'',
            'start_date'=>$_GET['start_date']??'',
            'end_date'=>$_GET['end_date']??'',
            'sort_by'=>$_GET['sort_by']??'start_time',
            'club_id'=>$_GET['club_id']??'',
            'page' => (int)($_GET['page'] ?? 1),
            'limit' => (int)($_GET['limit'] ?? 6)
        ];
        
        if ($filters['status'] === 'all') $filters['status'] = '';

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
        
        file_put_contents(__DIR__ . '/../public/uploads/debug.txt', "POST: " . print_r($_POST, true) . "\nFILES: " . print_r($_FILES, true));

        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error','message'=>'Ban khong co quyen tao su kien'],403);
        }

        $d = $this->getInputData();
        $required = ['club_id','title','start_time','end_time','registration_deadline','capacity'];
        foreach($required as $f) {
            if(empty($d[$f])) return $this->json(['status'=>'error','message'=>"Thieu truong: $f"],400);
        }

        $validationError = $this->validateEventTimes($d);
        if ($validationError) return $this->json(['status'=>'error','message'=>$validationError],400);
        
        $d['created_by'] = $user['id'];
        $d['status'] = $d['status'] ?? 'draft';
        $d['description'] = $d['description'] ?? '';
        $d['location'] = $d['location'] ?? '';
        
        $imagePath = $this->uploadImage('image', 'events');
        if ($imagePath) $d['image'] = $imagePath;
        
        try {
            $id = $this->eventRepo->create($d);
            $this->logAudit($user['id'], 'Create Event', 'events', $id, 'Created event: ' . $d['title']);
            $this->sendNotification('New Event: ' . $d['title'], 'A new event has been scheduled. Check it out!', $d['club_id'], $id, $user['id']);
            return $this->json(['status'=>'success','message'=>'Tao su kien thanh cong','data'=>$this->eventRepo->findById($id)],201);
        } catch (Exception $e) {
            return $this->json(['status'=>'error', 'message'=>'Lỗi: ' . $e->getMessage()], 400);
        }
    }

    // PUT /api/event/update?id=X
    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $event = $this->eventRepo->findById($id);
        if (!$event) return $this->json(['status'=>'error','message'=>'Khong tim thay su kien'],404);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error','message'=>'Ban khong co quyen cap nhat su kien'],403);
        }

        $d = $this->getInputData();
        $allowed = ['title','club_id','description','location','start_time','end_time','registration_deadline','capacity','status'];
        $update = array_intersect_key($d, array_flip($allowed));

        $validationError = $this->validateEventTimes(array_merge($event, $update));
        if ($validationError) return $this->json(['status'=>'error','message'=>$validationError],400);
        
        $imagePath = $this->uploadImage('image', 'events');
        if ($imagePath) $update['image'] = $imagePath;
        
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co du lieu can cap nhat'],400);
        try {
            $this->eventRepo->update($id, $update);
            $this->logAudit($user['id'], 'Update Event', 'events', $id, 'Updated event ID: ' . $id);
            $this->sendNotification('Event Updated: ' . ($update['title'] ?? $event['title']), 'Details for this event have been updated.', $update['club_id'] ?? $event['club_id'], $id, $user['id']);
            return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong','data'=>$this->eventRepo->findById($id)]);
        } catch (Exception $e) {
            return $this->json(['status'=>'error', 'message'=>'Khong the cap nhat su kien: ' . $e->getMessage()],400);
        }
    }

    private function validateEventTimes($event) {
        $start = strtotime($event['start_time'] ?? '');
        $end = strtotime($event['end_time'] ?? '');
        $deadline = strtotime($event['registration_deadline'] ?? '');

        if (!$start || !$end || !$deadline) return 'Thoi gian su kien khong hop le';
        if ($end <= $start) return 'Thoi gian ket thuc phai sau thoi gian bat dau';
        if ($deadline > $start) return 'Han dang ky phai truoc hoac bang thoi gian bat dau';
        if (isset($event['capacity']) && (int)$event['capacity'] <= 0) return 'So luong toi da phai lon hon 0';
        return null;
    }

    // DELETE /api/event/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $event = $this->eventRepo->findById($id);
        if (!$event) return $this->json(['status'=>'error','message'=>'Khong tim thay su kien'],404);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error','message'=>'Ban khong co quyen xoa su kien'],403);
        }

        $this->eventRepo->delete($id);
        $this->logAudit($user['id'], 'Delete Event', 'events', $id, 'Deleted event ID: ' . $id);
        return $this->json(['status'=>'success','message'=>'Da xoa su kien']);
    }

    // PUT /api/event/toggleStatus?id=X
    public function toggleStatus() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $id = $_GET['id'] ?? 0;
        $event = $this->eventRepo->findById($id);
        if (!$event) return $this->json(['status'=>'error','message'=>'Khong tim thay su kien'],404);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error','message'=>'Ban khong co quyen cap nhat su kien'],403);
        }
        
        $d = $this->getInputData();
        if (empty($d['status']) || !in_array($d['status'], ['draft', 'open', 'closed'])) {
            return $this->json(['status'=>'error','message'=>'Trang thai khong hop le'],400);
        }
        
        $this->eventRepo->update($id, ['status' => $d['status']]);
        return $this->json(['status'=>'success','message'=>'Cap nhat trang thai thanh cong', 'data'=>$this->eventRepo->findById($id)]);
    }
}
