<?php
function checkLogin($role = null) {
    session_start();
    if (!isset($_SESSION['user'])) {
        header("Location: /login.php");
        exit();
    }

    if ($role && $_SESSION['user']['role'] !== $role) {
        header("Location: /index.php");
        exit();
    }
}
?>
