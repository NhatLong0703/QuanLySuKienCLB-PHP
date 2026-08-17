<?php
class AuditController extends BaseController {
    private $auditRepo;

    public function __construct() {
        $this->auditRepo = new AuditLogRepository();
    }

    // GET /api/audit-log/list
    public function list() {
        $user = $this->requireCurrentUser();
        if ($user['role'] !== 'admin') {
            return $this->json(['status' => 'error', 'message' => 'Chi Admin moi co the xem nhat ky he thong'], 403);
        }

        $page = (int)($_GET['page'] ?? 1);
        
        return $this->json([
            'status' => 'success',
            'data' => $this->auditRepo->getLogs($page)
        ]);
    }
}
