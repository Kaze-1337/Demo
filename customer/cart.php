<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

//kiểm tra giỏ hàng
if (empty($_SESSION['cart'])) {
    echo "<p>Giỏ hàng của bạn trống. <a href='shop.php'>Quay lại cửa hàng</a></p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]); //xóa sản phẩm nếu hết hàng
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script>
        function updateCart(productId) {
            const quantityInput = document.getElementById('quantity_' + productId);
            const quantity = quantityInput.value;

            //gửi AJAX để cập nhật giá và số lượng
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('cart-total').innerHTML = xhr.responseText;
                }
            };
            xhr.send('product_id=' + productId + '&quantity=' + quantity);
        }
    </script>
</head>
<body>
    <h1>Giỏ hàng của bạn</h1>
    <form method="POST" action="cart.php">
        <table border="1">
            <thead>
                <tr >
                    <th style="background-color: orange; ">Tên sản phẩm</th>
                    <th style="background-color: orange; ">Giá</th>
                    <th style="background-color: orange; ">Số lượng</th>
                    <th style="background-color: orange; ">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
                    $stmt->execute(['id' => $product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    $subtotal = $product['price'] * $quantity;
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?> VND</td>
                        <td>
                            <input 
                                type="number" 
                                id="quantity_<?= $product_id ?>" 
                                name="quantities[<?= $product_id ?>]" 
                                value="<?= $quantity ?>" 
                                min="1" 
                                max="<?= $product['quantity'] ?>" 
                                onchange="updateCart(<?= $product_id ?>)">
                        </td>
                        <td id="subtotal_<?= $product_id ?>"><?= $subtotal ?> VND</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p>Tổng cộng: <span id="cart-total"><?= $total ?> VND</span></p>
        <button style="background-color: orange; " type="submit" name="update_cart">Cập nhật giỏ hàng</button>
        
        <a href="shop.php" class="btn">Tiếp tục mua hàng</a>
        <br><br>
        <a href="checkout.php" class="btn">Thanh toán</a>
    </form>
</body>
</html>
