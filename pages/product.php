<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: product.php';

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

$sql = "SELECT * FROM products LIMIT $start_from,$num_per_page";
$result = $conn->query($sql);


// Hàm xử lý thông báo
function setFlashMessage($message, $success) {
    $_SESSION['Message'] = $message;
    $_SESSION['Success'] = $success;
    header("Refresh:0");
    exit();
}



// Hàm thêm mới sản phẩm
function addProduct($conn) {
    if (isset($_POST['addProduct'])) {
        $product_productname = $_POST['ProductName'];
        $product_description = $_POST['Description'];
        $product_unitprice = $_POST['UnitPrice'];
        $product_image = basename($_FILES['ProductImage']['name']);

        // Lưu ảnh vào folder uploads
        $uploadDir = '../uploads/';
        $imageUrl = $uploadDir . $product_image;
        move_uploaded_file($_FILES['ProductImage']['tmp_name'], $imageUrl);

        $sql = "INSERT INTO `products` (`ProductName`, `Description`, `UnitPrice`, `image_url`) 
        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $product_productname, $product_description, $product_unitprice, $product_image);

        if ($stmt->execute()) {
            setFlashMessage("Thêm mới sản phẩm thành công", true);
        } else {
            setFlashMessage("Thêm mới sản phẩm không thành công", false);
        }

        $stmt->close();
    }
}

// Hàm cập nhật thông tin sản phẩm
function updateProduct($conn) {
    if (isset($_POST['updateProduct'])) {
        $product_id = $_POST['ProductID'];
        $product_productname = $_POST['ProductName'];
        $product_description = $_POST['Description'];
        $product_unitprice = $_POST['UnitPrice'];

        $sql = "UPDATE `products` SET `ProductName`=?, `Description`=?, `UnitPrice`=? WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $product_productname, $product_description, $product_unitprice, $product_id);

        if ($stmt->execute()) {
            setFlashMessage("Cập nhật thông tin sản phẩm thành công", true);
        } else {
            setFlashMessage("Cập nhật thông tin sản phẩm không thành công", false);
        }

        $stmt->close();
    }
}

if ($role == "Admin") {
    // Gọi các hàm xử lý
    addProduct($conn);
    updateProduct($conn);
}

// Kiểm tra và gán dữ liệu vào biến $model
$model = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $model[] = $row;
    }
}
// Đóng kết nối cơ sở dữ liệu
// $conn->close();
?>

<div class="container-xxl">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="section-header text-start mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <h1 class="display-5 mb-3">Quản lí sản phẩm</h1>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    <?php if ($role == "Admin"): ?>
                        <a class="btn btn-primary" onclick="openAdd()">Thêm mới</a>
                    <?php endif; ?>

                    <?php if (count($model) > 0): ?>
                        <?php $stt = 1; ?>
                        <div class="table-responsive card mt-2">
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Ảnh sản phẩm</th>
                                    <th>Mô tả</th>
                                    <th>Giá bán</th>
                                    <?php if ($role == "Admin"): ?>
                                        <th></th>
                                    <?php endif; ?>
                                </tr>
                                <?php foreach ($model as $item): ?>
                                    <tr>
                                        <td>
                                            <label style="width: auto"><?php echo $item['ProductID']?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['ProductName']; ?></label>
                                        </td>
                                        <td>
                                            <div>
                                                <img style="width: 100px; height: 100px;" src="../uploads/<?php echo $item['image_url']; ?>" alt="">
                                            </div>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo $item['Description']; ?></label>
                                        </td>
                                        <td>
                                            <label style="width: auto"><?php echo number_format($item['UnitPrice'], 0,',','.') . ' đồng'; ?></label>
                                        </td>
                                        <?php if ($role == "Admin"): ?>
                                            <td>
                                                <a class="btn btn-primary" onclick="return confirmDelete('<?php echo $item['ProductName']?>')" href="deleteproduct.php?id=<?php echo $item['ProductID']; ?> ">Xóa</a>
                                                <a class="btn btn-success" onclick="sua(<?php echo $item['ProductID']; ?>)">Sửa</a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="update<?php echo $item['ProductID']; ?>" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <form method="post">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="productModalLabel">Chỉnh sửa thông tin sản phẩm</h5>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Tên sản phẩm</label>
                                                                        <input type="hidden" value="<?php echo $item['ProductID']; ?>" name="ProductID" />
                                                                        <input class="form-control" name="ProductName" value="<?php echo $item['ProductName']; ?>" placeholder="Nhập tên sản phẩm" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Mô tả</label>
                                                                        <input class="form-control" name="Description" value="<?php echo $item['Description']; ?>" placeholder="Nhập mô tả" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Giá bán</label>
                                                                        <input class="form-control" name="UnitPrice" value="<?php echo $item['UnitPrice']; ?>" type="number" min="0" placeholder="Nhập giá tiền" required />
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" onclick="closeUpdate(<?php echo $item['ProductID']; ?>)" class="btn btn-secondary">Đóng</button>
                                                                    <button name="updateProduct" type="submit" class="btn btn-success">Sửa</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                                <!-- Pagination -->
                            <?php
                                $sql = "SELECT * FROM products";
                                $rs_result = $conn->query($sql);
                                $total_records = $rs_result->num_rows;
                                $total_pages = ceil($total_records/$num_per_page);

                                echo("<div class='pagination'>");
                                for($i =1;$i <=$total_pages;$i++) {
                                    echo("<a href='product.php?page=".$i."'>".$i."</a>");
                                }
                                echo("</div>");
                                $conn->close();
                            ?>
                        </div>
                    <?php else: ?>
                        <p class="alert alert-danger">Danh sách sản phẩm trống</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $role ?>
<?php if ($role == "Admin"): ?>
    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Thêm mới sản phẩm</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tên sản phẩm</label>
                            <input class="form-control" name="ProductName" placeholder="Nhập tên sản phẩm" required />
                        </div>
                        <div class="form-group">
                            <label>Ảnh sản phẩm</label>
                            <input type="file" class="form-control" name="ProductImage" required />
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <input class="form-control" name="Description" placeholder="Nhập mô tả" required />
                        </div>
                        <div class="form-group">
                            <label>Giá bán</label>
                            <input class="form-control" name="UnitPrice" type="number" min="0" placeholder="Nhập giá tiền" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeAdd()" class="btn btn-secondary">Đóng</button>
                        <button name="addProduct" type="submit" class="btn btn-success">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
include('../includes/layout.php');
?>
<script>
    function confirmDelete(name) {
       return confirm("Bạn có chắc chắn muốn xóa sản phẩm " + name + " không?");
    }
        $(document).ready(function() {
            var page = 1;

            function loadData(page) {
                $.ajax({
                    url: 'product.php?page=' + page,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hiển thị dữ liệu lên trang web
                    },
                    error: function() {
                    }
                });
            }

            loadData(page);

            // Xử lý phân trang khi người dùng nhấn nút "Next" hoặc "Previous"
            $('#next').click(function() {
                page++;
                loadData(page);
            });

            $('#prev').click(function() {
                if (page > 1) {
                    page--;
                    loadData(page);
                }
            });
        });
    </script>
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