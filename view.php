<!DOCTYPE html>
<html>
    <head>
    <title>Image Gallery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    </head>

    <body>
        <a href="upload.php" class="btn btn-warning">Back</a>
        <!-- (B) SEARCH FORM -->
        <form id="mySearch" method="POST">
        <div class="input-group mb-3">
          <input type="text" class="form-control form-control-sm" name="search" required placeholder="Enter Image Description to Search">
          <div class="input-group-append">
            <button class="btn btn-primary" type="submit">Search</button>
          </div>
        </div>
        </form>


        <!-- (C) SEARCH RESULTS -->
        <div id="results">
          <?php
          if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // (A) GET SEARCH TERM
            $searchTerm = $_POST['search'];
        
            // (A2) DATABASE CONNECTION AND QUERY
            $conn = mysqli_connect("localhost", "root", "", "lap3");
            if (!$conn) {
              die("Connection failed: " . mysqli_connect_error());
            }
        
            $sql = "SELECT id, description, file_name, upload_date FROM image_gallery WHERE description LIKE '%$searchTerm%'";
            $result = mysqli_query($conn, $sql);
        
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<div>";
                echo "<p>Image Description: ". $row['description'] . "</p>";
                echo "<p>Uploaded on: " . $row['upload_date'] . "</p>";
                echo "<p>File name: " . $row['file_name'] . "</p>";
                echo "<img src='images/" . $row['file_name'] . "' alt='Image'>";
                echo "</div> <br>";
              }
            } else {
              echo "No results found.";
            }
        
            mysqli_close($conn);
          }
          ?>
        </div>
    </body>
</html>


