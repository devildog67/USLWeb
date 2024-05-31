<?php
// Allow access from the Chrome extension
header('Access-Control-Allow-Origin: chrome-extension://lnhkjmcobfcjpfbphedefmeaibdkgiii'); // Use your actual extension ID
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-SECRET-KEY');

include 'db_connection.php';

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

// Your Base64 encoded secret key
$encoded_secret_key = 'VGhlVWx0aW1hdGVTdXJ2aXZhbExpYnJhcnk='; // Use your Base64 encoded key
$valid_secret_key = base64_decode($encoded_secret_key); // Decode the secret key

// Get headers and normalize them to lowercase
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
//logMessage("Received headers: " . print_r($headers, true));

if (!isset($headers['x-secret-key'])) {
    logMessage("No secret key provided. IP: " . $_SERVER['REMOTE_ADDR']);
    header('HTTP/1.1 403 Forbidden');
    exit('No secret key provided.');
}

$incoming_encoded_secret_key = $headers['x-secret-key'];
$incoming_secret_key = base64_decode($incoming_encoded_secret_key);
//logMessage("Incoming secret key (decoded): " . $incoming_secret_key);
//logMessage("Valid secret key: " . $valid_secret_key);

if ($incoming_secret_key !== $valid_secret_key) {
    logMessage("Invalid secret key. IP: " . $_SERVER['REMOTE_ADDR']);
    header('HTTP/1.1 403 Forbidden');
    exit('Invalid secret key.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = filter_var($_POST['url'], FILTER_VALIDATE_URL);
    if ($url) {
        $response = sendEmail("info@uslweb.com", "Submitted URL", $url, "From: info@uslweb.com");
        //logMessage("Received URL: " . htmlspecialchars($url));
        //echo "Received URL: " . htmlspecialchars($url);
    } else {
        logMessage("Invalid URL provided. IP: " . $_SERVER['REMOTE_ADDR']);
        //echo "Invalid URL.";
    }
} else {
    logMessage("No URL received. IP: " . $_SERVER['REMOTE_ADDR']);
    echo "No URL received.";
}
?>
