<?php
$servername = "localhost:3306";
$username = "rvfdcfte_uslweb_user";
$password = "wMf!eGx5VTVJ";
$dbname = "rvfdcfte_uslweb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];

    // Check if URL already exists
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM Links WHERE URL = ?");
    $check_stmt->bind_param("s", $url);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "URL already exists in the database.";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO Links (URL) VALUES (?)");
        $stmt->bind_param("s", $url);

        // Execute the statement
        if ($stmt->execute()) {
            echo "URL inserted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>
