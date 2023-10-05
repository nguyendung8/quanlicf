<?php
require_once('../includes/db_connection.php');

if (isset($_POST['idOrder'])) {
    $idOrder = $_POST['idOrder'];
    $sql = "SELECT *
        FROM orderdetails
        WHERE OrderID = $idOrder";
    $result = $conn->query($sql);

    if ($result) {
        $html = '';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<th>Mã sản phẩm</th>';
        $html .= '<th>Số lượng</th>';
        $html .= '<th>Tổng giá tiền</th>';
        $html .= '</tr>';
        while ($row = $result->fetch_assoc()) {
            $productID = $row['ProductID'];
            $quantity = $row['Quantity'];
            $totalPrice = $row['TotalPrice'];
            $html .= '<tr>';
            $html .= '<td>' . $productID . '</td>';
            $html .= '<td>' . $quantity . '</td>';
            $html .= '<td>' . $totalPrice . '</td>';
            $html .= '</tr>';
        }
        $html .= '<tbody>';
        echo $html;
    } else {
        echo 'Lỗi truy vấn CSDL';
    }
} else {
    echo "Không có dữ liệu hoặc dữ liệu không hợp lệ.";
}
