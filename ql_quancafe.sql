-- Đảm bảo bạn đã chọn cơ sở dữ liệu mà bạn muốn sử dụng
USE mysql;

-- Tạo cơ sở dữ liệu QL_QuanCafe
CREATE DATABASE QL_QuanCafe;

-- Sử dụng cơ sở dữ liệu mới tạo
USE QL_QuanCafe;

-- Tạo bảng __EFMigrationsHistory
CREATE TABLE __EFMigrationsHistory (
    MigrationId VARCHAR(150) NOT NULL,
    ProductVersion VARCHAR(32) NOT NULL,
    PRIMARY KEY (MigrationId)
);

-- Tạo bảng Customers
CREATE TABLE Customers (
    CustomerID INT AUTO_INCREMENT NOT NULL,
    FirstName TEXT,
    LastName TEXT,
    Email TEXT,
    PhoneNumber TEXT,
    PRIMARY KEY (CustomerID)
);

-- Tạo bảng Roles
CREATE TABLE Roles (
    RoleID INT AUTO_INCREMENT NOT NULL,
    RoleName TEXT,
    PRIMARY KEY (RoleID)
);

-- Tạo bảng Employees
CREATE TABLE Employees (
    EmployeeID INT AUTO_INCREMENT NOT NULL,
    FirstName TEXT,
    LastName TEXT,
    Email TEXT,
    PhoneNumber TEXT,
    UserName TEXT,
    Password TEXT,
    RoleID INT NOT NULL,
    PRIMARY KEY (EmployeeID),
    FOREIGN KEY (RoleID) REFERENCES Roles(RoleID) ON DELETE CASCADE
);

-- Tạo bảng DailyReports
CREATE TABLE DailyReports (
    ReportID INT AUTO_INCREMENT NOT NULL,
    EmployeeID INT NOT NULL,
    ReportDate DATETIME NOT NULL,
    TotalOrders INT NOT NULL,
    TotalRevenue DECIMAL(18, 2) NOT NULL,
    TotalCustomers INT NOT NULL,
    SpecialEvents TEXT,
    GeneraInfo TEXT,
    Improvements TEXT,
    PRIMARY KEY (ReportID),
    FOREIGN KEY (EmployeeID) REFERENCES Employees(EmployeeID) ON DELETE CASCADE
);

-- Tạo bảng Orders
CREATE TABLE Orders (
    OrderID INT AUTO_INCREMENT NOT NULL,
    CustomerID INT NOT NULL,
    EmployeeID INT NOT NULL,
    OrderDate DATETIME NOT NULL,
    PRIMARY KEY (OrderID),
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID) ON DELETE CASCADE,
    FOREIGN KEY (EmployeeID) REFERENCES Employees(EmployeeID) ON DELETE CASCADE
);

-- Tạo bảng Products
CREATE TABLE Products (
    ProductID INT AUTO_INCREMENT NOT NULL,
    ProductName TEXT,
    Description TEXT,
    UnitPrice DECIMAL(18, 2) NOT NULL,
    PRIMARY KEY (ProductID)
);

-- Tạo bảng OrderDetails
CREATE TABLE OrderDetails (
    OrderDetailID INT AUTO_INCREMENT NOT NULL,
    OrderID INT NOT NULL,
    ProductID INT NOT NULL,
    Quantity INT NOT NULL,
    TotalPrice DECIMAL(18, 2) NOT NULL,
    PRIMARY KEY (OrderDetailID),
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES Products(ProductID) ON DELETE CASCADE
);
INSERT INTO `roles`(`RoleName`) VALUES ('Admin');
INSERT INTO `employees`(`FirstName`, `LastName`, `Email`, `PhoneNumber`, `UserName`, `Password`, `RoleID`) 
VALUES ('Nguyễn','Văn A','nva@gmail.com','0779054545','admin','$2y$10$BMtytvnA5YHJq9svjux1GuaMpMuJKWSsbaMIOdIhlHVSun2/wgfra','1')