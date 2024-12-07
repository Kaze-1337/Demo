<?php
session_start();
include '../includes/db_connect.php';

//kiểm tra quyền truy cập xem có phải admin không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

//kiểm tra xem form có được gửi không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = "../assets/images/" . $image;

    //kiểm tra các trường dữ liệu
    if (empty($name) || empty($price) || empty($quantity) || empty($image)) {
        echo "Tất cả các trường đều bắt buộc!";
    } else {
        //upload file ảnh
        if (move_uploaded_file($image_tmp, $image_path)) {
            //thêm sản phẩm vào database
            $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image) VALUES (:name, :price, :quantity, :image)");
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $image
            ]);

            echo "Sản phẩm đã được thêm thành công!";
        } else {
            echo "Lỗi khi tải ảnh lên!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h1>Thêm sản phẩm mới</h1>
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="price">Đơn giá:</label>
        <input type="number" name="price" id="price" required><br>

        <label for="quantity">Số lượng:</label>
        <input type="number" name="quantity" id="quantity" required><br>

        <label for="image">Hình ảnh minh họa:</label>
        <input type="file" name="image" id="image" required><br>

        <button type="submit">Xác nhận</button>
    </form>
    <a href="manage_products.php">Quay lại trang chủ danh sách</a>
</body>

</html>
