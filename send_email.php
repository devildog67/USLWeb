<?php
header('Content-Type: application/json');

function sendEmail($to, $subject, $message, $headers) {
    if (mail($to, $subject, $message, $headers)) {
        return ['status' => 'success', 'message' => 'Email sent successfully!'];
    } else {
        return ['status' => 'error', 'message' => 'Failed to send email.'];
    }
}

$response = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $to = $data['to'];
    $subject = $data['subject'];
    $message = $data['message'];
    $headers = "From: your-email@example.com"; // Replace with your email address

    $response = sendEmail($to, $subject, $message, $headers);
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

echo json_encode($response);
?>
