<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/db_connect.php';

$product = null; // 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    //truy vấn sản phẩm theo id
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    //hiển thị thông báo và dừng script nếu không có sản phẩm
    if (!$product) {
        echo "<p>Sản phẩm không tồn tại. <a href='manage_products.php'>Quay lại danh sách sản phẩm</a></p>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']); 

    //kiểm tra hợp lệ tất cả các trường dữ liệu
    if (!empty($name) && !empty($price) && isset($quantity)) {
        try {
            //cập nhật thông tin mới cho sản phẩm
            $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'id' => $id
            ]);
            echo "<p>Chỉnh sửa sản phẩm thành công!</p>";
        } catch (PDOException $e) {
            echo "<p>Đã xảy ra lỗi: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Vui lòng điền đầy đủ thông tin.</p>";
    }
}
?>

<link rel="stylesheet" href="../assets/css/styles.css">

<h1>Chỉnh sửa sản phẩm</h1>

<?php if ($product): ?>
    <form method="POST" action="edit_product.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">       
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required><br>
        <label for="quantity">Số lượng:</label>
        <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required><br> 
        <button type="submit">Cập nhật</button>
    </form>
<?php endif; ?>

<a href="manage_products.php">Quay lại trang chủ danh sách</a>
