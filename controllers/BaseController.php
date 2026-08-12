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
