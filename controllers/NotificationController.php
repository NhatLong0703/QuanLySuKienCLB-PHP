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
}
