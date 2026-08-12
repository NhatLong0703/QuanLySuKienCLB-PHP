<?php
class EventController extends BaseController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    // API: GET /api/event/index
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->json(["message" => "Method Not Allowed"], 405);
        }

        $filters = [];
        if (isset($_GET['club_id'])) $filters['club_id'] = (int)$_GET['club_id'];
        if (isset($_GET['status'])) $filters['status'] = $_GET['status'];

        $events = $this->eventModel->getAll($filters);
        return $this->json([
            "status" => "success",
            "data" => $events
        ]);
    }

    // API: GET /api/event/detail?id=1
    public function detail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->json(["message" => "Method Not Allowed"], 405);
        }

        if (!isset($_GET['id'])) {
            return $this->json(["status" => "error", "message" => "Thiếu ID sự kiện"], 400);
        }

        $event = $this->eventModel->getById((int)$_GET['id']);
        if (!$event) {
            return $this->json(["status" => "error", "message" => "Không tìm thấy sự kiện"], 404);
        }

        return $this->json([
            "status" => "success",
            "data" => $event
        ]);
    }
}
