<?php
require_once('../includes/db_connection.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    if (isset($data['date'])) {
        $date = date("Y-m-d", strtotime($data['date']));
        $sql = "SELECT SUM(orderdetails.TotalPrice) AS revenue 
        FROM orderdetails
        INNER JOIN orders ON orderdetails.OrderID = orders.OrderID
        WHERE DATE(orders.OrderDate) = '$date'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $revenue = isset($row['revenue']) ? $row['revenue'] : 0;
            $formattedData = number_format($revenue, 0, ',', '.') . ' đồng';
            // Hiển thị số tiền đã định dạng
            echo 'Doanh thu: ' . $formattedData;
        } else {
            echo 'Lỗi truy vấn CSDL';
        }
    } else if (isset($data['month']) && isset($data['year'])) {
        $year = $data['year'];
        $month = $data['month'];

        // Truy vấn CSDL để tính tổng doanh thu
        $sql = "SELECT SUM(orderdetails.TotalPrice) AS revenue 
        FROM orderdetails
        INNER JOIN orders ON orderdetails.OrderID = orders.OrderID
        WHERE YEAR(orders.OrderDate) = '$year' AND MONTH(orders.OrderDate) = '$month'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $revenue = isset($row['revenue']) ? $row['revenue'] : 0;
            $formattedData = number_format($revenue, 0, ',', '.') . ' đồng';
            // Hiển thị số tiền đã định dạng
            echo 'Doanh thu: ' . $formattedData;
        } else {
            echo 'Lỗi truy vấn CSDL';
        }
    } else if (isset($data['year'])) {
        $year = $data['year'];

        // Truy vấn CSDL để tính tổng doanh thu
        $sql = "SELECT SUM(orderdetails.TotalPrice) AS revenue 
                FROM orderdetails
                INNER JOIN orders ON orderdetails.OrderID = orders.OrderID
                WHERE YEAR(orders.OrderDate) = '$year'";
        $result = $conn->query($sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $revenue = isset($row['revenue']) ? $row['revenue'] : 0;
            $formattedData = number_format($revenue, 0, ',', '.') . ' đồng';
            // Hiển thị số tiền đã định dạng
            echo 'Doanh thu: ' . $formattedData;
        } else {
            echo 'Lỗi truy vấn CSDL';
        }
    }
} else {
    echo "Không có dữ liệu hoặc dữ liệu không hợp lệ.";
}