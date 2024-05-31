<?php
require_once 'db_connection.php';

// Start the session
session_start();

// Get the username and password from the form
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Prepare and execute the query to fetch the user
$stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Verify the password using password_verify
    if (password_verify($password, $user['password'])) {
        // User is authenticated
        $_SESSION['authenticated'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<p style='color:red; text-align:center;'>Not authorized. Invalid username or password.</p>";
    }
} else {
    echo "<p style='color:red; text-align:center;'>Not authorized. Invalid username or password.</p>";
}

$stmt->close();
$conn->close();
?>
