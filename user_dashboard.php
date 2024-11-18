<?php
session_start();
require_once 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วและเป็นผู้ใช้ทั่วไป
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้และบันทึกเวลาของผู้ใช้
$sql = 'SELECT * FROM "User" 
        INNER JOIN "Record_Time" 
        ON "User".user_id = "Record_Time".user_id 
        WHERE "User".user_id = $1';
$result = pg_query_params($conn, $sql, array($id));

if ($result === false) {
    echo "Error in query execution.";
    exit();
}

$user = pg_fetch_all($result); // ใช้ fetch_all เพราะต้องการข้อมูลหลายแถว

// ตรวจสอบผลลัพธ์ว่าเป็น array หรือไม่ก่อนใช้งาน
if ($user === false) {
    echo "No user data found.";
    exit();
}

// คำนวณ 2 ช่วงเวลาที่ผู้ใช้บันทึกบ่อยที่สุด
$sql_time_stats = 'SELECT time, COUNT(time) as count FROM "Record_Time" WHERE user_id = $1 GROUP BY time ORDER BY count DESC LIMIT 2';
$time_stats_result = pg_query_params($conn, $sql_time_stats, array($id));

$time_stats = [];
if ($time_stats_result) {
    while ($row = pg_fetch_assoc($time_stats_result)) {
        $time_stats[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            }        body {
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .navbar {
            background-color: #3A3D5F;
        }

        .navbar-brand, .nav-link {
            color: #FFFFFF !important;
        }

        .profile-img {
            border: 3px solid #fff;
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.1);
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
            background: #ffffff;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-weight: 600;
            color: #3A3D5F;
        }

        .list-group-item {
            border: none;
            background: #f9f9f9;
        }

        .table {
            animation: slideIn 0.8s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .navbar {
            background-color: #3A3D5F; /* โปร่งใส */
            backdrop-filter: blur(10px); /* เบลอ */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* เพิ่มเงาเล็กน้อย */
        }

        .profile-img {
            border: 2px solid white; /* กรอบรูป */
        }

        .navbar .nav-link {
            color: #fff; /* ปรับสีตัวอักษร */
        }

        .navbar .nav-link:hover {
            color: #ddd; /* เปลี่ยนสีเมื่อ hover */
        }
        
    </style>
</head>
<body>
    <div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TrackCarsApplication</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"></a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <img src="./uploads/profile/<?php echo htmlspecialchars($_SESSION['user_img']); ?>" class="rounded-circle profile-img me-2" width="50" height="50" alt="Profile Image">
                    <h5 class="text-light me-3"><?php echo htmlspecialchars($_SESSION['first_name'])." " . htmlspecialchars($_SESSION['last_name']); ?></h5>
                    <a class="btn btn-danger" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card p-4 mb-4">
            <center>
                <h1 class="card-title text-alight-center">ข้อมูลผู้ใช้</h1>
                <img src="./uploads/profile/<?php echo htmlspecialchars($_SESSION['user_img']); ?>" class="rounded-circle profile-img me-2 mt-3" width="120" height="120" alt="Profile Image">
            </center>
            <div class="card mt-3">
                <p class="mt-5 ms-5"><strong>ชื่อ:</strong> <?php echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']); ?></p>
                <p class="mt-2 ms-5" ><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p class="mt-2 ms-5"><strong>อายุ:</strong> <?php echo htmlspecialchars($_SESSION['age']); ?></p>
                <p class="mt-2 ms-5"><strong>ทะเบียนรถที่คุณลงทะเบียนไว้:</strong> <?php echo htmlspecialchars($_SESSION['car_registration']); ?></p>
                <img src="./uploads/car/<?php echo htmlspecialchars($_SESSION['car_registration_img']); ?>" class="rounded profile-img mt-2 ms-5" width="250" height="250" alt="Profile Image">
                <a class="btn btn-success mt-3 ms-5 me-5 mb-5" href="user_edit.php?id=<?php echo $_SESSION['user_id']; ?>" > Edit</a>
            </div>
        </div>

        <div class="card p-4 mb-4">
            <h3 class="card-title">Most Frequent Record Times</h3>
            <?php if (!empty($time_stats)) { ?>
                <ul class="list-group">
                    <?php foreach ($time_stats as $stat) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong>Time:</strong> <?php echo htmlspecialchars($stat['time']); ?></span>
                            <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($stat['count']); ?></span>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p>No record time statistics available.</p>
            <?php } ?>
        </div>

        <h4 class="text-center mt-4">บันทึกเวลาการเข้าออกของคุณ</h4>
        <table class="table table-hover table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Record ID</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($user)) {
                    foreach ($user as $row) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['record_id']) ?></td>
                            <td><?php echo htmlspecialchars($row['time']) ?></td>
                            <td><?php echo htmlspecialchars($row['date']) ?></td>
                        </tr>
                    <?php }
                } else {
                    echo "<tr><td colspan='3'>No records found.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
