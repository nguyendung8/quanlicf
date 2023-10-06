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
$content = "<h1>Chào mừng bạn đến với quán của chúng tôi!</h1>
<p style='font-size: 30px;'>Mỗi hạt cà phê là một câu chuyện về đam mê</p>
<div>
<img class='bg-img' src='../uploads/bg.jpg'>
</div>
<div style='margin-top: 10px; display: flex; gap: 20px;'>
    <p style='font-size: 25px;'>Mời bạn order đồ uống</p>
    <button class='order-btn'><a style='color: #fff;' href='./product.php'>Order</a></button>
</div>
<p style='font-size: 20px;'>Chúc quý khách ngon miệng !!!</p>
";

include('../includes/layout.php');
?>
<style>
.bg-img {
    width: 787px;
    border-radius: 13px;
}
.order-btn {
    background-color: #cfab70;
    border: none;
    border-radius: 8px;
    width: 60px;
    cursor: pointer;
    height: 44px;
}
.order-btn:hover {
    opacity: 0.8;
}
</style>
