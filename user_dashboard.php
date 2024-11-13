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
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(148,187,233,1) 100%);
            font-family: 'Arial', sans-serif;
        }
        .navbar-brand, .nav-link {
            color: #000 !important;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .card {
            margin-top: 20px;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="#">TrackCarsApplication</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Link</a></li>
                </ul>
                <img src="<?php echo htmlspecialchars($user[0]['user_img']); ?>" class="rounded-circle" alt="Profile Image" width="40" height="40">
                <h2 class="me-3"><?php echo htmlspecialchars($_SESSION['first_name']); ?></h2>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- ข้อมูลส่วนตัวของผู้ใช้ -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">ข้อมูลผู้ใช้</h3>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['first_name']) . " " . htmlspecialchars($_SESSION['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>Profile Image:</strong> <img src="./uploads/profile/<?php echo htmlspecialchars($user[0]['user_img']); ?>" class="rounded-circle" width="80" height="80" alt="User Image"></p>
            </div>
        </div>

        <!-- แสดงสถิติช่วงเวลาที่บันทึกบ่อยที่สุด -->
        <div class="card mt-4">
            <div class="card-body">
                <h3 class="card-title">Most Frequent Record Times</h3>
                <?php if (!empty($time_stats)) { ?>
                    <ul class="list-group">
                        <?php foreach ($time_stats as $stat) { ?>
                            <li class="list-group-item">
                                <strong>Time:</strong> <?php echo htmlspecialchars($stat['time']); ?>
                                <br>
                                <strong>Count:</strong> <?php echo htmlspecialchars($stat['count']); ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>No record time statistics available.</p>
                <?php } ?>
            </div>
        </div>

        <!-- แสดงตารางการบันทึกเวลา -->
        <h4 class="text-center mt-4">บันทึกเวลาการเข้าออกของคุณ</h4>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($user)) {
                    foreach ($user as $row) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['record_id']) ?></td>
                            <td><?php echo htmlspecialchars($row['time']) ?> </td>
                            <td><?php echo htmlspecialchars($row['date']) ?></td>
                        </tr>
                 <?php   }
                } else {
                    echo "<tr><td colspan='3'>No records found.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
