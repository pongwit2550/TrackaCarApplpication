<?php
session_start();
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $age = $_POST['age']; // รับค่าอายุ
    $car_registration_number = $_POST['car_registration_number']; // รับค่าทะเบียนรถ
    $role = 'user';
    $user_img = null;
    $car_registration_img = null;

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
    if ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        // ตรวจสอบว่า user_id หรือ email ซ้ำหรือไม่
        $check_sql = 'SELECT * FROM "User" WHERE user_id = $1 OR email = $2';
        $check_result = pg_query_params($conn, $check_sql, array($user_id, $email));
        if (pg_num_rows($check_result) > 0) {
            $error = "User ID หรือ Email นี้ถูกใช้งานแล้ว";
        } else {
            // จัดการการอัปโหลดรูปโปรไฟล์
            if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['user_img']['tmp_name'];
                $file_ext = strtolower(pathinfo($_FILES['user_img']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($file_ext, $allowed_extensions)) {
                    $user_img = 'uploads/profiles/' . uniqid('profile_', true) . '.' . $file_ext;
                    move_uploaded_file($file_tmp, $user_img);
                } else {
                    $error = "รองรับเฉพาะไฟล์รูปภาพ (jpg, jpeg, png, gif) เท่านั้น";
                }
            }

            // จัดการการอัปโหลดรูปทะเบียนรถ
            if (isset($_FILES['car_registration_img']) && $_FILES['car_registration_img']['error'] === UPLOAD_ERR_OK) {
                $car_tmp = $_FILES['car_registration_img']['tmp_name'];
                $car_ext = strtolower(pathinfo($_FILES['car_registration_img']['name'], PATHINFO_EXTENSION));

                if (in_array($car_ext, $allowed_extensions)) {
                    $car_registration_img = 'uploads/car_registrations/' . uniqid('car_', true) . '.' . $car_ext;
                    move_uploaded_file($car_tmp, $car_registration_img);
                } else {
                    $error = "รองรับเฉพาะไฟล์รูปภาพ (jpg, jpeg, png, gif) เท่านั้น";
                }
            }

            // บันทึกข้อมูลผู้ใช้ใหม่ลงในฐานข้อมูล
            if (empty($error)) {
                $sql = 'INSERT INTO "User" (user_id, password, user_first_name, user_last_name, email, role, user_img, car_registration_img, age, car_registration) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';
                $result = pg_query_params($conn, $sql, array($user_id, $password, $first_name, $last_name, $email, $role, $user_img, $car_registration_img, $age, $car_registration_number));

                if ($result) {
                    $success = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | TrackCarsApplication</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .register-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
        }
        .register-title {
            text-align: center;
            margin-bottom: 30px;
            color: #673ab7;
        }
        .form-control:focus {
            border-color: #673ab7;
            box-shadow: 0 0 0 0.2rem rgba(103, 58, 183, 0.25);
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2 class="register-title">Create Your Account</h2>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="user_id" class="form-label">User ID:</label>
            <input type="text" name="user_id" id="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name:</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name:</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="age" class="form-label">Age:</label>
            <input type="number" name="age" id="age" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="car_registration_number" class="form-label">Car Registration Number:</label>
            <input type="text" name="car_registration_number" id="car_registration_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="user_img" class="form-label">Profile Image:</label>
            <input type="file" name="user_img" id="user_img" class="form-control">
        </div>
        <div class="mb-3">
            <label for="car_registration_img" class="form-label">Car Registration Image:</label>
            <input type="file" name="car_registration_img" id="car_registration_img" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <!-- แสดงข้อความแจ้งเตือน -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success mt-3"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
