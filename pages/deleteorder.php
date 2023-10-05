<?php
   require('../includes/db_connection.php');

    $id = $_GET["id"];

    $sql = "DELETE FROM orders where OrderID = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Đơn hàng đã được xóa thành công";
    } else {
        echo "Lỗi khi xóa đơn hàng: " . $conn->error;
    }
    header('location: order.php');

?>
