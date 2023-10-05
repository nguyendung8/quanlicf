<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: profile.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: profile.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';

$employeeId = $_SESSION['employeeId'] ?? "";

// Hàm xử lý thông báo
function setFlashMessage($message, $success)
{
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

$sql = "SELECT employees.*, roles.RoleName  FROM employees 
INNER JOIN roles ON employees.RoleID = roles.RoleID
 WHERE employees.EmployeeID = $employeeId";
$result = $conn->query($sql);

if (isset($_POST['updateProfile'])) {
    $employee_id = $_POST['EmployeeID'];
    $employee_firstname = $_POST['FirstName'];
    $employee_lastname = $_POST['LastName'];
    $employee_email = $_POST['Email'];
    $employee_phonenumber = $_POST['PhoneNumber'];
    $new_password = $_POST['Password']; 

    $sql = "UPDATE `employees` SET `FirstName`=?, `LastName`=?, `Email`=?, `PhoneNumber`=?";
    $bind_types = "sssi";

    // Kiểm tra xem người dùng có nhập mật khẩu mới không
    if (!empty($new_password)) {
        $sql .= ", `Password`=?";
        $bind_types .= "s";
    }

    $sql .= " WHERE EmployeeID = ?";
    $bind_types .= "i";

    $stmt = $conn->prepare($sql);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Nếu người dùng đã nhập mật khẩu mới, thực hiện bind thêm giá trị NewPassword vào câu lệnh SQL
        $stmt->bind_param($bind_types, $employee_firstname, $employee_lastname, $employee_email, $employee_phonenumber, $hashed_password, $employee_id);
    } else {
        // Nếu không có mật khẩu mới, không cần bind giá trị NewPassword
        $stmt->bind_param($bind_types, $employee_firstname, $employee_lastname, $employee_email, $employee_phonenumber, $employee_id);
    }

    if ($stmt->execute()) {
        setFlashMessage("Cập nhật thông tin tài khoản thành công", true);
    } else {
        setFlashMessage("Cập nhật thông tin tài khoản không thành công", false);
    }

    $stmt->close();
}

$Model = (object) [
    'EmployeeID' => '',
    'FirstName' => '',
    'LastName' => '',
    'Email' => '',
    'UserName' => '',
    'PhoneNumber' => '',
    'RoleName' => '',
];
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $Model->EmployeeID = $row['EmployeeID'];
    $Model->FirstName = $row['FirstName'];
    $Model->LastName = $row['LastName'];
    $Model->Email = $row['Email'];
    $Model->UserName = $row['UserName'];
    $Model->PhoneNumber = $row['PhoneNumber'];
    $Model->RoleName = $row['RoleName'];
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Thông tin cá nhân</div>
                <div class="card-body">
                    <div class="form-group">
                        <p><strong>Tên đăng nhập:</strong> <?php echo $Model->UserName; ?></p>
                    </div>
                    <div class="form-group">
                        <p><strong>Họ và tên:</strong> <?php echo $Model->FirstName . ' ' . $Model->LastName; ?></p>
                    </div>
                    <div class="form-group">
                        <p><strong>Email:</strong> <?php echo $Model->Email; ?></p>
                    </div>
                    <div class="form-group">
                        <p><strong>Số điện thoại:</strong> <?php echo $Model->PhoneNumber; ?></p>
                    </div>
                    <div class="form-group">
                        <p><strong>Chức vụ:</strong> <?php echo $Model->RoleName; ?></p>
                    </div>
                    <div class="form-group">
                        <a class="btn btn-primary" onclick="openAdd()">Cập nhật</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Cập nhật thông tin</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ</label>
                        <input type="hidden" value="<?php echo $Model->EmployeeID; ?>" name="EmployeeID" />
                        <input class="form-control" name="FirstName" value="<?php echo $Model->FirstName; ?>" placeholder="Nhập họ" required />
                    </div>
                    <div class="form-group">
                        <label>Tên</label>
                        <input class="form-control" name="LastName" value="<?php echo $Model->LastName; ?>" placeholder="Nhập tên" required />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" name="Email" value="<?php echo $Model->Email; ?>" type="email" placeholder="Nhập email" required />
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input class="form-control" name="PhoneNumber" value="<?php echo $Model->PhoneNumber; ?>" type="tel" placeholder="Nhập số điện thoại" required />
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input class="form-control" name="Password" minlength="8" type="password" placeholder="Nhập mật khẩu" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                    <button name="updateProfile" type="submit" class="btn btn-success">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>