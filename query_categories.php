<?php
include 'db_connection.php';

$sql = "SELECT * FROM Categories";
$result = $conn->query($sql);

$categories = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    echo "0 results";
}

$conn->close();

echo json_encode($categories);
?>
