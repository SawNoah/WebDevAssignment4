<?php
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
?>