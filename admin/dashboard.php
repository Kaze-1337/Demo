<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ admin</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h1>Trang chủ admin</h1>
    <a href="manage_products.php">Quản lý sản phẩm</a> | <a href="manage_orders.php">Quản lý đơn hàng</a> | <a href="../login.php">Đăng xuất</a>
</body>
</html>
