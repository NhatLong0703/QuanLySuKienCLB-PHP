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
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
            $data = json_decode(base64_decode($token), true);
            return $data;
        }
        return null;
    }
}
