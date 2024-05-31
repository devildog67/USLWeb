<?php
header('Content-Type: application/json');

function testMethod() {
    return ['status' => 'success', 'message' => 'PHP method called successfully!'];
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = testMethod();
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

echo json_encode($response);
?>
