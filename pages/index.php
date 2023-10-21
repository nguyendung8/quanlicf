<?php
session_start();

// Đặt trang hiện tại trong session
$_SESSION['current_page'] = 'Location: index.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
$content = '

<h2 style="text-align: center; color: #868900;">Chào mừng bạn đến với quán của chúng tôi!</h1>
<img class="bg-img" src="../uploads/bg.jpg">
<p style="margin-top: 10px">"Mỗi hạt cà phê là một câu chuyện về đam mê, chúng tôi bằng tất cả tâm huyết, tự tin sẽ mang lại cho khách hàng
thức uống chỉnh chu và toàn vẹn nhất."</p>
<div style="margin-top: 10px; display: flex; gap: 20px;">
    <p style="font-size: 25px;">Hãy thưởng thức ngay nào!!!</p>
    <button class="order-btn"><a style="color: #fff;" href="./product.php">Order</a></button>
</div>
<p style="font-size: 15px; font-style: italic">Chúc quý khách ngon miệng !!!</p>

';

include('../includes/layout.php');
?>
<style>
    @import "https://fonts.googleapis.com/css?family=Josefin Sans";
html,
body {
  font-family: Josefin Sans;
  background-color: #e4e1dc;
  color: #3e4546; /*gray*/
  font-weight: 300;
  font-size: 20px;
  text-rendering: optimizeLegibility;
  overflow-x: hidden;
}
p{
  font-size: 90%;
  line-height: 1.7;
}
h3{
  color: #99a644; /*green*/
  text-shadow: 0 0 1px #99a644; /*green*/
}

/*-------- HEADER -----------*/
nav {
  background-color: #dab78d; /*beige*/
  width: 100%;
  margin-left: -7.px;
  display: inline-block;
  height: 165px;
}
ul {
  list-style-type: none;
  width: 100%;
  padding-left: 0;
  margin-top: 6%;
}
nav ul li {
  opacity: 1;
  padding-top: 4%;
  width: 14%;
  height: 0;
  float: left;
  color: #ffffff;
}
#sakat{
  all: unset;
}
nav ul li a {
  text-decoration: none;
  color: #491404; /*brown*/
  font-size: 16px;
  font-weight: lighter;
  text-transform: uppercase;
}
nav li a img{
  margin-top: unset;
  width: 200px;
  margin-top: -128px;
}
/*------- //HEADER ----------*/

/*-------- MAIN -------------*/
section ul li p,
section ul li a,
section ul li h1 {
  padding: 7% 0 0;
  text-decoration: none;
  color: #fff;
  text-shadow: 1px 2px black;
}
.smain {
  background-image: url("https://image.freepik.com/free-photo/top-view-roasted-coffee-beans-with-copy-space_23-2148251592.jpg");
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: 100%;
  background-repeat: no-repeat;
  margin-top: 13%;
}
#overmain {
  list-style-type: none;
  color: #fff;
  text-align: center;
  padding-bottom: 9%;
}
.body {
  background-image: url("https://image.freepik.com/free-photo/close-up-blank-old-concrete-wall_23-2147856094.jpg");
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-repeat: no-repeat;
  background-size: 100%;
  height: 139%;
}
/*-------- //MAIN -----------*/

/* ----- SPECIAL TABLE ------*/
.specials {
  margin-top: -50px;
  /* small brown line */
  border-top: 11px solid #dab78d; /*beige*/
  text-align: center;
}
.specials ul {
  margin-top:2%;
  list-style-type: none;
  width: 50%;
  margin-left: auto;
  margin-right: auto;
}
.specials-trigger {
  /* center allign */
  border: 1px solid #54301a; /*brown*/
  border-radius: 50px;
  font-size: 26px;
  display: inline-block;
  cursor: pointer;
  position: relative;
}
.specials li {
  background-color: #54301a; /*brown*/
  padding-top: 30px;
  opacity: 0.5;
  /* box */
  display: table-cell;
  width: 31%;
  -moz-transition: all 0.25s, color 0s !important;
  -o-transition: all 0.25s, color 0s !important;
  transition: all 0.25s, color 0s !important;
  z-index: 0;
  border: 1px #54301a; /*brown*/
}
.header {
  font-weight: 800;
  color: #e4e1dc; /*beige*/
}
.specials li:hover {
  box-shadow: 0 13px 48px rgba(0, 0, 0, 0.2);
  z-index: 1;
  border-radius: 3px;
  opacity: 1;
  background: #b48b66; /*beige*/
  border-color: #54301a; /*brown*/
}
.specials li:hover .header {
  text-shadow: none;
  color: #54301a; /*brown*/
}
.specials ul .description {
  text-shadow: none;
  color: #e4e1dc; /*beige*/
  display: block;
  font-size: 19px;
  line-height: 30px;
}
.specials ul .middescription {
  font-size: 10px;
  text-shadow: none;
  color: #e4e1dc; /*beige*/
  display: block;
  line-height: 30px;
}
.specials .price {
  font-size: 84px;
  letter-spacing: 2px;
  padding-top: 20px;
  display: block;
  font-weight: 400;
  padding-bottom: 12px;
}

/*----- //SPECIAL TABLE -----*/

/*-------- FOOTER -----------*/
footer {
  padding: 6%;
  background-color: #dfddd8; /*dark gray*/
  text-align: center;
  color: #fff;
}
.footer-nav {
  list-style: none;
  font-size: 80%;
}
.footer-nav h4,
footer a:link,
footer a:visited {
  text-decoration: none;
  color: #fff;
  transition: 0.3s;
  margin-top: 5px;
}
footer a:hover,
footer a:active {
  transition: 0.1s;
  /*font-weight: 900; */
  color: #7f5b51; /*light brown*/
}
#h3 {
  font-family: "Snell Roundhand" !important;
  font-weight: normal;
  font-size: 29px;
  color: #fff;
  text-shadow: none;
}
#h3,
h3 {
  line-height: 0.5;
}
#flogo {
  box-shadow: 0 0 10px 0 #4e3629;
  border-radius: 18%;
  width: 14%;
}
#fpin{
  width:10%;
}

#rights {
  font-size: 45%;
  text-align: right;
  color: #b48b66 /*light brow*/
}
#freepik{
  color: #848484; /*grey*/
  text-align: center;
  font-size: 9px;
}
/*------- //FOOTER ----------*/

/*-------- QUERIES ----------*/

.bg-img {
    width: 100%;
    border-radius: 20px;
}
.order-btn {
    background-color: #cfab70;
    border: none;
    border-radius: 15px;
    width: 83px;
    cursor: pointer;
    height: 47px;
}
.order-btn:hover {
    opacity: 0.8;
}
</style>
