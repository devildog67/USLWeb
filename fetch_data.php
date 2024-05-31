<?php
// fetch_data.php

// Include the database connection file
include 'db_connection.php';

header('Content-Type: application/json');

try {
    // Create a prepared statement to fetch categories and their corresponding links
    $query = "
        SELECT 
            Categories.Category, 
            Links.Title, 
            Links.URL, 
            Links.Description,
            Links.Screenshot,
            Links.FavIcon
        FROM 
            Categories
        INNER JOIN 
            Links ON Categories.Id = Links.CategoryId
        WHERE Links.Active = 1
        ORDER BY 
            Categories.Category, Links.Title
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $result = [];
    $stmt->bind_result($category, $title, $url, $description, $screenshot, $favicon);

    while ($stmt->fetch()) {
        $result[$category][] = [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'screenshot' => $screenshot,
            'favicon' => $favicon
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
