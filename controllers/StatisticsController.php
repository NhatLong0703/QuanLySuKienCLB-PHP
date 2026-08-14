<?php
class StatisticsController extends BaseController {
    private $db;
    public function __construct() { $this->db = Database::getInstance()->getConnection(); }

    // GET /api/statistics/admin
    public function admin() {
        $stats = [];
        $stats['total_users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_clubs'] = $this->db->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
        $stats['total_events'] = $this->db->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $stats['total_registrations'] = $this->db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
        
        $recentEvents = $this->db->query("SELECT title, registered_count, capacity FROM events ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        return $this->json(['status'=>'success', 'data'=>['overview'=>$stats, 'recent_events'=>$recentEvents]]);
    }
}
