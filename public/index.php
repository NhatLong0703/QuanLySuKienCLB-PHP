<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoloader cơ bản
spl_autoload_register(function ($class_name) {
    $paths = [
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
        __DIR__ . '/../config/'
    ];
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Simple Router
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Loại bỏ thư mục gốc nếu project chạy trong XAMPP (VD: /QuanLySuKienCLB-PHP/public/)
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $request_uri = str_replace($basePath, '', $request_uri);
}

// Bắt đầu route từ /api/
$path = str_replace('/api/', '', $request_uri);
$path = trim($path, '/');
$parts = explode('/', $path);

// Nếu không truyền gì, mặc định vào EventController -> index
$controllerName = 'EventController'; 
$action = 'index';

if (!empty($parts[0])) {
    // VD: /api/event -> EventController
    // VD: /api/registration -> RegistrationController
    $controllerName = ucfirst($parts[0]) . 'Controller';
}
if (isset($parts[1])) {
    $action = $parts[1];
}

$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        try {
            $controller->$action();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Không tìm thấy Action"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Không tìm thấy Controller: $controllerName"]);
}
