<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: statistical_management.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';

$sqlCus = "SELECT 
            c.CustomerID,
            c.FirstName,
            c.LastName,
            COUNT(o.OrderID) AS NumberOfPurchases,
            SUM(od.TotalPrice) AS TotalSpending,
            (SELECT SUM(od2.TotalPrice) 
             FROM orderdetails od2 
             WHERE od2.OrderID IN (SELECT o2.OrderID FROM orders o2 WHERE o2.CustomerID = c.CustomerID)) AS TotalRevenue
        FROM customers c
        LEFT JOIN orders o ON c.CustomerID = o.CustomerID
        LEFT JOIN orderdetails od ON o.OrderID = od.OrderID
        GROUP BY c.CustomerID, c.FirstName, c.LastName";
$resultCus = $conn->query($sqlCus);

// Kiểm tra và gán dữ liệu vào biến $model
$modelCus = [];
if ($resultCus->num_rows > 0) {
    while ($row = $resultCus->fetch_assoc()) {
        $modelCus[] = $row;
    }
}

$sqlPro = "SELECT 
            p.ProductID,
            p.ProductName,
            SUM(od.Quantity) AS TotalQuantitySold,
            SUM(od.TotalPrice) AS TotalRevenue
        FROM products p
        LEFT JOIN orderdetails od ON p.ProductID = od.ProductID
        GROUP BY p.ProductID, p.ProductName";

$resultPro = $conn->query($sqlPro);

// Kiểm tra và gán dữ liệu vào biến $model
$modelPro = [];
if ($resultPro->num_rows > 0) {
    while ($row = $resultPro->fetch_assoc()) {
        $modelPro[] = $row;
    }
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<?php if ($role == "Admin") : ?>
    <div class="container">
        <h2>Thống kê doanh thu, số lần mua và chi tiêu của khách hàng</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã khách hàng</th>
                    <th>Họ tên</th>
                    <th>Số lần mua hàng</th>
                    <th>Tổng chi tiêu</th>
                    <th>Tổng doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultCus as $item) : ?>
                    <tr>
                        <td><?= $item['CustomerID'] ?></td>
                        <td><?= $item['FirstName'] . ' ' . $item['LastName'] ?></td>
                        <td><?= $item['NumberOfPurchases'] ?></td>
                        <td><?= isset($item['TotalSpending']) ? $item['TotalSpending'] : 'N/A' ?></td>
                        <td><?= isset($item['TotalRevenue']) ? number_format($item['TotalRevenue'], 0, ',', '.') . ' đồng' : 'N/A' ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Thống kê sản phẩm đã bán -->
    <div class="container">
        <h2>Thống kê sản phẩm đã bán</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Tổng số lượng đã bán</th>
                    <th>Tổng doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modelPro as $item) : ?>
                    <tr>
                        <td><?= $item['ProductID'] ?></td>
                        <td><?= $item['ProductName'] ?></td>
                        <td><?= isset($item['TotalQuantitySold']) ? $item['TotalQuantitySold'] : 'N/A' ?></td>
                        <td><?= isset($item['TotalRevenue']) ? number_format($item['TotalRevenue'], 0, ',', '.') . ' đồng' : 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <span>Bạn không đủ quyền để truy cập!</span>
<?php endif; ?>
<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>