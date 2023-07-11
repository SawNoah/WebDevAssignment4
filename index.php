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
    <h2>Image Gallery</h2>
    <div class="gallery gallery-item">
    <!-- Image Upload Form -->
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image">
        <input type="text" name="description" placeholder="Image Description">
        <input type="submit" value="Upload">
        <a href="view.php" class="btn btn-primary">Search</a>
    </form>
    </div>    

    <!-- Display Images in Gallery -->
    
        <?php
        //Connecting to mysql
        $conn = mysqli_connect("localhost", "root", "", "lap3");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // file_exists($filename): Checks if a file or directory exists.
        $fileExists = file_exists("images");
        if(!$fileExists){
            // mkdir($dirname): Creates a directory.
            $directoryCreated = mkdir("images");
            echo ($directoryCreated ? "Folder exists" : "Failed to create folder") . "<br>";
        }
        ?>

</body>
</html>
