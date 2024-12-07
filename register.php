<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //lấy dữ liệu từ form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; 

    //kiểm tra các trường bắt buộc
    if (empty($username) || empty($password) || empty($role)) {
        echo "Vui lòng điền đầy đủ thông tin!";
    } else {
        //kiểm tra nếu tên đăng nhập đã tồn tại
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo "Tên đăng nhập đã tồn tại!";
        } else {
            //mã hóa mật khẩu trước khi lưu vào database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            //thêm người dùng mới vào database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'role' => $role
            ]);

            echo "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
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

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box; 
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

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <form method="POST" action="register.php">
        <h1>Đăng ký tài khoản</h1>

        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <div class="form-group">
            <label for="role">Chọn vai trò:</label>
            <select name="role" id="role" required>
                <option value="customer">Khách hàng</option>
                <option value="admin">Quản trị viên</option>
            </select>
        </div>

        <button type="submit">Đăng ký</button>
        
        <p class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>
        </p>
    </form>
</body>
</html>
