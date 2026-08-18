<?php
class DashboardRepository extends BaseRepository {
    public function getPaginatedListings($page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;
        
        // Count total for pagination
        $countSql = "
            SELECT SUM(cnt) as total FROM (
                SELECT COUNT(*) as cnt FROM clubs
                UNION ALL
                SELECT COUNT(*) as cnt FROM events
            ) t
        ";
        $stmtCount = $this->db->query($countSql);
        $total = $stmtCount->fetchColumn();

        // Fetch paginated data
        // We use UNION to combine clubs and events, sort them by booked descending
        $sql = "
            SELECT * FROM (
                SELECT 
                    'Club' as type, 
                    id, 
                    name as title, 
                    'Activity' as cat, 
                    status, 
                    0 as price, 
                    10 as booked, -- Dummy static booked count for clubs, or subquery if we had club_members table
                    50 as total, 
                    image as img, 
                    created_at 
                FROM clubs
                UNION ALL
                SELECT 
                    'Event' as type, 
                    id, 
                    title, 
                    'Event' as cat, 
                    status, 
                    0 as price, 
                    registered_count as booked, 
                    capacity as total, 
                    image as img, 
                    created_at 
                FROM events
            ) t
            ORDER BY booked DESC, created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
}
