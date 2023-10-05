<?php
session_start();

require_once('../includes/db_connection.php');
$role = $_SESSION['role'] ?? '';
$current_page = 'Location: ../pages/index.php';

// Lấy giá trị của tham số "id" từ URL
$id = $_GET['id'];

// Lấy giá trị của tham số "type" từ URL
$type = $_GET['type'];

// Tạo ánh xạ từ "type" sang tên bảng và trang chuyển hướng
$tableMappings = [
    "Customer" => ["table" => "customers", "redirect" => "customer.php"],
    "Employee" => ["table" => "employees", "redirect" => "employee.php"],
    "Product" => ["table" => "products", "redirect" => "product.php"],
    "Order" => ["table" => "orders", "redirect" => "order.php"],
    "OrderDetail" => ["table" => "orderdetails", "redirect" => "order.php"]
];

if (isset($id) && isset($type) && $role == "Admin" && isset($tableMappings[$type])) {
    $tableInfo = $tableMappings[$type];
    $tableName = $tableInfo["table"];
    $redirectPage = $tableInfo["redirect"];

    $sql = "DELETE FROM $tableName WHERE {$type}ID = $id";
    $result = $conn->query($sql);

    if ($result === TRUE) {
        $messageSuccess = "Xóa thành công.";
    } else {
        $messageError = "Lỗi xóa: " . $conn->error;
    }

    $_SESSION['Message'] = isset($messageSuccess) ? $messageSuccess : $messageError;
    $_SESSION['Success'] = isset($messageSuccess);

    $current_page = "Location: ../pages/$redirectPage";
}

header($current_page);
exit();
$conn->close();
?>
