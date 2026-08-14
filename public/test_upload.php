<?php
$result = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result['POST'] = $_POST;
    $result['FILES'] = $_FILES;
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>
