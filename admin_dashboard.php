<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo "<p style='color:red; text-align:center;'>Not authorized. You must log in first.</p>";
    exit;
}

require 'db_connection.php';

$limit = 10; // Number of entries to show per page
if (isset($_GET["page"])) { 
    $page  = $_GET["page"]; 
} else { 
    $page=1; 
};  
$start_from = ($page-1) * $limit;

// Fetch categories and links
$sql_categories = "SELECT * FROM Categories";
$result_categories = $conn->query($sql_categories);

$categories = [];
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}

$filter = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';

$sql_links = "SELECT Id, CategoryId, Title, SUBSTRING(URL, 1, 50) as URL, SUBSTRING(Description, 1, 100) as Description, Screenshot, FavIcon, Active FROM Links WHERE Title LIKE '%$filter%' OR Description LIKE '%$filter%' OR URL LIKE '%$filter%' LIMIT $start_from, $limit";
$result_links = $conn->query($sql_links);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css?v=1.0.0" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .container {
            max-width: fit-content;
            margin-left: 10px;
        }
        .table-container {
            width: 100%;
            margin: 0 auto;
        }
        .table {
            width: 100%;
            table-layout: fixed;
        }
        .table thead th {
            text-align: center;
        }
        .filter-container {
            margin-bottom: 15px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Welcome to the Admin Dashboard</h1>
        <div class="filter-container">
            <form method="get" action="admin_dashboard.php">
                <input type="text" name="filter" id="filterInput" class="form-control" placeholder="Filter..." value="<?php echo htmlspecialchars($filter); ?>">
                <button type="submit" class="btn btn-primary mt-2">Apply Filter</button>
            </form>
        </div>
        <div class="table-container mt-4">
            <table class="table table-bordered table-responsive" id="dataTable">
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Description</th>
                        <th>Screenshot</th>
                        <th>FavIcon</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result_links->fetch_assoc()) { ?>
                    <tr>
                        <td><button class="btn btn-primary btn-edit" data-toggle="modal" data-target="#editModal" data-id="<?php echo $row['Id']; ?>">Edit</button></td>
                        <td><?php echo $row['Id']; ?></td>
                        <td><?php echo $row['CategoryId']; ?></td>
                        <td><?php echo $row['Title']; ?></td>
                        <td><?php echo $row['URL']; ?>...</td>
                        <td><?php echo $row['Description']; ?>...</td>
                        <td><?php echo $row['Screenshot']; ?></td>
                        <td><?php echo $row['FavIcon']; ?></td>
                        <td><?php echo $row['Active'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php 
        // Pagination code
        $sql = "SELECT COUNT(Id) FROM Links WHERE Title LIKE '%$filter%' OR Description LIKE '%$filter%' OR URL LIKE '%$filter%'";  
        $result = $conn->query($sql);  
        $row = $result->fetch_row();  
        $total_records = $row[0];  
        $total_pages = ceil($total_records / $limit);  
        $pagLink = "<nav><ul class='pagination justify-content-center'>";  
        for ($i=1; $i<=$total_pages; $i++) {  
            $pagLink .= "<li class='page-item'><a class='page-link' href='admin_dashboard.php?page=".$i."&filter=".urlencode($filter)."'>".$i."</a></li>";  
        };  
        echo $pagLink . "</ul></nav>";  
        ?>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="editForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Link</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editId" name="id">
                            <div class="form-group">
                                <label for="editCategoryId">Category</label>
                                <select class="form-control" id="editCategoryId" name="categoryId">
                                    <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo $category['Id']; ?>"><?php echo $category['Category']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editTitle">Title</label>
                                <input type="text" class="form-control" id="editTitle" name="title">
                            </div>
                            <div class="form-group">
                                <label for="editURL">URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="editURL" name="url">
                                    <div class="input-group-append">
                                        <a id="urlLink" href="#" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-globe"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="editDescription">Description</label>
                                <textarea class="form-control" id="editDescription" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editScreenshot">Screenshot</label>
                                <input type="text" class="form-control" id="editScreenshot" name="screenshot">
                            </div>
                            <div class="form-group">
                                <label for="editFavIcon">FavIcon</label>
                                <input type="text" class="form-control" id="editFavIcon" name="favicon">
                            </div>
                            <div class="form-group">
                                <label for="editActive">Active</label>
                                <select class="form-control" id="editActive" name="active">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on("click", ".btn-edit", function () {
            var id = $(this).data('id');
            $.ajax({
                url: 'get_link.php',
                type: 'GET',
                data: { id: id },
                success: function(data) {
                    var link = JSON.parse(data);
                    $("#editId").val(link.Id);
                    $("#editCategoryId").val(link.CategoryId);
                    $("#editTitle").val(link.Title);
                    $("#editURL").val(link.URL);
                    $("#urlLink").attr("href", link.URL);
                    $("#editDescription").val(link.Description);
                    $("#editScreenshot").val(link.Screenshot);
                    $("#editFavIcon").val(link.FavIcon);
                    $("#editActive").val(link.Active);
                }
            });
        });

        $("#editCategoryId").on("change", function() {
            var selectedCategoryId = $(this).val();
            $("#editCategoryId").val(selectedCategoryId);
        });

        $("#editForm").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                url: 'update_link.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        });
    </script>
</body>
</html>
