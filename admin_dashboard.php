<?php
session_start();
require_once 'db.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วและเป็นผู้ใช้ admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    $_SESSION['login_error'] = "You must be logged in as an admin to access this page.";
    exit();
}

$id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
$sql = 'SELECT * FROM "User"';
$result = pg_query($conn, $sql);

if ($result === false) {
    echo "Error in query execution.";
    exit();
}

$user = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .table-container {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle !important;
        }
        .btn-edit {
            background-color: #ffc107;
            border: none;
        }
        .btn-delete {
            background-color: #dc3545;
            border: none;
        }
        .btn-add {
            background-color: #28a745;
            border: none;
        }

    </style>
</head>
<body>
    <div>
        <div class="wave"></div>
        <div class="wave"></div>
        <div class="wave"></div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TrackCarsApplication</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"></a></li>
                    <li class="nav-item"><a class="nav-link" href="#"></a></li>
                    <li class="nav-item"><a class="nav-link disabled"></a></li>
                </ul>
                <span class="navbar-text me-3 text-white">
                    Welcome, ADMIN: <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                </span>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="table-container">
            <div class="d-flex justify-content-between mb-4">
                <h4>User Records</h4>
                <a href="add_user.php" class="btn btn-add text-white">
                    <i class="fas fa-plus"></i> เพิ่มข้อมูล
                </a>
            </div>
            <table class="table table-striped table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Car Registration</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($user)) {
                        foreach ($user as $row) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['car_registration']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-edit text-white">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                            <td>
                                <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="btn btn-delete text-white" onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>No records found.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>
