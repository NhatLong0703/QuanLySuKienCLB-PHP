<?php
class RegistrationController extends BaseController {
    private $regRepo;
    private $eventRepo;
    public function __construct() { $this->regRepo = new RegistrationRepository(); $this->eventRepo = new EventRepository(); }

    // POST /api/registration/register
    public function register() {
        if ($_SERVER['REQUEST_METHOD']!=='POST') return $this->json(['message'=>'Method Not Allowed'],405);
        $d = $this->getJsonInput();
        if (empty($d['event_id'])||empty($d['user_id'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        $ev = $this->eventRepo->findById($d['event_id']);
        if (!$ev) return $this->json(['status'=>'error','message'=>'Su kien khong ton tai'],404);
        if ($ev['status'] !== 'open') return $this->json(['status'=>'error','message'=>'Su kien khong con mo dang ky'],400);
        if ($ev['slots_left'] <= 0) return $this->json(['status'=>'error','message'=>'Su kien da het cho'],400);
        $existing = $this->regRepo->findByEventAndUser($d['event_id'], $d['user_id']);
        if ($existing && $existing->getStatus()==='registered') return $this->json(['status'=>'error','message'=>'Ban da dang ky su kien nay roi'],400);
        $id = $this->regRepo->create($d['event_id'], $d['user_id']);
        return $this->json(['status'=>'success','message'=>'Dang ky thanh cong'],201);
    }

    // POST /api/registration/cancel
    public function cancel() {
        $d = $this->getJsonInput();
        if (empty($d['registration_id'])||empty($d['event_id'])) return $this->json(['status'=>'error','message'=>'Thieu thong tin'],400);
        $this->regRepo->cancel($d['registration_id']);
        return $this->json(['status'=>'success','message'=>'Da huy dang ky']);
    }

    // GET /api/registration/byEvent?event_id=X
    public function byEvent() {
        $data = $this->regRepo->getByEvent($_GET['event_id']??0);
        return $this->json(['status'=>'success','data'=>$data]);
    }

    // GET /api/registration/mine?user_id=X
    public function mine() {
        $data = $this->regRepo->getMyRegistrations($_GET['user_id']??0);
        return $this->json(['status'=>'success','data'=>$data]);
    }
    
    // GET /api/registration/all
    public function all() {
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') {
            return $this->json(['status'=>'error', 'message'=>'Chi Admin moi co quyen xem tat ca'], 403);
        }
        $data = $this->regRepo->getAllRegistrations();
        return $this->json(['status'=>'success','data'=>$data]);
    }

    // PUT /api/registration/update?id=X
    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $d = $this->getInputData();
        $allowed = ['status'];
        $update = array_intersect_key($d, array_flip($allowed));
        
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co gi cap nhat'],400);
        $this->regRepo->update($id, $update);
        return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong']);
    }

    // DELETE /api/registration/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $this->regRepo->delete($id);
        return $this->json(['status'=>'success','message'=>'Da xoa']);
    }
}
