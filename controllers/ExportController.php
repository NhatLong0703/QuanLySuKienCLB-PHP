<?php
class ExportController extends BaseController {
    // GET /api/export/participants?event_id=X
    public function participants() {
        $eventId = $_GET['event_id'] ?? 0;
        $regRepo = new RegistrationRepository();
        $rows = $regRepo->getByEvent($eventId);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="participants_event_'.$eventId.'.csv"');
        echo "\xEF\xBB\xBF"; // BOM UTF-8
        $out = fopen('php://output','w');
        fputcsv($out, ['ID','Ho va Ten','Email','So dien thoai','Trang thai','Ngay dang ky']);
        foreach ($rows as $r) fputcsv($out,[$r['id'],$r['full_name'],$r['email'],$r['phone']??'',$r['status'],$r['registered_at']]);
        fclose($out);
        exit;
    }
}
