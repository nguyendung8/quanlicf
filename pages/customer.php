<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: customer.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';

// Xử lý phân trang ajax
$num_per_page = 03;

if(isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}

$start_from = ($page - 1) * 03;

$sql = "SELECT * FROM customers LIMIT $start_from,$num_per_page";
$result = $conn->query($sql);
// Hàm xử lý thông báo
function setFlashMessage($message, $success)
{
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

// Hàm thêm mới khách hàng
function addCustomer($conn)
{
    if (isset($_POST['addCustomer'])) {
        $customer_firstname = $_POST['FirstName'];
        $customer_lastname = $_POST['LastName'];
        $customer_email = $_POST['Email'];
        $customer_phonenumber = $_POST['PhoneNumber'];

        $sql = "INSERT INTO `customers` (`FirstName`, `LastName`, `Email`, `PhoneNumber`) 
        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $customer_firstname, $customer_lastname, $customer_email, $customer_phonenumber);

        if ($stmt->execute()) {
            setFlashMessage("Thêm mới khách hàng thành công", true);
        } else {
            setFlashMessage("Thêm mới khách hàng không thành công", false);
        }

        $stmt->close();
    }
}

// Hàm cập nhật thông tin khách hàng
function updateCustomer($conn)
{
    if (isset($_POST['updateCustomer'])) {
        $customer_id = $_POST['CustomerID'];
        $customer_firstname = $_POST['FirstName'];
        $customer_lastname = $_POST['LastName'];
        $customer_email = $_POST['Email'];
        $customer_phonenumber = $_POST['PhoneNumber'];

        $sql = "UPDATE `customers` SET `FirstName`=?, `LastName`=?, `Email`=?, `PhoneNumber`=? WHERE CustomerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $customer_firstname, $customer_lastname, $customer_email, $customer_phonenumber, $customer_id);

        if ($stmt->execute()) {
            setFlashMessage("Cập nhật thông tin khách hàng thành công", true);
        } else {
            setFlashMessage("Cập nhật thông tin khách hàng không thành công", false);
        }

        $stmt->close();
    }
}
if ($role == "Admin") {
    // Gọi các hàm xử lý
    addCustomer($conn);
    updateCustomer($conn);
}

// Kiểm tra và gán dữ liệu vào biến $model
$model = [];
if ($result->num_rows > 0) {
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
                    <h1 class="display-5 mb-3">Quản lí khách hàng</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    <?php if ($role == "Admin") : ?>
                        <a class="btn btn-primary" onclick="openAdd()">Thêm mới</a>
                    <?php endif; ?>

                    <?php if (count($model) > 0) : ?>
                        <?php $stt = 1; ?>
                        <div class="table-responsive card mt-2">
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên khách hàng</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <?php if ($role == "Admin") : ?>
                                        <th></th>
                                    <?php endif; ?>
                                </tr>
                                <?php foreach ($model as $item) : ?>
                                    <tr>
                                        <td>
                                        <label style="width: auto"><?php echo $item['CustomerID']?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['FirstName'] . ' ' . $item['LastName']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['Email']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['PhoneNumber']; ?></label>
                                        </td>
                                        <?php if ($role == "Admin") : ?>
                                            <td>
                                                <a class="btn btn-primary" href="deletecustomer.php?id=<?php echo $item['CustomerID']; ?> ">Xóa</a>
                                                <a class="btn btn-success" onclick="sua(<?php echo $item['CustomerID']; ?>)">Sửa</a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="update<?php echo $item['CustomerID']; ?>" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="customerModalLabel">Chỉnh sửa thông tin khách hàng</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Họ</label>
                                                                        <input type="hidden" value="<?php echo $item['CustomerID']; ?>" name="CustomerID" />
                                                                        <input class="form-control" name="FirstName" value="<?php echo $item['FirstName']; ?>" placeholder="Nhập họ" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Tên</label>
                                                                        <input class="form-control" name="LastName" value="<?php echo $item['LastName']; ?>" placeholder="Nhập tên" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Email</label>
                                                                        <input class="form-control" name="Email" value="<?php echo $item['Email']; ?>" type="email" placeholder="Nhập email" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Số điện thoại</label>
                                                                        <input class="form-control" name="PhoneNumber" value="<?php echo $item['PhoneNumber']; ?>" type="tel" placeholder="Nhập số điện thoại" required />
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" onclick="closeUpdate(<?php echo $item['CustomerID']; ?>)" class="btn btn-secondary">Đóng</button>
                                                                    <button name="updateCustomer" type="submit" class="btn btn-success">Sửa</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                               <!-- Pagination -->
                               <?php
                                $sql = "SELECT * FROM customers";
                                $rs_result = $conn->query($sql);
                                $total_records = $rs_result->num_rows;
                                $total_pages = ceil($total_records/$num_per_page);

                                echo("<div class='pagination'>");
                                for($i =1;$i <=$total_pages;$i++) {
                                    echo("<a href='customer.php?page=".$i."'>".$i."</a>");
                                }
                                echo("</div>");
                                $conn->close();
                            ?>
                        </div>
                    <?php else : ?>
                        <p class="alert alert-danger">Danh sách khách hàng trống</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($role == "Admin") : ?>
    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customerModalLabel">Thêm mới khách hàng</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Họ</label>
                            <input class="form-control" name="FirstName" placeholder="Nhập họ" required />
                        </div>
                        <div class="form-group">
                            <label>Tên</label>
                            <input class="form-control" name="LastName" placeholder="Nhập tên" required />
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" name="Email" type="email" placeholder="Nhập email" required />
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input class="form-control" name="PhoneNumber" type="tel" placeholder="Nhập số điện thoại" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                        <button name="addCustomer" type="submit" class="btn btn-success">Thêm</button>
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
    <script>
        $(document).ready(function() {
            var page = 1;

            function loadData(page) {
                $.ajax({
                    url: 'customer.php?page=' + page,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hiển thị dữ liệu lên trang web
                    },
                    error: function() {
                    }
                });
            }

            loadData(page);

            $('#next').click(function() {
                page++;
                loadData(page);
            });

            $('#prev').click(function() {
                if (page > 1) {
                    page--;
                    loadData(page);
                }
            });
        });
    </script>
<style>
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 50px;
        width: 100%;
        /* border: 1px solid #168fff; */
        border-radius: 10px;
    }
    .pagination a {
        color: #fff;
        border: none;
        width: 29px;
        height: 29px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #168fff;
        border-radius: 6px;
    }
    .pagination a:hover {
        opacity: 0.7;
    }
</style>