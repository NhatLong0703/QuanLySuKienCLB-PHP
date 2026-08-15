<?php
$base_url = 'http://localhost:8080/api';

function callApi($method, $endpoint, $data = null, $token = null) {
    global $base_url;
    $ch = curl_init($base_url . $endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . base64_encode(json_encode($token));
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "[$method] $endpoint -> HTTP $httpCode\n";
    echo $response . "\n\n";
    $response = preg_replace('/^\\xEF\\xBB\\xBF/', '', $response);
    return json_decode($response, true);
}

echo "=== TEST 1: Register User ===\n";
$email = "test_api_" . time() . "@example.com";
$password = "password123";
$res = callApi('POST', '/user/register', [
    'full_name' => 'API Test User',
    'email' => $email,
    'password' => $password,
    'role' => 'organizer'
]);

echo "=== TEST 2: Login User ===\n";
$loginRes = callApi('POST', '/user/login', [
    'email' => $email,
    'password' => $password
]);
$token = $loginRes['data'] ?? null;
if (!$token) {
    die("Login failed!\n");
}

echo "=== TEST 3: Create Notification ===\n";
callApi('POST', '/notification/create', [
    'club_id' => 1,
    'title' => 'Test Notification',
    'content' => 'This is a test notification from API test script'
], $token);

echo "=== TEST 4: Get Notifications ===\n";
callApi('GET', '/notification/list', null, $token);

// To test ClubManager and Attendance, we need an admin token and real club_id/event_id.
// Assuming user ID 1 is Admin, we can mock an admin token.
$adminToken = ['id' => 1, 'role' => 'admin', 'email' => 'admin@example.com'];

echo "=== TEST 5: Assign Club Manager ===\n";
callApi('POST', '/club-manager/assign', [
    'club_id' => 1,
    'user_id' => $token['id']
], $adminToken);

echo "=== TEST 6: Get Club Managers ===\n";
callApi('GET', '/club-manager/list-by-club?club_id=1');

echo "=== TEST 7: Check-in Attendance ===\n";
callApi('POST', '/attendance/check-in', [
    'registration_id' => 1,
    'note' => 'Checked in via API test'
], $adminToken);

echo "=== TEST 8: Get Audit Logs ===\n";
callApi('GET', '/audit-log/list', null, $adminToken);

echo "API tests completed.\n";
