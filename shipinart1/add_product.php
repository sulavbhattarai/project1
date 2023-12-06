<!DOCTYPE html>
<html>

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


ini_set('error_reporting', 'E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR');

include 'connection.php';
$conn = OpenCon();

if (mysqli_connect_errno()) {
    echo "Unable to connect to server " . mysqli_connect_error();
}
session_start();

if ($_SESSION["admin"] == false) {
    echo '<script>alert("You are not admin. Only admins can view this page.");</script>';
    echo '<script>window.history.back();</script>';
    exit;
}

$target_dir = 'images/';
$filename = basename($_FILES["pic"]["name"]);
$target_file = $target_dir . $filename;

$uploadOk = 1;

// Check if file already exists
while (file_exists($target_file)) {
    $filename = time() . '_' . basename($_FILES["pic"]["name"]);
    $target_file = $target_dir . $filename;
}

// Check file size
if ($_FILES["pic"]["size"] > 500000) {
    echo '<script>alert("Sorry, your file is too large.")</script>';
    $uploadOk = 0;
}

// Allow only certain file formats
$allowed_extensions = array("jpg", "jpeg", "png", "gif");
$file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    echo '<script>alert("Sorry, only JPG, JPEG, PNG, and GIF files are allowed.")</script>';
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo '<script>window.location.href = "admin.php";</script>';
    exit;
} else {
    // Try to upload the file
    if (move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file)) {
        // File uploaded successfully

        $check_query = 'SELECT product_id FROM products ORDER BY product_id DESC';
        $result = mysqli_query($conn, $check_query);
        $row = $result->fetch_assoc();
        $p_id = $row['product_id'] + 1;

        $check_query = 'SELECT product_id FROM products WHERE product_id=' . $p_id;
        $result = mysqli_query($conn, $check_query);
        $number_of_rows = $result->num_rows;

        if ($number_of_rows == 1) {
            echo '<script>alert("Product already exists with this ID") </script>';
            echo '<script>window.location.href = "admin.php";</script>';
            exit;
        } else if ($number_of_rows == 0) {
            $add_query = 'INSERT INTO `products` (`product_id`, `product_name`, `category_id`, `date_added`, `description`, `price`, `icon_name`) VALUES (' . $p_id . ', \'' . $_POST['product_name'] . '\', ' . $_POST['category_id'] . ', NOW(), \'' . $_POST['description'] . '\',' . $_POST['price'] . ',\'' . $filename . '\')';

            $add = mysqli_query($conn, $add_query);
            if ($add == 1) {
                echo '<script>alert("Product has been added");</script>';
                echo '<script>window.location.href = "admin.php";</script>';
                exit;
            } else {
                echo '<script>alert("Error adding product")</script>';
                echo '<script>window.location.href = "admin.php";</script>';
                exit;
            }
        }
    } else {
        echo '<script>alert("Sorry, there was an error uploading your file.")</script>';
        echo '<script>window.location.href = "admin.php";</script>';
        exit;
    }
}
?>

</html>

