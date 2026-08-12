<?php
class RegistrationController extends BaseController {
    private $regModel;

    public function __construct() {
        $this->regModel = new Registration();
    }

    // API: POST /api/registration/register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(["message" => "Method Not Allowed"], 405);
        }

        $input = $this->getJsonInput();
        if (empty($input['event_id']) || empty($input['user_id'])) {
            return $this->json(["status" => "error", "message" => "Thiếu event_id hoặc user_id"], 400);
        }

        try {
            $this->regModel->register($input['event_id'], $input['user_id']);
            return $this->json(["status" => "success", "message" => "Đăng ký thành công!"]);
        } catch (Exception $e) {
            // Lỗi quăng ra từ Model (bao gồm cả lỗi bắt từ DB Triggers SQLSTATE 45000)
            // Lỗi Trigger thường có dạng: SQLSTATE[45000]: <<message>>
            $msg = $e->getMessage();
            if (strpos($msg, 'SQLSTATE[45000]') !== false) {
                // Tách lấy message thân thiện
                $parts = explode('1644', $msg); // 1644 is typical mysql custom signal code
                if (count($parts) > 1) {
                    $msg = trim(preg_replace('/SQLSTATE\[\w+\]: General error: 1644 /', '', $msg));
                }
            }
            return $this->json(["status" => "error", "message" => $msg], 400);
        }
    }

    // API: POST /api/registration/cancel
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(["message" => "Method Not Allowed"], 405);
        }

        $input = $this->getJsonInput();
        if (empty($input['event_id']) || empty($input['user_id'])) {
            return $this->json(["status" => "error", "message" => "Thiếu event_id hoặc user_id"], 400);
        }

        try {
            $this->regModel->cancel($input['event_id'], $input['user_id']);
            return $this->json(["status" => "success", "message" => "Đã huỷ đăng ký thành công!"]);
        } catch (Exception $e) {
            return $this->json(["status" => "error", "message" => $e->getMessage()], 400);
        }
    }
}
