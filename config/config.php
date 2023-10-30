<?php 

define("KEY_TOKEN", "ASU.ACO-682*");
define("TOKEN_MP", "TEST-3164937789388934-102101-bd8ab5e3e4affce98ba7dea344543a22-230891789");
define("MONEDA", "$");

session_start();
$num_cart = 0;
if(isset($_SESSION['carrito']['productos'])){
    $num_cart = count($_SESSION['carrito']['productos']);
}
?>