<?php
abstract class BaseController {
    
    // Trả về JSON chuẩn
    protected function json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Lấy body từ request (chủ yếu cho API Fetch/Axios gửi bằng JSON)
    protected function getJsonInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
}
