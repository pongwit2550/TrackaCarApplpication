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
            background: rgb(238,174,202);
            background: radial-gradient(circle, rgba(238,174,202,1) 0%, rgba(148,187,233,1) 100%);
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TrackCarsApplication</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Link</a></li>
                    <li class="nav-item"><a class="nav-link disabled">Disabled</a></li>
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
