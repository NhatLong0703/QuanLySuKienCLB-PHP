<?php
class BaseController {
    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    protected function getInputData() {
        if (!empty($_POST)) return $_POST;
        return $this->getJsonInput();
    }

    protected function uploadImage($fileField = 'image', $folder = 'events') {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) return null;
        
        $file = $_FILES[$fileField];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return null;

        $targetDir = __DIR__ . '/../public/uploads/' . $folder;
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = uniqid() . '.' . $ext;
        $targetFile = $targetDir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return '/uploads/' . $folder . '/' . $filename;
        }
        return null;
    }

    protected function getCurrentUser() {
        $token = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            $token = $headers['Authorization'] ?? '';
        }
        
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
            $data = json_decode(base64_decode($token), true);
            return $data;
        }
        return null;
    }

    protected function requireCurrentUser() {
        $user = $this->getCurrentUser();
        if (!$user) {
            $this->json(['status' => 'error', 'message' => 'Vui long dang nhap de thuc hien thao tac nay'], 401);
        }
        return $user;
    }

    protected function logAudit($userId, $action, $table, $targetId, $detail = '') {
        $repo = new AuditLogRepository();
        $detailJson = is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : json_encode(['message' => $detail], JSON_UNESCAPED_UNICODE);
        $repo->log($userId, $action, $table, $targetId, $detailJson);
    }

    protected function sendNotification($title, $content, $clubId = null, $eventId = null, $createdBy = null) {
        $repo = new NotificationRepository();
        $repo->create([
            'title' => $title,
            'content' => $content,
            'club_id' => $clubId,
            'event_id' => $eventId,
            'created_by' => $createdBy
        ]);
    }
}
