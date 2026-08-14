<?php
class AttendanceController extends BaseController {
    private $attendanceRepo;
    public function __construct() { $this->attendanceRepo = new AttendanceRepository(); }

    // POST /api/attendance/checkin
    public function checkin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message'=>'Method Not Allowed'], 405);
        $d = $this->getJsonInput();
        if (empty($d['registration_id']) || empty($d['checked_in_by'])) return $this->json(['status'=>'error', 'message'=>'Thieu thong tin'], 400);
        try {
            $id = $this->attendanceRepo->checkIn($d['registration_id'], $d['checked_in_by'], $d['note'] ?? '');
            return $this->json(['status'=>'success', 'message'=>'Diem danh thanh cong', 'data'=>['id'=>$id]]);
        } catch (Exception $e) { return $this->json(['status'=>'error', 'message'=>$e->getMessage()], 500); }
    }

    // GET /api/attendance/event?event_id=X
    public function event() {
        $eventId = $_GET['event_id'] ?? 0;
        $data = $this->attendanceRepo->getByEvent($eventId);
        return $this->json(['status'=>'success', 'data'=>$data]);
    }
}
