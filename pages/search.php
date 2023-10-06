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


// Hàm xử lý thông báo
function setFlashMessage($message, $success) {
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

// xử lý tìm kiếm
if(isset($_POST["search"])) {
    $s = $_POST["txtsearch"];
    if($s == "") {
        echo("Vui lòng nhập vào ô tìm kiếm");
    } else {
        $sql = "SELECT customers.CustomerID,customers.FirstName, customers.LastName, orders.OrderID, products.ProductName, orderdetails.TotalPrice
        FROM customers
        INNER JOIN orders ON customers.CustomerID = orders.CustomerID
        INNER JOIN orderdetails ON orders.OrderID = orderdetails.OrderID
        INNER JOIN products ON orderdetails.ProductID = products.ProductID
        WHERE LastName LIKE '%$s%'";

        $result = $conn->query($sql);
    }
}
$model = [];
if (!empty($result) && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $model[] = $row;
    }
}

// Đóng kết nối cơ sở dữ liệu
// $conn->close();
?>

<div class="container-xxl">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <h1 class="display-5 mb-3">Tìm kiếm</h1>
                    <p>Bạn có thể thỏa sức tìm kiếm thứ gì mà bạn muốn!</p>
                </div>
            </div>
            <div id="search">
                <form action="" method="post">
                    <input type="text" name="txtsearch"></input>
                    <input type="submit" value="Tìm" name="search"></input>
                </form>
            </div>
        </div>
        <div class="table-responsive card mt-2">
        <?php if (count($model) > 0): ?>
            <table class="table table-hover">
                <tr>
                    <th>Mã Khách Hàng</th>
                    <th>Tên khách hàng</th>
                    <th>Mã hóa đơn</th>
                    <th>Tên sản phẩm</th>
                    <th>Tổng tiền</th>
                </tr>
                <?php foreach ($model as $item): ?>
                    <tr>
                        <td>
                            <label style="width: auto"><?php echo $item['CustomerID']?></label>
                        </td>
                        <td>
                            <label style="width: auto"><?php echo $item['LastName']; ?></label>
                        </td>
                        <td>
                            <label style="width: auto"><?php echo $item['OrderID']; ?></label>
                        </td>
                        <td>
                            <label style="width: auto"><?php echo $item['ProductName']; ?></label>
                        </td>
                        <td>
                            <label style="width: auto"><?php echo number_format($item['TotalPrice'], 0,',','.') . ' đồng'; ?></label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="alert alert-danger">Danh sách trống</p>
        <?php endif; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>
<style>
input[type="text"] {
    min-width: 536px;
    border-radius: 5px;
    padding: 7px
}
input[type="submit"] {
    padding: 7px;
    margin-left: 10px;
    border-radius: 6px;
    width: 102px;
    cursor: pointer;
}
input[type="submit"]:hover {
    opacity: 0.8;
}
</style>
