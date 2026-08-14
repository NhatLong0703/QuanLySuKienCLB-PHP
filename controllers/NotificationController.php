<?php
class NotificationController extends BaseController {
    private $notiRepo;
    public function __construct() { $this->notiRepo = new NotificationRepository(); }

    // POST /api/notification/create
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'], 405);
        $d = $this->getJsonInput();
        if (empty($d['title']) || empty($d['content']) || empty($d['created_by'])) return $this->json(['status'=>'error', 'message'=>'Thieu thong tin'], 400);
        
        $data = [
            'club_id' => !empty($d['club_id']) ? $d['club_id'] : null,
            'event_id' => !empty($d['event_id']) ? $d['event_id'] : null,
            'title' => $d['title'],
            'content' => $d['content'],
            'created_by' => $d['created_by']
        ];
        $id = $this->notiRepo->create($data);
        return $this->json(['status'=>'success', 'message'=>'Tao thong bao thanh cong', 'data'=>['id'=>$id]]);
    }

    // GET /api/notification/my?user_id=X
    public function my() {
        $userId = $_GET['user_id'] ?? 0;
        $data = $this->notiRepo->getForUser($userId);
        return $this->json(['status'=>'success', 'data'=>$data]);
    }

    // GET /api/notification/all
    public function all() {
        return $this->json(['status'=>'success', 'data'=>$this->notiRepo->getAll()]);
    }
}
