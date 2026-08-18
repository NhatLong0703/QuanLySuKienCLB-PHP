<?php
class DashboardController extends BaseController {
    private $dashRepo;

    public function __construct() {
        $this->dashRepo = new DashboardRepository();
    }

    // GET /api/dashboard/listings
    public function listings() {
        $user = $this->requireCurrentUser();
        // Allow admin and organizer
        if ($user['role'] !== 'admin' && $user['role'] !== 'organizer') {
            return $this->json(['status'=>'error','message'=>'Ban khong co quyen truy cap'], 403);
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 6;

        $result = $this->dashRepo->getPaginatedListings($page, $limit);

        return $this->json([
            'status' => 'success',
            'data' => $result
        ]);
    }
}
