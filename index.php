<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูลผู้ใช้จากฐานข้อมูล
    $sql = 'SELECT * FROM "User" WHERE user_id = $1 AND password = $2';
    $result = pg_query_params($conn, $sql, array($user_id, $password));

    if ($result && pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);

        // บันทึกข้อมูลลงใน session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['first_name'] = $user['user_first_name'];
        $_SESSION['last_name'] = $user['user_last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['age'] = $user['age'];
        $_SESSION['car_registration'] = $user['car_registration'];
        $_SESSION['car_registration_img'] = $user['car_registration_img'];
        $_SESSION['user_img'] = $user['user_img'];

        // ตรวจสอบ role และพาไปยังแดชบอร์ดที่เหมาะสม
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TrackCarsApplication</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: radial-gradient(circle, rgba(238, 174, 202, 1) 0%, rgba(148, 187, 233, 1) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .navbar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin-top: 100px; /* เว้นระยะด้านบน */
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
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
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .alert {
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">TrackCarsApplication</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
     
        </div>
    </div>
</nav>

<!-- Login Form -->
<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="login-container">
        <h2 class="login-title">Login</h2>
        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="user_id" class="form-label">เลขบัตรประชาชน</label>
                <input type="text" name="user_id" id="user_id" class="form-control" placeholder="กรุณากรอกเลขบัตรประชาชน" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="กรุณากรอก รหัสผ่าน" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            <a href="register.php" class="btn btn-secondary w-100">สมัครการใช้งาน</a>
        </form>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
