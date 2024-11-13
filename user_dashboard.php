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
            background: linear-gradient(135deg, #FFDEE9 0%, #B5FFFC 100%);
            font-family: 'Poppins', sans-serif;
            animation: fadeIn 1s ease-in-out;
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TrackCarsApplication</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
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
            <h3 class="card-title">ข้อมูลผู้ใช้</h3>
            <img src="./uploads/profile/<?php echo htmlspecialchars($_SESSION['user_img']); ?>" class="rounded-circle profile-img me-2" width="120" height="120" alt="Profile Image">
            <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>อายุ:</strong> <?php echo htmlspecialchars($_SESSION['age']); ?></p>
            <p><strong>ทะเบียนรถที่คุณลงทะเบียนไว้:</strong> <?php echo htmlspecialchars($_SESSION['car_registration']); ?></p>
            <img src="./uploads/profile/<?php echo htmlspecialchars($_SESSION['car_registration']); ?>" class="rounded profile-img " width="120" height="120" alt="Profile Image">

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
