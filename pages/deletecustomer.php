<?php
   require('../includes/db_connection.php');

    $id = $_GET["id"];

    $sql = "DELETE FROM customers where CustomerID = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Khách hàng đã được xóa thành công";
    } else {
        echo "Lỗi khi xóa khách hàng: " . $conn->error;
    }
    header('location: customer.php');

?>
