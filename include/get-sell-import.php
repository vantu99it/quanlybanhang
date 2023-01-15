<?php 
require_once("./connect.php");

if(!empty($_GET["quantity"]) && !empty($_GET["product"]) && !empty($_GET["payment"])) {
    $quantity = $_GET["quantity"];
    $id_product = $_GET["product"];
    $id_payment = $_GET["payment"];
    $sale = $_GET["sale"];
    $plus = $_GET["plus"];

    $queryPro = $conn -> prepare("SELECT * FROM tbl_product WHERE id = :id_product");
    $queryPro->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryPro->execute();
    $resultsPro = $queryPro->fetch(PDO::FETCH_OBJ);
    $price = $resultsPro -> price;

    $total = $price * $quantity - $sale + $plus;
    $totalNew = number_format($total,0,",",".");
    echo '<p class="item-name">Tổng tiền</p>
            <input type="text" class="form-focus boder-ra-5 col-red" name = "" id="" value="'.$totalNew.' VNĐ" placeholder = "" disabled style = "font-weight: 700;">';
        
}
?>