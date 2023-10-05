setTimeout(function () {
    $("#msgAlert").fadeOut("slow");
}, 2000);

function confirm_delete(id, type) {
    let confirmMessage = "";

    switch (type) {
        case "Customer":
            confirmMessage = "Bạn muốn xóa khách hàng";
            break;
        case "Employee":
            confirmMessage = "Bạn muốn xóa nhân viên";
            break;
        case "Product":
            confirmMessage = "Bạn muốn xóa sản phẩm";
            break;
        case "Order":
            confirmMessage = "Bạn muốn xóa đơn hàng";
            break;
        case "OrderDetail":
            confirmMessage = "Bạn muốn xóa chi tiết đơn hàng";
            break;
        default:
            return;
    }

    if (confirm(confirmMessage)) {
        window.location.href = '../../actions/confirm_delete.php?id=' + id + '&type=' + type;
    }
}

function sua(id) {
    $("#update" + id).modal("show");
}
function closeUpdate(id) {
    $("#update" + id).modal("hide");
}
function openAdd() {
    $("#add").modal("show");
}
function closeAdd() {
    $("#add").modal("hide");
}

$(document).ready(function () {
    $('#btnAddOrder').on('click', function () {
        var orderDetails = [];
        const table = document.getElementById('orderDetailsTable');
        const rows = table.rows;
        const CustomerID = document.getElementById('customerID').value;
        const EmployeeID = document.getElementById('employeeID').value;

        if (CustomerID == "") {
            alert("Vui lòng chọn khách hàng");
        }
        else if (EmployeeID == "") {
            alert("Vui lòng chọn nhân viên")
        }
        else if (rows.length <= 1) {
            alert("Vui lòng chọn thức uống")
        }
        else {
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.cells;
                const selectElement = cells[0].querySelector('select');
                const quantityInput = cells[1].querySelector('.quantity');
                const totalPriceInput = cells[2].querySelector('.totalPrice');

                const rowData = {
                    ProductID: parseInt(selectElement.value),
                    Quantity: parseInt(quantityInput.value),
                    TotalPrice: parseInt(totalPriceInput.value)
                };

                orderDetails.push(rowData);
            }

            const order = {
                CustomerID: CustomerID,
                EmployeeID: EmployeeID,
                OrderDetails: orderDetails
            };
            // Gửi yêu cầu POST bằng Ajax
            $.ajax({
                url: '../../actions/add_order.php',
                type: 'POST',
                data: JSON.stringify(order),
                contentType: 'application/json',
                success: function (response) {
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    window.location.reload();
                    console.error('Lỗi khi gửi yêu cầu: ', error);
                }
            });
        }
    });
});
function chiTiet(id) {
    document.getElementById('orderID').value = id;
    $.ajax({
        url: '../../actions/detail_order.php',
        type: 'POST',
        data: { idOrder: id },
        success: function (response) {
            $("#lstOrderDetailsTable").html(response);
            $("#updateOrderDetail").modal("show");
        },
        error: function (xhr, status, error) {
            console.error('Lỗi khi gửi yêu cầu: ', error);
        }
    });
}
function closeUpdateOrderDetail() {
    $("#updateOrderDetail").modal("hide");
}
function confirmLogout() {
    var confirmResult = confirm("Bạn có chắc chắn muốn đăng xuất?");
    if (confirmResult) {
        window.location.href = "/QL_QuanCafe_PHP/pages/logout.php";
    }
}
function showInputField(selectedType) {
    // Ẩn tất cả các trường nhập liệu
    $('#divDate').hide();
    $('#divMonth').hide();
    $('#inputDate').val('');
    $('#inputMonth').val('');
    $('#inputYear').val('');

    // Hiển thị trường nhập liệu tương ứng với loại thống kê được chọn
    switch (selectedType) {
        case 'byDate':
            $('#divDate').show();
            break;
        case 'byMonth':
            $('#divMonth').show();
            break;
        case 'byYear':
            $('#divYear').show();
            break;
    }
}
function revenue_management() {
    const selectedType = $('#inputType').val();
    var data;

    if (selectedType == "byDate") {
        const date = $('#inputDate').val();
        if (date != null && date.trim() !== "") {
            data = {
                date: date
            };
        }
    }
    else if (selectedType == "byMonth") {
        const selectedMonth = $('#inputMonth').val();
        if (selectedMonth != null && selectedMonth.trim() !== "") {
            const year = selectedMonth.split('-')[0];
            const month = selectedMonth.split('-')[1];
            data = {
                year: year,
                month: month
            };
        }
    }
    else if (selectedType == "byYear") {
        const year = $('#inputYear').val();
        if (year != null && year.trim() !== "") {
            data = {
                year: year
            };
        }
    }
    if (data != null) {
        $.ajax({
            url: '../../actions/get_revenue.php',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            success: function (data) {
                $('#result').html(data);
            },
            error: function (xhr, status, error) {
                console.error('Đã xảy ra lỗi khi lấy doanh thu: ', error);
            }
        });
    } else {
        alert("Vui lòng chọn thời gian")
    }

}
