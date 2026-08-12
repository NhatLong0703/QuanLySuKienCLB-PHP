<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Load .env
require_once __DIR__ . '/../config/env.php';

// Autoloader
spl_autoload_register(function ($class) {
    $dirs = [__DIR__.'/../controllers/',__DIR__.'/../repositories/',__DIR__.'/../entities/',__DIR__.'/../config/'];
    foreach ($dirs as $dir) { $f = $dir.$class.'.php'; if (file_exists($f)) { require_once $f; return; } }
});

// Router
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') $request_uri = str_replace($basePath, '', $request_uri);

$path = trim(str_replace('/api/', '', $request_uri), '/');

if (empty($path) || $path === 'index.php') {
    header('Content-Type: text/html; charset=UTF-8');
    $homeFile = __DIR__.'/home.html';
    echo file_exists($homeFile) ? file_get_contents($homeFile) : '<h2>Home</h2>';
    exit;
}

$parts = explode('/', $path);
$controllerName = 'EventController';
$action = 'index';

if ($parts[0] === 'auth' && isset($parts[1])) {
    $controllerName = 'AuthController';
    $provider = $parts[1];
    $action = (isset($parts[2]) && $parts[2]==='callback') ? $provider.'Callback' : $provider;
} else if (!empty($parts[0])) {
    $controllerName = ucfirst($parts[0]).'Controller';
    if (isset($parts[1])) $action = $parts[1];
}

$controllerFile = __DIR__.'/../controllers/'.$controllerName.'.php';
if (file_exists($controllerFile)) {
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        try { $controller->$action(); }
        catch (Exception $e) { http_response_code(500); echo json_encode(['status'=>'error','message'=>$e->getMessage()]); }
    } else {
        http_response_code(404); echo json_encode(['status'=>'error','message'=>"Action not found: $action"]);
    }
} else {
    http_response_code(404); echo json_encode(['status'=>'error','message'=>"Controller not found: $controllerName"]);
}
