<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../frontend/login.html");
    exit;
}

if (isset($_POST['add'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $brand       = mysqli_real_escape_string($conn, $_POST['brand']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = (float) $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Default empty image
    $image = "";

    // File upload
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName   = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Save relative path (so HTML can load it easily)
            $image = "uploads/" . $fileName;
        } else {
            echo "<script>alert('❌ Image upload failed.');window.history.back();</script>";
            exit;
        }
    }

    $sql = "INSERT INTO toys (name, brand, category, price, image, description) 
            VALUES ('$name', '$brand', '$category', $price, '$image', '$description')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ Toy added successfully!');window.location='../frontend/admin.php';</script>";
    } else {
        echo "<script>alert('❌ Database Error: " . mysqli_error($conn) . "');window.history.back();</script>";
    }
}
?>
