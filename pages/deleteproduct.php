<?php
   require('../includes/db_connection.php');

    $id = $_GET["id"];

    $sql = "DELETE FROM products where ProductID = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Sản phẩm đã được xóa thành công";
    } else {
        echo "Lỗi khi xóa sản phẩm: " . $conn->error;
    }
    header('location: product.php');

?>
