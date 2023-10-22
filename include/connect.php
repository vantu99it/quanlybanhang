<?php
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    $now_day = date('Y-m-d H:i:s');

    if(!isset($_SESSION)){ 
        session_start();
    }
    $servername = "localhost";
    $username = "root";
    $password = "";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=quanlybanhang1", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $timezone = "+07:00"; // Múi giờ Hà Nội, Việt Nam
        $sql = "SET time_zone = '$timezone'";
        $conn->exec($sql);
        // echo "Kết nối thành công";
    } catch(PDOException $e) {
        // echo "Kết nối thất bại: " . $e->getMessage();
    }

?>