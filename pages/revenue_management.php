<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: revenue_management.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

ob_start();

$role = $_SESSION["role"] ?? '';

?>

<?php if ($role == "Admin") : ?>
    <label for="inputType">Chọn loại thống kê:</label>
    <select name="inputType" id="inputType" class="form-control" onchange="showInputField(this.value)">
        <option value="byDate">Theo ngày</option>
        <option value="byMonth">Theo tháng</option>
        <option value="byYear">Theo năm</option>
    </select>
    <div id="divDate">
        <label for="inputDate">Ngày:</label>
        <input type="date" name="inputDate" id="inputDate" class="form-control">
    </div>
    <div id="divMonth" style="display:none;">
        <label for="inputMonth">Tháng:</label>
        <input type="month" name="inputMonth" id="inputMonth" class="form-control">
    </div>
    <div id="divYear" style="display:none;">
        <label for="inputYear">Năm:</label>
        <input type="number" name="inputYear" id="inputYear" min="1900" max="2099" class="form-control">
    </div>
    <button onclick="revenue_management()" type="submit" id="btnSubmit" class="btn btn-primary mt-2">Thống kê</button>
    <div id="result"></div>
<?php else : ?>
    <span>Bạn không đủ quyền để truy cập!</span>
<?php endif; ?>
<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>