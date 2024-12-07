<?php
session_start();
include '../includes/db_connect.php';

//kiểm tra xem người dùng đã đăng nhập tài khoản role customer hay chưa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

//lấy danh sách sản phẩm còn hàng từ database
$stmt = $conn->prepare("SELECT * FROM products WHERE quantity > 0");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

//xử lý giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) 
{
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    //lưu thông tin sản phẩm vào giỏ hàng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    //kiểm tra nếu sản phẩm đã có trong giỏ, cập nhật số lượng
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    
</head>
<body>
<a href="cart.php">Xem giỏ hàng</a>|<a href='../login.php'>Đăng xuất</a>
    <h1>Cửa hàng</h1>
    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="150">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p>Giá: <?= htmlspecialchars($product['price']) ?> VND</p>
                <p>Số lượng còn lại: <?= $product['quantity'] ?></p>

                <form method="POST" action="shop.php">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <label for="quantity">Số lượng:</label>
                    <input type="number" name="quantity" min="1" max="<?= $product['quantity'] ?>" value="1" required>
                    <button style="background-color: orange; " type="submit">Thêm vào đơn hàng</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
