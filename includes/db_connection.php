<?php
// Thông tin kết nối đến cơ sở dữ liệu
$servername = "localhost";   // Tên máy chủ MySQL
$username = "root"; // Tên người dùng MySQL
$password = "123456"; // Mật khẩu MySQL
$database = "quanlicf"; // Tên cơ sở dữ liệu

// Tạo kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến CSDL thất bại: " . $conn->connect_error);
}

// Đặt mã ký tự kết nối thành UTF-8 (tuỳ chọn)
$conn->set_charset("utf8");

?>
