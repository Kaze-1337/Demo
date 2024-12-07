<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu thông tin đăng nhập không rỗng
    if (empty($username) || empty($password)) {
        echo "<p style='color: red;'>Vui lòng điền đầy đủ thông tin!</p>";
    } else {
        try {
            // Truy vấn để lấy thông tin người dùng từ cơ sở dữ liệu
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công, lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // 'admin' hoặc 'customer'

                // Điều hướng đến trang phù hợp dựa trên vai trò
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else if ($user['role'] === 'customer') {
                    header("Location: customer/shop.php");
                }
                exit();
            } else {
                echo "<p style='color: red;'>Tên đăng nhập hoặc mật khẩu không chính xác!</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 40%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%; /* Chiếm 100% chiều rộng của form */
            box-sizing: border-box; /* Đảm bảo padding không làm thay đổi chiều rộng */
        }

        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #FF5722;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #FF3D00;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h1>Đăng nhập</h1>

        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <button type="submit">Đăng nhập</button>
        
        <p class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký</a>
        </p>
    </form>
</body>
</html>
