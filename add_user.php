<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $role = "user";
    $password = $_POST['password']; 
    $carRegistration = $_POST['car_registration'];

    // จัดการการอัปโหลดรูปภาพผู้ใช้
    $profileImage = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profileImageName = uniqid() . '-' . basename($_FILES['profile_image']['name']);
        $profileImagePath = 'uploads/profile/' . $profileImageName;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath);
        $profileImage = $profileImagePath;
    }

    // จัดการการอัปโหลดรูปป้ายทะเบียนรถ
    $carImage = '';
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $carImageName = uniqid() . '-' . basename($_FILES['car_image']['name']);
        $carImagePath = 'uploads/car/' . $carImageName;
        move_uploaded_file($_FILES['car_image']['tmp_name'], $carImagePath);
        $carImage = $carImagePath;
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $sql = 'INSERT INTO "User" (user_first_name, user_last_name, email, age, car_registration, user_img, car_registration_img, role,  password) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)';
    $result = pg_query_params($conn, $sql, [$firstName, $lastName, $email, $age, $carRegistration, $profileImage, $carImage, $role, $password]);

    if ($result) {
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "Error adding user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New User</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Age:</label>
                <input type="number" name="age" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Car Registration:</label>
                <input type="text" name="car_registration" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Profile Image:</label>
                <input type="file" name="profile_image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">  
                <label>Car Image:</label>
                <input type="file" name="car_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
</body>
</html>
