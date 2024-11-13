<?php
// db.php - เก็บค่าการเชื่อมต่อฐานข้อมูล
$host = 'aws-0-ap-southeast-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.reedfppffweswquzbeet';
$password = 'TrackCars2024';

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>
