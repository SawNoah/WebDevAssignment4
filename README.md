# WebDevAssignment4
This is an Image Gallery Assignment.
There will be a total of 5 php files to run this project. (index.php, upload.php and images.php, delete.php and search.php)
All you need is to run the index.php along with the database.
We will be using "lap3" as database and "image_gallery" as a table.

This mini project will allow users to upload images and image's name, description and uploaded date  will be saved in the database but images will be saved in the computer.
After that your images will be shown in as a table along with its name, description, uploaded date and an image's thumbnail. At last, you can also view all your uploaded images, search images through their description and able to delete images from both database and folders.

The following is the mysql code to create a table.

CREATE TABLE image_gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
