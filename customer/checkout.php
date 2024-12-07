<?php
session_start();
include '../includes/db_connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra nếu giỏ hàng trống
if (empty($_SESSION['cart'])) {
    echo "<p>Giỏ hàng của bạn trống. <a href='shop.php'>Quay lại cửa hàng</a></p>";
    exit();
}

// Tính tổng giá trị đơn hàng
$total_price = 0;
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_price += $product['price'] * $quantity;
}

// Xử lý khi người dùng bấm nút "Xác nhận thanh toán"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];

    // Chèn thông tin đơn hàng vào bảng `orders`
    $stmt = $conn->prepare("
        INSERT INTO orders (customer_id, total_price, created_at) 
        VALUES (:customer_id, :total_price, NOW())
    ");
    $stmt->execute([
        'customer_id' => $_SESSION['user_id'], // Sử dụng `customer_id` thay vì `user_id`
        'total_price' => $total_price,
    ]);

    // Lấy ID của đơn hàng vừa tạo
    $order_id = $conn->lastInsertId();

    // Thêm từng sản phẩm vào bảng `order_items`
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");
        $stmt->execute([
            'order_id' => $order_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $product['price'], // Giá của từng sản phẩm
        ]);
    }

    // Cập nhật số lượng sản phẩm trong kho
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity - :quantity WHERE id = :id");
        $stmt->execute([
            'quantity' => $quantity,
            'id' => $product_id,
        ]);
    }

    // Xóa giỏ hàng sau khi thanh toán
    unset($_SESSION['cart']);

    // Hiển thị thông báo thành công
    echo "<p>Thanh toán thành công! Cảm ơn bạn đã mua hàng.</p>";
    echo '<a href="shop.php" class="btn">Quay về trang chủ cửa hàng</a>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #555;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            resize: none;
            height: 100px;
            margin-bottom: 15px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            text-align: center;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <form method="POST" action="checkout.php">
        <h1>Thông tin thanh toán</h1>
        <label for="address">Địa chỉ giao hàng:</label>
        <textarea id="address" name="address" placeholder="Nhập địa chỉ giao hàng của bạn..." required></textarea>

        <label for="payment_method">Phương thức thanh toán:</label>
        <select id="payment_method" name="payment_method" required>
            <option value="online">Thanh toán online</option>
            <option value="cash">Thanh toán khi nhận hàng</option>
        </select>

        <p>Tổng giá trị: <b><?= number_format($total_price, 2) ?> VND</b></p>

        <button type="submit">Xác nhận thanh toán</button>
        <a href="cart.php" class="btn-secondary">Quay lại giỏ hàng</a>
    </form>
</body>
</html>
