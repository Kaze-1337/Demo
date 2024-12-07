<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db_connect.php';

//lấy list đơn hàng
$stmt = $conn->prepare("SELECT * FROM orders");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <a href="manage_products.php">Quản lý sản phẩm</a> | <a href="../login.php">Đăng xuất</a>
    <h1>Danh sách đơn hàng</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Địa chỉ</th>
                <th>Phương thức thanh toán</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>                   
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= $order['payment_method'] === 'cash' ? 'Tiền mặt' : 'Online' ?></td>
                    <td><?= $order['total_price'] ?> VND</td>
                    <td><?= $order['status'] ?></td>
                    <td><?= $order['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
