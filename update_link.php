<?php
require 'db_connection.php';

$id = $_POST['id'];
$categoryId = $_POST['categoryId'];
$title = $_POST['title'];
$url = $_POST['url'];
$description = $_POST['description'];
$screenshot = $_POST['screenshot'];
$favicon = $_POST['favicon'];
$active = $_POST['active'];

$sql = "UPDATE Links SET 
    CategoryId = '$categoryId',
    Title = '$title',
    URL = '$url',
    Description = '$description',
    Screenshot = '$screenshot',
    FavIcon = '$favicon',
    Active = '$active' 
    WHERE Id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
