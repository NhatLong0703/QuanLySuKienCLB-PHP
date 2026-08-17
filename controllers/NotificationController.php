<?php
class NotificationController extends BaseController {
    private $notiRepo;

    public function __construct() {
        $this->notiRepo = new NotificationRepository();
    }

    // POST /api/notification/create
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message' => 'Method Not Allowed'], 405);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status' => 'error', 'message' => 'Ban khong co quyen tao thong bao'], 403);
        }

        $input = $this->getJsonInput();
        if (empty($input['title']) || empty($input['content'])) {
            return $this->json(['status' => 'error', 'message' => 'Thieu tieu de hoac noi dung'], 400);
        }

        $input['created_by'] = $user['id'];
        $id = $this->notiRepo->create($input);
        
        return $this->json(['status' => 'success', 'message' => 'Tao thong bao thanh cong', 'data' => ['id' => $id]]);
    }

    // GET /api/notification/list
    public function list() {
        $filters = [
            'club_id'  => $_GET['club_id'] ?? null,
            'event_id' => $_GET['event_id'] ?? null
        ];
        
        return $this->json([
            'status' => 'success',
            'data' => $this->notiRepo->getList($filters)
        ]);
    }

    // PUT /api/notification/update?id=X
    public function update() {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'POST'])) return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $d = $this->getInputData();
        $allowed = ['title', 'content', 'club_id', 'event_id'];
        $update = array_intersect_key($d, array_flip($allowed));
        
        if (empty($update)) return $this->json(['status'=>'error','message'=>'Khong co gi cap nhat'],400);
        $this->notiRepo->update($id, $update);
        return $this->json(['status'=>'success','message'=>'Cap nhat thanh cong']);
    }

    // DELETE /api/notification/delete?id=X
    public function delete() {
        if ($_SERVER['REQUEST_METHOD']!=='DELETE') return $this->json(['message'=>'Method Not Allowed'],405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') return $this->json(['status'=>'error','message'=>'Ban khong co quyen'],403);
        
        $id = $_GET['id'] ?? 0;
        $this->notiRepo->delete($id);
        return $this->json(['status'=>'success','message'=>'Da xoa']);
    }
}
