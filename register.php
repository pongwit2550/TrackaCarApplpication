<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $car_registration_number = $_POST['car_registration_number'];

    // ตรวจสอบรหัสผ่านว่าตรงกันหรือไม่
    if ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        // ตรวจสอบและอัปโหลดรูปภาพโปรไฟล์
        $user_img = '';
        if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profile/';
            $file_name = basename($_FILES['user_img']['name']);
            $user_img = uniqid() . '_' . $file_name;
            $target_file = $upload_dir . $user_img;

            // ตรวจสอบและย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (!move_uploaded_file($_FILES['user_img']['tmp_name'], $target_file)) {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพโปรไฟล์";
            }
        }

        // ตรวจสอบและอัปโหลดรูปภาพป้ายทะเบียนรถ
        $car_plate_img = '';
        if (isset($_FILES['car_plate_img']) && $_FILES['car_plate_img']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/car/';
            $file_name = basename($_FILES['car_plate_img']['name']);
            $car_plate_img = uniqid() . '_' . $file_name;
            $target_file = $upload_dir . $car_plate_img;

            // ตรวจสอบและย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (!move_uploaded_file($_FILES['car_plate_img']['tmp_name'], $target_file)) {
                $error = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพป้ายทะเบียนรถ";
            }
        }

        // ถ้าไม่มีข้อผิดพลาด ให้บันทึกข้อมูลลงฐานข้อมูล
        if (empty($error)) {
            $sql = 'INSERT INTO "User" (user_id, password, user_first_name, user_last_name, email, age, car_registration, user_img, car_registration_img, role) 
                    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';
            $result = pg_query_params($conn, $sql, array(
                $user_id,
                $password,
                $first_name,
                $last_name,
                $email,
                $age,
                $car_registration_number,
                $user_img,
                $car_plate_img,
                'user'
            ));

            if ($result) {
                $success = "สมัครสมาชิกสำเร็จ!";
            } else {
                $error = "ไม่สามารถสมัครสมาชิกได้ กรุณาลองใหม่";
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
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .register-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        .register-title {
            text-align: center;
            margin-bottom: 30px;
            color: #6a11cb;
            font-weight: bold;
        }
        .form-control:focus {
            border-color: #2575fc;
            box-shadow: 0 0 8px rgba(38, 143, 255, 0.5);
        }
        .btn-primary {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
        }
        .alert {
            margin-top: 20px;
            border-radius: 8px;
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
            <label for="user_img" class="form-label">Profile Picture:</label>
            <input type="file" name="user_img" id="user_img" class="form-control">
        </div>
        <div class="mb-3">
            <label for="car_plate_img" class="form-label">Car Plate Picture:</label>
            <input type="file" name="car_plate_img" id="car_plate_img" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
        <a href="index.php" class="btn btn-secondary w-100">Back</a>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
