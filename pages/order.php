<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: order.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require_once('../includes/db_connection.php');
ob_start();

$role = $_SESSION["role"] ?? '';

// Xử lý phân trang ajax
$num_per_page = 03;

if(isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}

$start_from = ($page - 1) * 03;

$sqlOder = "SELECT employees.LastName AS EmployeeLastName, customers.LastName AS CustomerLastName, 
            employees.FirstName AS EmployeeFirstName, customers.FirstName AS CustomerFirstName, 
            employees.EmployeeID,
            customers.CustomerID,
            orders.* FROM orders
              INNER JOIN customers ON customers.CustomerID = orders.CustomerID
              INNER JOIN employees ON employees.EmployeeID = orders.EmployeeID
              LIMIT $start_from,$num_per_page";
$resultOrder = $conn->query($sqlOder);

// Hàm xử lý thông báo
function setFlashMessage($message, $success)
{
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}

$sqlEmployees = "SELECT * FROM employees";
$resultEmployees = $conn->query($sqlEmployees);

$sqlCustomers = "SELECT * FROM customers";
$resultCustomers = $conn->query($sqlCustomers);

$orders = [];
if ($resultOrder->num_rows > 0) {
    while ($row = $resultOrder->fetch_assoc()) {
        $orders[] = $row;
    }
}
$employees = [];
if ($resultEmployees->num_rows > 0) {
    while ($row = $resultEmployees->fetch_assoc()) {
        $employees[] = $row;
    }
}
$customers = [];
if ($resultCustomers->num_rows > 0) {
    while ($row = $resultCustomers->fetch_assoc()) {
        $customers[] = $row;
    }
}
// Get list product
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Hàm tính toán tổng giá tiền khi thay đổi
function updateTotalPrice($selectedProductID, $selectedQuantity, $products)
{
    foreach ($products as $product) {
        if ($product['ProductID'] == $selectedProductID) {
            $totalPrice = $product['UnitPrice'] * $selectedQuantity;
            return $totalPrice;
        }
    }
    return 0;
}

//create order 
function addOrder($conn) {
    if (isset($_POST['addOrder'])) {
        $customer_id = $_POST['customerId'];
        $employee_id = $_POST['employeeId'];

        $sql = "INSERT INTO `orders` (`CustomerID`, `EMployeeID`, `UnitPrice`) 
        VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $customer_id, $employee_id);

        if ($stmt->execute()) {
            setFlashMessage("Thêm mới đơn hàng thành công", true);
        } else {
            setFlashMessage("Thêm mới đơn hàng không thành công", false);
        }
        $stmt->close();
    }
}

//update order
function updateOrder($conn)
{
    if (isset($_POST['updateOrder'])) {
        $customer_customerid = $_POST['CustomerID'];
        $customer_employeeid = $_POST['EmployeeID'];
        $customer_orderid = $_POST['OrderID'];

        $sql = "UPDATE `orders` SET `CustomerID`=?, `EmployeeID`=? WHERE OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $customer_customerid, $customer_employeeid, $customer_orderid);

        if ($stmt->execute()) {
            setFlashMessage("Cập nhật thông tin đơn hàng thành công", true);
        } else {
            setFlashMessage("Cập nhật thông tin đơn hàng không thành công", false);
        }

        $stmt->close();
    }
}
updateOrder($conn);
addOrder($conn);

// Đóng kết nối cơ sở dữ liệu
// $conn->close();
?>
<script>
    function addOrderDetailRow(id) {
        const products = <?php echo json_encode($products); ?>;
        const table = document.getElementById(id);
        const newRow = table.insertRow();

        const productCell = newRow.insertCell();
        const quantityCell = newRow.insertCell();
        const totalPriceCell = newRow.insertCell();
        const deleteCell = newRow.insertCell(); // Thêm cột mới cho nút xóa

        const newSelect = document.createElement('select');
        newSelect.classList.add('form-control');
        newSelect.required = true;

        products.forEach(item => {
            newSelect.id = 'ProductID' + item.ProductID;
            const option = document.createElement('option');
            option.value = item.ProductID;
            option.textContent = item.ProductName;
            newSelect.appendChild(option);
        });

        // Thêm sự kiện 'change' vào select box
        newSelect.addEventListener('change', updateTotalPrice);

        const newQuantityInput = document.createElement('input');
        newQuantityInput.type = 'number';
        newQuantityInput.classList.add('form-control');
        newQuantityInput.classList.add('quantity');
        newQuantityInput.required = true;
        newQuantityInput.value = 1; // Đặt giá trị mặc định là 1
        newQuantityInput.min = 1; // Giá trị nhỏ nhất là 1

        // Thêm sự kiện 'change' vào ô quantity
        newQuantityInput.addEventListener('change', updateTotalPrice);

        const newTotalPriceInput = document.createElement('input');
        newTotalPriceInput.type = 'number';
        newTotalPriceInput.classList.add('form-control');
        newTotalPriceInput.classList.add('totalPrice');
        newTotalPriceInput.required = true;

        // Tính giá tiền ban đầu (giá của sản phẩm đầu tiên * 1)
        const firstProduct = products[0];
        if (firstProduct) {
            const initialPrice = firstProduct.UnitPrice;
            newTotalPriceInput.value = initialPrice;
        }

        productCell.appendChild(newSelect);
        quantityCell.appendChild(newQuantityInput);
        totalPriceCell.appendChild(newTotalPriceInput);

        // Thêm nút xóa
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.textContent = 'Xóa';
        deleteButton.classList.add('btn');
        deleteButton.classList.add('btn-warning');
        deleteButton.addEventListener('click', function() {
            const rowId = newRow.dataset.id;
            // Xóa hàng khi nhấn nút xóa
            table.deleteRow(newRow.rowIndex);
        });

        deleteCell.appendChild(deleteButton); // Đưa nút xóa vào ô deleteCell

        // Hàm tính toán tổng giá tiền khi thay đổi
        function updateTotalPrice() {
            const selectedProductID = parseInt(newSelect.value);
            const selectedQuantity = parseInt(newQuantityInput.value);
            // Tìm sản phẩm đã chọn trong danh sách sản phẩm
            const selectedProduct = products.find(product => product.ProductID === selectedProductID.toString());

            if (selectedProduct) {
                const totalPrice = selectedProduct.UnitPrice * selectedQuantity;
                newTotalPriceInput.value = totalPrice;
            }
        }
    }
</script>

<div class="container-xxl">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <h1 class="display-5 mb-3">Quản lí đơn hàng</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    <a class="btn btn-primary" onclick="openAdd()">Thêm mới</a>
                    <?php if (count($orders) > 0) : ?>
                        <?php $stt = 1; ?>
                        <div class="table-responsive card mt-2">
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên khách hàng</th>
                                    <th>Tên nhân viên tạo đơn hàng</th>
                                    <th>Ngày tạo hóa đơn</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td>
                                        <label style="width: auto"><?php echo $order['OrderID']?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $order['CustomerFirstName'] . ' ' . $order['CustomerLastName']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $order['EmployeeFirstName'] . ' ' . $order['EmployeeLastName']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $order['OrderDate']; ?></label>
                                        </td>
                                        <td>
                                            <a class="btn btn-primary" href="deleteorder.php?id=<?php echo $order['OrderID']; ?> ">Xóa</a>
                                            <a class="btn btn-success" onclick="sua(<?php echo $order['OrderID']; ?>)">Sửa</a>
                                            <a class="btn btn-warning" onclick="chiTiet(<?php echo $order['OrderID']; ?>)">Chi tiết</a>

                                            <!-- Modal -->
                                            <div class="modal fade" id="update<?php echo $order['OrderID']; ?>" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <form method="post">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="orderModalLabel">Chỉnh sửa thông tin đơn hàng</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Tên khách hàng</label>
                                                                    <input type="hidden" value="<?php echo $order['OrderID']; ?>" name="OrderID" />
                                                                    <select class="form-control" name="CustomerID" required>
                                                                        <?php foreach ($customers as $customer) : ?>
                                                                            <option value="<?php echo $customer['CustomerID']; ?>" <?php echo $customer['CustomerID'] == $order['CustomerID'] ? 'selected' : ''; ?>>
                                                                                <?php echo $customer['FirstName'] . ' ' . $customer['LastName']; ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Tên nhân viên</label>
                                                                    <select class="form-control" name="EmployeeID" required>
                                                                        <?php foreach ($employees as $employee) : ?>
                                                                            <option value="<?php echo $employee['EmployeeID']; ?>" <?php echo $employee['EmployeeID'] == $order['EmployeeID'] ? 'selected' : ''; ?>>
                                                                                <?php echo $employee['FirstName'] . ' ' . $employee['LastName']; ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" onclick="closeUpdate(<?php echo $order['OrderID']; ?>)" class="btn btn-secondary">Đóng</button>
                                                                <button type="submit" name="updateOrder" class="btn btn-success">Sửa</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                             <!-- Pagination -->
                             <?php
                                $sql = "SELECT * FROM orders";
                                $rs_result = $conn->query($sql);
                                $total_records = $rs_result->num_rows;
                                $total_pages = ceil($total_records/$num_per_page);

                                echo("<div class='pagination'>");
                                for($i =1;$i <=$total_pages;$i++) {
                                    echo("<a href='order.php?page=".$i."'>".$i."</a>");
                                }
                                echo("</div>");
                                $conn->close();
                            ?>
                        </div>
                    <?php else : ?>
                        <p class="alert alert-danger">Danh sách đơn hàng trống</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm mới đơn hàng -->
<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form method="post">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Thêm mới đơn hàng</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tên khách hàng</label>
                    <select name="customerId" class="form-control" id="customerID" required>
                        <?php foreach ($customers as $item1) : ?>
                            <option value="<?php echo $item1['CustomerID']; ?>"><?php echo $item1['FirstName'] . ' ' . $item1['LastName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tên nhân viên</label>
                    <select name="employeeId" class="form-control" id="employeeID" required>
                        <?php foreach ($employees as $item1) : ?>
                            <option value="<?php echo $item1['EmployeeID']; ?>"><?php echo $item1['FirstName'] . ' ' . $item1['LastName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class=" table-responsive">
                        <table id="orderDetailsTable" class="table">
                            <tr>
                                <th>Mã sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Tổng giá tiền</th>
                            </tr>
                        </table>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addOrderDetailRow('orderDetailsTable')">Thêm chi tiết</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                <button name="addOrder" type="submit" class="btn btn-success" id="btnAddOrder">Thêm</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateOrderDetail" tabindex="-1" role="dialog" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class=" table-responsive">
                        <table id="lstOrderDetailsTable" class="table">
                        </table>
                    </div>
                    <input type="hidden" id="orderID" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeUpdateOrderDetail()" class="btn btn-secondary">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>
<style>
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 50px;
        width: 100%;
        /* border: 1px solid #168fff; */
        border-radius: 10px;
    }
    .pagination a {
        color: #fff;
        border: none;
        width: 29px;
        height: 29px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #168fff;
        border-radius: 6px;
    }
    .pagination a:hover {
        opacity: 0.7;
    }
</style>