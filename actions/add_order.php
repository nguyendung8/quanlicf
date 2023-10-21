<?php
session_start();

require_once('../includes/db_connection.php');

$data = json_decode(file_get_contents('php://input'), true);
// Hàm xử lý thông báo
function setFlashMessage($message, $success)
{
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
}
if ($data) {
    if (isset($data['CustomerID']) && isset($data['EmployeeID'])) {
        $sql = "INSERT INTO `orders` (`CustomerID`, `EmployeeID`, `OrderDate`) 
        VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $data['CustomerID'], $data['EmployeeID']);

        if ($stmt->execute()) {
            $orderID = mysqli_insert_id($conn);
            if (isset($data['OrderDetails'])) {
                $n = 0;
                foreach ($data['OrderDetails'] as $orderDetail) {

                    $sqlDetail = "INSERT INTO `orderdetails` (`OrderID`, `ProductID`, `Quantity`, `TotalPrice`) 
                    VALUES (?, ?, ?, ?)";
                    $stmtDetail = $conn->prepare($sqlDetail);
                    $stmtDetail->bind_param("ssss", $orderID, $orderDetail['ProductID'], $orderDetail['Quantity'], $orderDetail['TotalPrice']);
                    if ($stmtDetail->execute()) {
                        $n++;
                    }
                }
                if($n == count($data['OrderDetails'])){
                    setFlashMessage("Thêm mới đơn hàng thành công", true);
                    echo "Thêm mới đơn hàng thành công";
                }
                else{
                    setFlashMessage("Thêm mới đơn hàng không thành công", false);
                    echo "Thêm mới đơn hàng không thành công";
                }
            }
        } else {
            setFlashMessage("Thêm mới đơn hàng không thành công", false);
            echo "Thêm mới đơn hàng không thành công";
        }

        $stmt->close();
    }
} else {
    setFlashMessage("Không có dữ liệu hoặc dữ liệu không hợp lệ.", false);
    echo "Không có dữ liệu hoặc dữ liệu không hợp lệ.";
}
