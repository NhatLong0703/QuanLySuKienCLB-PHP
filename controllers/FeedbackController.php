<?php
class FeedbackController extends BaseController {
    private $feedbackRepo;
    private $eventRepo;
    private $regRepo;

    public function __construct() {
        require_once __DIR__ . '/../repositories/FeedbackRepository.php';
        $this->feedbackRepo = new FeedbackRepository();
        $this->eventRepo = new EventRepository();
        $this->regRepo = new RegistrationRepository();
    }

    // POST /api/feedback/submit
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'],405);
        
        $user = $this->requireCurrentUser();
        $d = $this->getJsonInput();
        
        if (empty($d['event_id']) || empty($d['rating'])) {
            return $this->json(['status'=>'error', 'message'=>'Vui long cung cap event_id va rating'], 400);
        }
        
        // Ensure user actually attended or registered for the event
        $reg = $this->regRepo->findByEventAndUser($d['event_id'], $user['id']);
        if (!$reg) {
            return $this->json(['status'=>'error', 'message'=>'Ban chua tham gia su kien nay'], 403);
        }
        
        // Ensure event is finished
        $event = $this->eventRepo->findById($d['event_id']);
        if (!$event) return $this->json(['status'=>'error', 'message'=>'Su kien khong ton tai'], 404);
        
        $now = new DateTime();
        $end = new DateTime($event['end_time']);
        if ($end > $now && $event['status'] !== 'closed') {
            return $this->json(['status'=>'error', 'message'=>'Su kien chua ket thuc, ban chua the danh gia'], 400);
        }

        // Ensure user hasn't rated yet
        $existing = $this->feedbackRepo->findByUserAndEvent($user['id'], $d['event_id']);
        if ($existing) {
            return $this->json(['status'=>'error', 'message'=>'Ban da danh gia su kien nay roi'], 400);
        }

        $id = $this->feedbackRepo->create($d['event_id'], $user['id'], $d['rating'], $d['comment'] ?? '');
        $this->logAudit($user['id'], 'Submit Feedback', 'event_feedbacks', $id, 'Submitted rating: ' . $d['rating']);
        
        return $this->json(['status'=>'success', 'message'=>'Cam on ban da danh gia!'], 201);
    }

    // GET /api/feedback/list?event_id=X
    public function list() {
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error', 'message'=>'Ban khong co quyen xem'], 403);
        }
        
        $eventId = $_GET['event_id'] ?? 0;
        $feedbacks = $this->feedbackRepo->getByEvent($eventId);
        $stats = $this->feedbackRepo->getEventStats($eventId);
        
        return $this->json([
            'status'=>'success',
            'data'=> [
                'stats' => $stats,
                'feedbacks' => $feedbacks
            ]
        ]);
    }
}
