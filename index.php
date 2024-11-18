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
    <title>Dragon Cursor Effect</title>

    <style>
        /*::after
        body {
            background: radial-gradient(circle, rgba(238, 174, 202, 1) 0%, rgba(148, 187, 233, 1) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }*/

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
        .welcome{
            color: #ffffff;
        }
        .car {
            color:aliceblue;
            margin-top: 60px;
            white-space: nowrap;
            overflow: hidden;
            border-right: 4px solid #ffffff;
            width: 0;
            animation: typing 3s steps(30) 1s forwards, blink 0.25s step-end infinite;
        }
        @keyframes typing {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }
        @keyframes blink {
            50% {
                border-color: transparent;
            }
        }
    </style>
</head>
<body>
    <div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>

    <!-- แสดงข้อความแอนิเมชัน -->
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <center>
            <h1 class="welcome fw-bold">Welcome to </h1>
            <h1 class="car mt-5">TrackCars Application</h1>
        </center>
    </div>

    <!-- ฟอร์มล็อกอิน -->
    <div class="container d-flex align-items-center justify-content-center vh-100 mb-5" id="loginForm">
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
                <button type="submit" class="btn btn-primary w-100 mb-3" id="liveAlertBtn">Login</button>
                <a href="register.php" class="btn btn-secondary w-100">สมัครการใช้งาน</a>
            </form>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // ใช้ setTimeout เพื่อเลื่อนหน้าไปยังฟอร์มล็อกอินเมื่อแอนิเมชันเสร็จ
        setTimeout(function() {
            // เลื่อนหน้ามาที่ฟอร์มล็อกอินหลังจากแอนิเมชันเสร็จ (3 วินาที)
            document.getElementById('loginForm').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 5000); // ระยะเวลา 3000 มิลลิวินาที (3 วินาที)


        
    </script>

</body>
</html>