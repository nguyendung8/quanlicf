<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: employee.php';

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

$sqlE = "SELECT roles.*, employees.* FROM employees
              INNER JOIN roles ON roles.RoleID = employees.RoleID
              LIMIT $start_from,$num_per_page";
$resultE = $conn->query($sqlE);
// Hàm xử lý thông báo
function setFlashMessage($message, $success) {
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

// Hàm thêm mới nhân viên
function addEmployee($conn) {
    if (isset($_POST['addEmployee'])) {
        $employee_username = $_POST['UserName'];
        $employee_password = $_POST['Password'];
        $employee_firstname = $_POST['FirstName'];
        $employee_lastname = $_POST['LastName'];
        $employee_email = $_POST['Email'];
        $employee_phonenumber = $_POST['PhoneNumber'];
        $employee_roleid = $_POST['RoleID'];

        $hashed_password = password_hash($employee_password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `employees` (`FirstName`, `LastName`, `Email`, `PhoneNumber`, `UserName`, `Password`, `RoleID`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $employee_firstname, $employee_lastname, $employee_email, $employee_phonenumber, $employee_username, $hashed_password, $employee_roleid);

        if ($stmt->execute()) {
            setFlashMessage("Thêm mới nhân viên thành công", true);
        } else {
            setFlashMessage("Thêm mới nhân viên không thành công", false);
        }

        $stmt->close();
    }
}

// Hàm cập nhật thông tin nhân viên
function updateEmployee($conn) {
    if (isset($_POST['updateEmployee'])) {
        $employee_id = $_POST['EmployeeID'];
        $employee_firstname = $_POST['FirstName'];
        $employee_lastname = $_POST['LastName'];
        $employee_email = $_POST['Email'];
        $employee_phonenumber = $_POST['PhoneNumber'];
        $employee_username = $_POST['UserName'];
        $employee_roleid = $_POST['RoleID'];

        $sql = "UPDATE `employees` SET `FirstName`=?, `LastName`=?, `Email`=?, `PhoneNumber`=?, `UserName`=?, `RoleID`=? WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $employee_firstname, $employee_lastname, $employee_email, $employee_phonenumber, $employee_username, $employee_roleid, $employee_id);

        if ($stmt->execute()) {
            setFlashMessage("Cập nhật thông tin nhân viên thành công", true);
        } else {
            setFlashMessage("Cập nhật thông tin nhân viên không thành công", false);
        }

        $stmt->close();
    }
}

if ($role == "Admin") {
    // Gọi các hàm xử lý
    addEmployee($conn);
    updateEmployee($conn);
}

$sqlR = "SELECT * FROM roles";
$resultR = $conn->query($sqlR);
// Kiểm tra và gán dữ liệu vào biến $Model
$Model = [];
if ($resultE->num_rows > 0) {
    while ($row = $resultE->fetch_assoc()) {
        $Model[] = $row;
    }
}
$roles = [];
if ($resultR->num_rows > 0) {
    while ($row = $resultR->fetch_assoc()) {
        $roles[] = $row;
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
                    <h1 class="display-5 mb-3">Quản lí nhân viên</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    <?php if ($role == "Admin") : ?>
                        <a class="btn btn-primary" onclick="openAdd()">Thêm mới</a>
                    <?php endif; ?>

                    <?php if (count($Model) > 0) : ?>
                        <?php $stt = 1; ?>
                        <div class="table-responsive card mt-2">
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên nhân viên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Chức vụ</th>
                                    <?php if ($role == "Admin") : ?>
                                        <th></th>
                                    <?php endif; ?>
                                </tr>
                                <?php foreach ($Model as $item) : ?>
                                    <tr>
                                        <td>
                                            <label style="width: auto"><?php echo $item['EmployeeID']?></label>
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
                                        <td>
                                            <label style="width: auto"><?php echo $item['UserName']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['RoleName']; ?></label>
                                        </td>
                                        <?php if ($role == "Admin") : ?>
                                            <td>
                                                <a class="btn btn-primary deleteButton" href="deleteemployee.php?id=<?php echo $item['EmployeeID']?> ">Xóa</a>
                                                <a class="btn btn-success" onclick="sua(<?php echo $item['EmployeeID']; ?>)">Sửa</a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="update<?php echo $item['EmployeeID']; ?>" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="employeeModalLabel">Chỉnh sửa thông tin nhân viên</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Họ</label>
                                                                        <input type="hidden" value="<?php echo $item['EmployeeID']; ?>" name="EmployeeID" />
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
                                                                    <div class="form-group">
                                                                        <label>Tên đăng nhập</label>
                                                                        <input class="form-control" name="UserName" value="<?php echo $item['UserName']; ?>" type="text" placeholder="Nhập tên đăng nhập" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Chức vụ</label>
                                                                        <select class="form-control" name="RoleID" required>
                                                                            <?php foreach ($roles as $item1) : ?>
                                                                                <option value="<?php echo $item1['RoleID']; ?>" <?php echo ($item1['RoleID'] == $item['RoleID']) ? 'selected="selected"' : ''; ?>><?php echo $item1['RoleName']; ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" onclick="closeUpdate(<?php echo $item['EmployeeID']; ?>)" class="btn btn-secondary">Đóng</button>
                                                                    <button name="updateEmployee" type="submit" class="btn btn-success">Sửa</button>
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
                                $sql = "SELECT * FROM employees";
                                $rs_result = $conn->query($sql);
                                $total_records = $rs_result->num_rows;
                                $total_pages = ceil($total_records/$num_per_page);

                                echo("<div class='pagination'>");
                                for($i =1;$i <=$total_pages;$i++) {
                                    echo("<a href='employee.php?page=".$i."'>".$i."</a>");
                                }
                                echo("</div>");
                                $conn->close();
                            ?>
                        </div>
                        </div>
                    <?php else : ?>
                        <p class="alert alert-danger">Danh sách nhân viên trống</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($role == "Admin") : ?>
    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeModalLabel">Thêm mới nhân viên</h5>
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
                        <div class="form-group">
                            <label>Tên đăng nhập</label>
                            <input class="form-control" name="UserName" type="text" placeholder="Nhập tên đăng nhập" required />
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu</label>
                            <input class="form-control" name="Password" minlength="8" type="password" placeholder="Nhập mật khẩu" required />
                        </div>
                        <div class="form-group">
                            <label>Chức vụ</label>
                            <select class="form-control" name="RoleID">
                                <?php foreach ($roles as $item1) : ?>
                                    <option value="<?php echo $item1['RoleID']; ?>"><?php echo $item1['RoleName']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                        <button name="addEmployee" type="submit" class="btn btn-success">Thêm</button>
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
                    url: 'employee.php?page=' + page,
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

            // Xử lý phân trang khi người dùng nhấn nút "Next" hoặc "Previous"
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