<?php
header('Access-Control-Allow-Origin: *'); // Allow access from any origin
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function sendEmail($to, $subject, $message, $headers) {
    if (mail($to, $subject, $message, $headers)) {
        return ['status' => 'success', 'message' => 'Email sent successfully!'];
    } else {
        return ['status' => 'error', 'message' => 'Failed to send email.'];
    }
}

function logMessage($message) {
    $date = date('Y-m-d H:i:s');
    $formattedMessage = "[$date] $message\n";
    error_log($formattedMessage);
}

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    // Handle preflight request
    exit(0);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $useremail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $email = 'info@uslweb.com';
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    if ($name && $email && $message) {
        $emailMessage = "Name: $name\nEmail: $useremail\n\nMessage:\n$message";
        $response = sendEmail("info@uslweb.com", "Contact Form Submission", $emailMessage, "From: $email");
        logMessage("Received message from $name <$email>: " . htmlspecialchars($message));
        echo json_encode($response);
    } else {
        logMessage("Invalid form data. IP: " . $_SERVER['REMOTE_ADDR']);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'Invalid form data.']);
    }
} else {
    logMessage("No form data received. IP: " . $_SERVER['REMOTE_ADDR']);
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'No form data received.']);
}
?>
