<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: index.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>


<?php
$content = "<h1>Xin chào, đây là trang chủ!</h1>
<p>Chào mừng bạn đến với trang chủ của chúng tôi.</p>";

include('../includes/layout.php');
?>
