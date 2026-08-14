<?php
class AuditLogController extends BaseController {
    private $auditRepo;
    public function __construct() { $this->auditRepo = new AuditLogRepository(); }

    // GET /api/auditlog/list
    public function list() {
        return $this->json(['status'=>'success', 'data'=>$this->auditRepo->getLogs(50)]);
    }
}
