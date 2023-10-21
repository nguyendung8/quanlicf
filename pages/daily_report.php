<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: daily_report.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';

// Hàm xử lý thông báo
function setFlashMessage($message, $success)
{
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

$models = [];

if ($role != "Admin") {
    $employeeId = (int)$_SESSION['employeeId'];
    $query = "SELECT employees.*, dailyreports.* FROM employees
              INNER JOIN dailyreports ON employees.EmployeeID = dailyreports.EmployeeID
              WHERE employees.EmployeeID = $employeeId";
    $result = $conn->query($query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $models[] = $row;
        }
    }
} else {
    $query = "SELECT employees.*, dailyreports.* FROM employees
              INNER JOIN dailyreports ON employees.EmployeeID = dailyreports.EmployeeID";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $models[] = $row;
        }
    }
}
if (isset($_POST['addDailyReport'])) {
    $employeeId = $_SESSION['employeeId'];
    $reportDateTime = date("Y-m-d H:i:s");
    $reportDate = date("Y-m-d", strtotime($reportDateTime));

    // Kiểm tra xem báo cáo đã tồn tại cho ngày và nhân viên tương ứng hay chưa
    $sqlCheckReport = "SELECT * FROM dailyreports WHERE EmployeeID = $employeeId AND DATE(ReportDate) = '$reportDate'";
    $resultCheckReport = $conn->query($sqlCheckReport);

    if ($resultCheckReport->num_rows > 0) {
        setFlashMessage("Báo cáo cuối ngày cho nhân viên và ngày báo cáo đã tồn tại.", false);
    }

    // Truy vấn dữ liệu các đơn hàng của nhân viên cho ngày tương ứng
    $sqlOrders = "SELECT * FROM orders WHERE EmployeeID = $employeeId AND DATE(OrderDate) = '$reportDate'";
    $resultOrders = $conn->query($sqlOrders);

    $totalOrders = $resultOrders->num_rows;

    // Truy vấn dữ liệu chi tiết đơn hàng và tính tổng doanh thu
    $sqlOrderDetails = "SELECT SUM(TotalPrice) AS TotalRevenue FROM orderdetails WHERE OrderID IN (SELECT OrderID FROM orders WHERE EmployeeID = $employeeId AND DATE(OrderDate) = '$reportDate')";
    $resultOrderDetails = $conn->query($sqlOrderDetails);
    $rowOrderDetails = $resultOrderDetails->fetch_assoc();
    $totalRevenue = $rowOrderDetails['TotalRevenue']; /// null here

    // Truy vấn dữ liệu số lượng khách hàng
    $sqlCustomers = "SELECT COUNT(DISTINCT CustomerID) AS TotalCustomers FROM orders WHERE EmployeeID = $employeeId AND DATE(OrderDate) = '$reportDate'";
    $resultCustomers = $conn->query($sqlCustomers);
    $rowCustomers = $resultCustomers->fetch_assoc();
    $totalCustomers = $rowCustomers['TotalCustomers'];

    $report_SpecialEvents = $_POST['SpecialEvents'] ?? '';
    $report_GeneralInfo = $_POST['GeneralInfo'] ?? '';
    $report_Improvements = $_POST['Improvements'] ?? '';

    // Thêm mới báo cáo cuối ngày
    $sqlInsertReport = "INSERT INTO dailyreports (EmployeeID, ReportDate, TotalOrders, TotalRevenue, 
    TotalCustomers, SpecialEvents, GeneraInfo, Improvements) VALUES ('$employeeId', '$reportDateTime', 
    '$totalOrders', '$totalRevenue', '$totalCustomers', '$report_SpecialEvents', '$report_GeneralInfo', 
    '$report_Improvements')";
    $resultInsertReport = $conn->query($sqlInsertReport);

    if ($resultInsertReport) {
        setFlashMessage("Thêm mới báo cáo cuối ngày thành công.", true);
        exit();
    } else {
        setFlashMessage("Lỗi thêm mới báo cáo cuối ngày: " . $conn->error, false);
    }
}
// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<div class="container-xxl">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <h1 class="display-5 mb-3">Báo cáo cuối ngày</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    <?php if ($role == "Admin") : ?>
                        <a class="btn btn-primary" onclick="openAdd()">Thêm mới</a>
                    <?php endif; ?>

                    <?php if (count($models) > 0) : ?>
                        <?php $stt = 1; ?>
                        <div class="table-responsive card mt-2">
                            <table class="table table-hover">
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên nhân viên</th>
                                    <th>Ngày báo cáo</th>
                                    <th>Số lượng hóa đơn</th>
                                    <th>Tổng doanh thu</th>
                                    <th>Số lượng khách hàng</th>
                                </tr>
                                <?php foreach ($models as $item) : ?>
                                    <tr>
                                        <td><?php echo $stt++; ?></td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['FirstName'] . ' ' . $item['LastName']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['ReportDate']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['TotalOrders']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo number_format($item['TotalRevenue'], 0,',','.') . ' đồng'; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['TotalCustomers']; ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="alert alert-danger">Danh sách báo cáo cuối ngày trống</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($role == "Admin") : ?>
    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="dailyreportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dailyreportModalLabel">Thêm mới báo cáo cuối ngày</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Các sự kiện trong ngày</label>
                            <textarea class="form-control" name="SpecialEvents" placeholder="Các sự kiện trong ngày"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Thông tin chung và yêu cầu hỗ trợ</label>
                            <textarea class="form-control" name="GeneralInfo" placeholder="Thông tin chung và yêu cầu hỗ trợ"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Đề xuất cải tiến</label>
                            <textarea class="form-control" name="Improvements" placeholder="Đề xuất cải tiến"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                        <button name="addDailyReport" type="submit" class="btn btn-success">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>