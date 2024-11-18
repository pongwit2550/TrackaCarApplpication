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
    <style>
        body {
                margin: auto;
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
                overflow: auto;
                background: linear-gradient(315deg, rgba(101,0,94,1)3%, rgba(60,132,206,1) 38%, rgba(255,102,178,1) 68%,rgba(48,238,226,1) 98%);
                animation: gradient 15s ease infinite;
                background-size: 400% 400%;
                background-attachment: fixed;
            }

            @keyframes gradient {
                0% {
                    background-position: 0% 0%;
                }
                50% {
                    background-position: 100% 100%;
                }
                100% {
                    background-position: 0% 0%;
                }
            }

            .wave {
                background: rgb(255 255 255 / 25%);
                border-radius: 1000% 1000% 0 0;
                position: fixed;
                width: 200%;
                height: 12em;
                animation: wave 10s -3s linear infinite;
                transform: translate3d(0, 0, 0);
                opacity: 0.8;
                bottom: 0;
                left: 0;
                z-index: -1;
            }

            .wave:nth-of-type(2) {
                bottom: -1.25em;
                animation: wave 18s linear reverse infinite;
                opacity: 0.8;
            }

            .wave:nth-of-type(3) {
                bottom: -2.5em;
                animation: wave 20s -1s reverse infinite;
                opacity: 0.9;
            }

            @keyframes wave {
                2% {
                    transform: translateX(1);
                }

                25% {
                    transform: translateX(-25%);
                }

                50% {
                    transform: translateX(-50%);
                }

                75% {
                    transform: translateX(-25%);
                }

                100% {
                    transform: translateX(1);
                }
            }

            .card {
                border-radius: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .card-header {
                background-color: #007bff;
                color: white;
                font-weight: bold;
                text-align: center;
                padding: 15px;
                margin: 20px 0 0 0;
                border-radius: 8px;
                width: 90%;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            .card-body {
                padding: 20px;
            }

            .form-label {
                font-weight: bold;
            }

            .btn-primary {
                background-color: #007bff;
                border: none;
            }

            .btn-primary:hover {
                background-color: #0056b3;
            }
            .back-button {
                position: absolute; /* ใช้ absolute เพื่อให้อยู่ใน container */
                top: 10px; /* ระยะห่างจากด้านบน */
                right: 10px; /* ระยะห่างจากด้านขวา */
                background-color: #ff4d4d; /* สีแดง */
                color: white; /* สีของ X */
                font-size: 20px;
                font-weight: bold;
                width: 40px;
                height: 40px;
                display: flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%; /* ทำให้ปุ่มเป็นวงกลม */
                cursor: pointer; /* เปลี่ยนเคอร์เซอร์เมื่อเลื่อนผ่าน */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* เงา */
                transition: background-color 0.3s ease; /* การเปลี่ยนสีเมื่อ hover */
            }

            .back-button:hover {
            background-color: #e60000; /* สีแดงเข้มเมื่อ hover */
            }
    </style>
</head>
<body>
    <div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>
    <div class="container mt-5">
        <div class="card">
            <div class="back-button" onclick="goBack()">X</div>
                <center>
                    <div class="card-header text-center w-25">
                        Add New User
                </center>
            <form method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="mb-3">
                        <label>ชื่อ:</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>นามสกุล:</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>รหัสผ่าน:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>อายุ:</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>หมายเลขป้ายทะเบียนรถยนต์:</label>
                        <input type="text" name="car_registration" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>รูปผู้ใช้งาน (สามารถใช้ได้แค่ไฟลนามสกุล .png และ .jpg):</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">  
                        <label>รูปป้ายทะเบียนรถยนต์:</label>
                        <input type="file" name="car_image" class="form-control" accept="image/*">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                    <script>
                        // xย้อน
                        function goBack() {
                        window.history.back();
                        }
                    </script>
            </form>
        </div>
    </div>
</body>
</html>
