<?php
class AttendanceController extends BaseController {
    private $attRepo;

    public function __construct() {
        $this->attRepo = new AttendanceRepository();
    }

    // POST /api/attendance/check-in
    public function checkIn() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message' => 'Method Not Allowed'], 405);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status' => 'error', 'message' => 'Ban khong co quyen diem danh'], 403);
        }

        $input = $this->getJsonInput();
        if (empty($input['registration_id'])) {
            return $this->json(['status' => 'error', 'message' => 'Thieu registration_id'], 400);
        }

        $note = $input['note'] ?? '';
        $id = $this->attRepo->checkIn($input['registration_id'], $user['id'], $note);
        
        return $this->json(['status' => 'success', 'message' => 'Diem danh thanh cong', 'data' => ['id' => $id]]);
    }

    // GET /api/attendance/list?event_id=X
    public function list() {
        $eventId = $_GET['event_id'] ?? null;
        if (!$eventId) return $this->json(['status' => 'error', 'message' => 'Thieu event_id'], 400);
        
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status' => 'error', 'message' => 'Ban khong co quyen xem danh sach'], 403);
        }

        return $this->json([
            'status' => 'success',
            'data' => $this->attRepo->getAttendanceList($eventId)
        ]);
    }
}
