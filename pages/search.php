<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: search.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';


// Hàm xử lý thông báo
function setFlashMessage($message, $success) {
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}


if ($role == "Admin") {
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<div class="container-xxl">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <h1 class="display-5 mb-3">Tìm kiếm</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            ....table
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>
