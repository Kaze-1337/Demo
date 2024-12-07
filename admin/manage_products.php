<?php
session_start();
include '../includes/db_connect.php';

// Kiểm tra quyền truy cập, nếu không phải admin thì không được vào trang này
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<a href="../login.php">Đăng xuất</a> | <a href="manage_orders.php">Quản lí đơn hàng</a>
    <h1>Danh sách sản phẩm</h1>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Đơn giá (VND)</th>
                <th>Số lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 0, ',', '.') ?> VND</td>
                    <td><?= $product['quantity'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>">Chỉnh sửa</a> |
                        <a href="delete_product.php?id=<?= $product['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="add_product.php">Thêm sản phẩm mới</a></p>
</body>
</html>
