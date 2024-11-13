<?php
session_start();
require_once 'db.php';

$userId = $_GET['id'];
$sql = 'SELECT * FROM "User" WHERE user_id = $1';
$result = pg_query_params($conn, $sql, [$userId]);
$user = pg_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $carRegistration = $_POST['car_registration'];

    // อัปเดตรูปโปรไฟล์
    $profileImage = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profileImageName = uniqid() . '-' . $_FILES['profile_image']['name']; // ใช้เฉพาะชื่อไฟล์
        $profileImagePath = 'uploads/profile/' . $profileImageName;
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath);
        $profileImage = $profileImageName; // เก็บเฉพาะชื่อไฟล์ในฐานข้อมูล
    }

    // อัปเดตรูปรถ
    $carImage = $user['car_image'];
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $carImageName = uniqid() . '-' . $_FILES['car_image']['name']; // ใช้เฉพาะชื่อไฟล์
        $carImagePath = './uploads/car/' . $carImageName;
        move_uploaded_file($_FILES['car_image']['tmp_name'], $carImagePath);
        $carImage = $carImageName; // เก็บเฉพาะชื่อไฟล์ในฐานข้อมูล
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql = 'UPDATE "User" SET user_first_name=$1, user_last_name=$2, email=$3, age=$4, car_registration=$5, user_img=$6, car_registration_img=$7 WHERE user_id=$8';
    $result = pg_query_params($conn, $sql, [$firstName, $lastName, $email, $age, $carRegistration, $profileImage, $carImage, $userId]);

    if ($result) {
        header('Location: user_dashboard.php');
        exit();
    } else {
        echo "Error updating user.";
    }
}
?>

<!-- Form for editing user similar to add_user.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit User</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['user_first_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['user_last_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Age:</label>
                <input type="number" name="age" class="form-control" value="<?= htmlspecialchars($user['age']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Car Registration:</label>
                <input type="text" name="car_registration" class="form-control" value="<?= htmlspecialchars($user['car_registration']); ?>" required>
            </div>
            <div class="mb-3">
                <label>Profile Image:</label>
                <input type="file" name="profile_image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label>Car Image:</label>
                <input type="file" name="car_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
