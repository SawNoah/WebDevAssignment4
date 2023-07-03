<?php
include 'index.php';
// Function to resize an image
function resizeImage($sourcePath, $targetPath, $maxWidth, $maxHeight)
{
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

    // Calculate the aspect ratio
    $aspectRatio = $sourceWidth / $sourceHeight;

    // Calculate the new dimensions while maintaining the aspect ratio
    if ($maxWidth / $maxHeight > $aspectRatio) {
        $newWidth = $maxHeight * $aspectRatio;
        $newHeight = $maxHeight;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    }

    // Create a new blank image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Load the source image based on its type
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
            // Add support for other image types if needed
        default:
            return false;
    }

    // Resize the image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

    // Save the resized image
    imagepng($newImage, $targetPath);

    // Free up memory
    imagedestroy($newImage);
    imagedestroy($sourceImage);

    return true;
}

// Image Upload
if (isset($_FILES['image'])) {
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $description = $_POST['description'];

    // Move the uploaded file to the images directory
    $target_dir = "images/";
    $target_file = $target_dir . basename($file_name);

    // Check if the file already exists
    if (file_exists($target_file)) {
        echo "File already exists.";
        exit;
        header("Location: index.php");
    }

    // Create the thumbnails directory if it doesn't exist
    $thumbnail_dir = "thumbnails/";
    if (!file_exists($thumbnail_dir)) {
        mkdir($thumbnail_dir, 0777, true);
        // Creates the directory with full permissions (you can adjust the permission value if needed)
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Resize the image (optional)
        $thumbnail_dir = "thumbnails/";
        $thumbnail_file = $thumbnail_dir . basename($file_name);
        resizeImage($target_file, $thumbnail_file, 200, 200);

        // Save image metadata to the database
        $conn = mysqli_connect("localhost", "root", "", "lap3");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "INSERT INTO image_gallery (file_name, description) VALUES ('$file_name', '$description')";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
    }
}
?>
<!Doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    $conn = mysqli_connect("localhost", "root", "", "lap3");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // Fetch images from the database
    $sql = "SELECT * FROM  image_gallery";
    $result = mysqli_query($conn, $sql);

    ?>
    <div class="gallery gallery-item">
        <table>
            <tr>
                <th>File Name</th>
                <th>Image Description</th>
                <th>Uploaded Date</th>
                <th>Thumbnail</th>
                <th>Edit Image</th>
            </tr>
            <tr>
                <?php
                if ($result->num_rows > 0) {
                    $images_dir = "thumbnails/";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td> {$row['file_name']} </td>";
                        echo "<td class='image-description'> {$row['description']} </td>";
                        echo "<td> {$row['upload_date']} </td>";
                        echo '<td><img src="' . $images_dir . $row["file_name"] . '" alt="Image"></td>';
                        echo "<td><a href = 'images.php? id= '{$row["id"]}''>View Image</a></td>";
                        echo "</tr>";
                    }
                }
                mysqli_close($conn);
                ?>
            </tr>
        </table>
    </div>
</body>
</html>
