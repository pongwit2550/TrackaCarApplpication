<?php
session_start();
require_once 'db.php'; // เรียกใช้งานการเชื่อมต่อจาก db.php

$user_id = $_GET['id'];
// ลบข้อมูลผู้ใช้จากฐานข้อมูล
$sql = 'DELETE FROM "User" WHERE user_id = $1';
$result = pg_query_params($conn, $sql, array($user_id));

if ($result) {
    // ถ้าลบสำเร็จ
    echo "ลบผู้ใช้ $user_id สำเร็จ!";
    header("Location: admin_dashboard.php"); // เปลี่ยนไปหน้าแสดงรายชื่อผู้ใช้ หรือหน้าอื่นๆ ที่ต้องการ
    exit();
} else {
    // ถ้าลบไม่สำเร็จ
    echo "ไม่สามารถลบผู้ใช้ได้ กรุณาลองใหม่";
}
?>
