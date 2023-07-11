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

// Delete an image
if (isset($_GET["delete_id"])) {
    $id = $_GET["delete_id"];

    // Retrieve the file name from the database
    $sql = "SELECT file_name FROM image_gallery WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['file_name'];

        // Delete the record from the database
        $sql = "DELETE FROM image_gallery WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $imageToDelete = "images/" . $file_name;
            $thumbnailToDelete = "thumbnails/" . $file_name;
            
            // Check if the file exists before deleting
            if (file_exists($imageToDelete)) {
                // Attempt to delete the file
                if (unlink($imageToDelete)) {
                    echo "Image file deleted successfully.<br>";
                } else {
                    echo "Unable to delete the image file.<br>";
                }
            } else {
                echo "Image file does not exist.<br>";
            }
            
            // Check if the thumbnail exists before deleting
            if (file_exists($thumbnailToDelete)) {
                // Attempt to delete the thumbnail
                if (unlink($thumbnailToDelete)) {
                    echo "Thumbnail file deleted successfully.<br>";
                } else {
                    echo "Unable to delete the thumbnail file.<br>";
                }
            } else {
                echo "Thumbnail file does not exist.<br>";
            }
            
            echo "Image deleted successfully.<br>";
        } else {
            echo "Error deleting image: " . $conn->error . "<br>";
        }
    } else {
        echo "Image not found.<br>";
    }
}

//View Image
if (isset($_GET["image_id"])) {
    $image_id = $_GET["image_id"];

    // Retrieve the file name from the database based on the image ID
    $sql = "SELECT file_name FROM image_gallery WHERE id = $image_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['file_name'];
        $image_path = "images/" . $file_name;

        // Display the selected image
        echo '<div class="gallery-item">';
        echo '<img src="' . $image_path . '" alt="Image">';
        echo '</div>';
    } else {
        echo "Image not found.<br>";
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
    $sql = "SELECT * FROM image_gallery";
    $result = mysqli_query($conn, $sql);
    
    ?>
    <div class="gallery gallery-item">
    <table>
        <tr>
            <th>File Name</th>
            <th>Image Description</th>
            <th>Uploaded Date</th>
            <th>Thumbnail</th>
            <th>View Image</th>
            <th>Delete Image</th>
        </tr>
        <tr>
            <?php
            if ($result->num_rows > 0){
                $images_dir = "thumbnails/";
                while ($row = $result->fetch_assoc()){
                echo "<tr>";
                echo "<td> {$row['file_name']} </td>";
                echo "<td class='image-description'> {$row['description']} </td>";
                echo "<td> {$row['upload_date']} </td>";
                echo '<td><img src="' . $images_dir . $row["file_name"] . '" alt="Image"></td>';
                echo "<td><a href='images.php?id={$row["id"]}'><button class='btn btn-primary'>View Image</button></a></td>";
                echo "<td><a href='?delete_id={$row["id"]}'><button class='btn btn-primary'>Delete</button></a></td>";
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