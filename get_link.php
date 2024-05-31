<?php
require 'db_connection.php';

$id = $_GET['id'];

$sql = "SELECT * FROM Links WHERE Id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode([]);
}
?>
