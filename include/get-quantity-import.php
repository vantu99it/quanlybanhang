<?php 
require_once("./connect.php");

 if(!empty($_GET["brand"]) && !empty($_GET["product"]) && !empty($_GET["quantity"])) {
	$id_brand = $_GET["brand"];
	$id_product = $_GET["product"];
	$quantity = $_GET["quantity"];

    // Tổng số nhập vào
    $queryInput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 1 AND id_brand = :id_brand and id_product = :id_product");
    $queryInput->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $queryInput->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryInput->execute();
    $resultsInput = $queryInput->fetch(PDO::FETCH_OBJ);
    $input = (int) $resultsInput-> total;

    // Tổng số xuất ra
    $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_brand = :id_brand and id_product = :id_product");
    $queryOutput->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $queryOutput->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryOutput->execute();
    $resultsOutput = $queryOutput->fetch(PDO::FETCH_OBJ);
    $output= (int)$resultsOutput->total;

    $checkTotal = $input - $output;
        echo 'Số lượng kho đang có: <b>'.$checkTotal.'</b>';       

 }


?>