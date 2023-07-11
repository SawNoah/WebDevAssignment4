<?php
// File Listing
$images_dir = "images/";
$images = scandir($images_dir);

// Display Images in Gallery
foreach ($images as $image) {
    if ($image != "." && $image != "..") {
        echo '<div class="gallery-item">';
        echo '<img src="' . $images_dir . $image . '" alt="Image">';
        echo '</div>';
    }
}
?>