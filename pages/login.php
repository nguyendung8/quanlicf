<?php
session_start();

if (isset($_SESSION['username'])) {
    // Người dùng đã đăng nhập, chuyển họ về trang chính (home)
    header("Location: index.php");
    exit();
}
require_once('../includes/db_connection.php');

if (isset($_POST['login'])) {
    $mainCaptcha = strtoupper($_POST['mainCaptcha']);
    $txtInput = strtoupper($_POST['txtInput']);
    if ($mainCaptcha === $txtInput) {

        $current_page = $_SESSION['current_page'] ?? 'Location: index.php';

        // Lấy dữ liệu từ form đăng nhập
        $employee_username = $_POST['UserName'];
        $employee_password = $_POST['Password'];

        // Truy vấn CSDL để kiểm tra đăng nhập
        $sql = "SELECT employees.*, roles.RoleName FROM employees 
        INNER JOIN roles ON employees.RoleID = roles.RoleID
        WHERE UserName = '$employee_username'";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            //$hashed_password = password_hash($employee_password, PASSWORD_DEFAULT);
            if (password_verify($employee_password, $row['Password'])) {
                // Đăng nhập thành công
                $_SESSION['username'] = $row['UserName'];
                $_SESSION['employeeId'] = $row['EmployeeID'];
                $_SESSION['role'] = $row['RoleName'];
                $_SESSION['Message'] = "Đăng nhập thành công";
                $_SESSION['Success'] = true;
                header($current_page); // Chuyển hướng đến trang chính
                exit();
            } else {
                $_SESSION['Message'] = "Sai mật khẩu";
                $_SESSION['Success'] = false;
                header("Location: login.php"); // Chuyển hướng lại trang đăng nhập
                exit();
            }
        } else {
            $_SESSION['Message'] = "Tên đăng nhập không đúng";
            $_SESSION['Success'] = false;
            header("Location: login.php"); // Chuyển hướng lại trang đăng nhập
            exit();
        }
        $conn->close();
    }else {
        echo $mainCaptcha . $txtInput;
        $_SESSION['Message'] = $mainCaptcha . $txtInput;
        $_SESSION['Success'] = false;
        header("Location: login.php"); // Chuyển hướng lại trang đăng nhập
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta https-equiv="x-ua-compatible" content="ie=edge">
    <title>Đăng nhập</title>
    <link rel="shortcut icon" type="image/png" href="../assets/img/avatar.jpg">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/css/login/font/all.css">
    <!-- Bootstrap core CSS -->
    <link href="../assets/css/login/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Material Design Bootstrap -->
    <link href="../assets/css/login/css/mdb.min.css" rel="stylesheet" />
    <!-- Your custom styles (optional) -->
    <link href="../assets/css/login/css/site.css" rel="stylesheet" />
    <link href="../assets/css/login/css/captcha.css" rel="stylesheet" />
    <link id="pagestyle" href="../assets/css/site.css" rel="stylesheet" />
</head>

<body class="login-page" onload="Captcha();">
    <!-- Main Navigation -->
    <header>
        <!-- Intro Section -->
        <section class="view intro-2">
            <div class="mask rgba-stylish-strong h-100 d-flex justify-content-center align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-6 col-md-10 col-sm-12 mx-auto mt-5">
                            <!-- Form with header -->
                            <div class="card wow fadeIn" data-wow-delay="0.3s">
                                <div class="card-body">
                                    <!-- Header -->
                                    <div class="form-header purple-gradient">
                                        <h3 class="font-weight-500 my-2 py-1">
                                            <i class="fas fa-sign-in-alt"></i> Đăng nhập:
                                        </h3>
                                    </div>
                                    <!-- Body -->
                                    <form id="login" method="post">
                                        <?php if (isset($_SESSION["Message"]) && isset($_SESSION["Success"])) : ?>
                                            <?php if ($_SESSION["Success"] && !empty($_SESSION["Message"])) : ?>
                                                <div id="msgAlert" class="alert alert-success" role="alert">
                                                    <?php echo $_SESSION["Message"]; ?>
                                                </div>
                                            <?php else : ?>
                                                <div id="msgAlert" class="alert alert-danger" role="alert">
                                                    <?php echo $_SESSION["Message"]; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="md-form">
                                            <i class="fas fa-user prefix white-text"></i>
                                            <input type="text" id="orangeForm-name" name="UserName" class="form-control" required>
                                            <label for="orangeForm-name">Tên đăng nhập</label>
                                        </div>
                                        <div class="md-form">
                                            <i class="fas fa-lock prefix white-text"></i>
                                            <input type="password" name="Password" id="orangeForm-pass" class="form-control" minlength="8" required>
                                            <label for="orangeForm-pass">Mật khẩu</label>
                                        </div>
                                        <div class="md-form">
                                            <i class="fas fa-key prefix white-text"></i>
                                            <input type="text" name="txtInput" id="txtInput" required />
                                            <label for="txtInput">Mã bảo vệ</label>
                                            <input readonly onclick="Captcha();" type="text" id="mainCaptcha" />
                                            <input type="hidden" name="mainCaptcha" id="iptmainCaptcha" />
                                        </div>
                                        <div class="text-center">
                                            <input name="login" class="btn purple-gradient btn-lg" type="submit" value="Đăng nhập" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Form with header -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Intro Section -->
    </header>
    <?php
    if (isset($_SESSION["Message"])) {
        sleep(5);
        unset($_SESSION['Message']);
    }
    ?>
    <!-- Main Navigation -->
    <!-- SCRIPTS -->
    <!-- JQuery -->
    <script src="../assets/css/login/js/jquery-3.4.1.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script src="../assets/css/login/js/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script src="../assets/css/login/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script src="../assets/css/login/js/mdb.min.js"></script>
    <script src="../assets/css/login/js/captcha.js"></script>
    <script>
        new WOW().init();
    </script>
    <script src="../assets/js/site.js"></script>
</body>

</html>