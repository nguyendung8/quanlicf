<?php
   require('../includes/db_connection.php');

    $id = $_GET["id"];

    $sql = "DELETE FROM employees where EmployeeID = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Nhân viên đã được xóa thành công";
    } else {
        echo "Lỗi khi xóa nhân viên: " . $conn->error;
    }
    header('location: employee.php');

?>
