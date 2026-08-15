<?php
class ClubManagerController extends BaseController {
    private $cmRepo;

    public function __construct() {
        $this->cmRepo = new ClubManagerRepository();
    }

    // POST /api/club-manager/assign
    public function assign() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message' => 'Method Not Allowed'], 405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') {
            return $this->json(['status' => 'error', 'message' => 'Chi Admin moi co the phan cong quan ly'], 403);
        }

        $input = $this->getJsonInput();
        if (empty($input['club_id']) || empty($input['user_id'])) {
            return $this->json(['status' => 'error', 'message' => 'Thieu thong tin'], 400);
        }

        $this->cmRepo->assign($input['club_id'], $input['user_id']);
        return $this->json(['status' => 'success', 'message' => 'Phan cong thanh cong']);
    }

    // POST /api/club-manager/revoke
    public function revoke() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return $this->json(['message' => 'Method Not Allowed'], 405);
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') {
            return $this->json(['status' => 'error', 'message' => 'Chi Admin moi co the huy phan cong'], 403);
        }

        $input = $this->getJsonInput();
        if (empty($input['club_id']) || empty($input['user_id'])) {
            return $this->json(['status' => 'error', 'message' => 'Thieu thong tin'], 400);
        }

        $this->cmRepo->revoke($input['club_id'], $input['user_id']);
        return $this->json(['status' => 'success', 'message' => 'Huy phan cong thanh cong']);
    }

    // GET /api/club-manager/list-by-club?club_id=X
    public function listByClub() {
        $clubId = $_GET['club_id'] ?? null;
        if (!$clubId) return $this->json(['status' => 'error', 'message' => 'Thieu club_id'], 400);
        
        return $this->json([
            'status' => 'success',
            'data' => $this->cmRepo->findByClub($clubId)
        ]);
    }
}
