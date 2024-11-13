<?php
// logout.php - ออกจากระบบ
session_start();
session_unset(); // ล้างตัวแปร session ทั้งหมด
session_destroy(); // ทำลาย session
header("Location: index.php");
exit();
?>
