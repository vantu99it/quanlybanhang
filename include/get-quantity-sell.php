<?php 
require_once("./connect.php");

if(!empty($_GET["brand"]) && !empty($_GET["product"]) && !empty($_GET["quantity"])) {
	$id_brand = $_GET["brand"];
	$id_product = $_GET["product"];
	$quantity = $_GET["quantity"];

    // Tổng số xuất ra
    $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_brand = :id_brand and id_product = :id_product");
    $queryOutput->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $queryOutput->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryOutput->execute();
    $resultsOutput = $queryOutput->fetch(PDO::FETCH_OBJ);
    $output = (int) $resultsOutput -> total;

    // Tổng số đã hủy
    $queryCancel = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 3 AND id_brand = :id_brand and id_product = :id_product");
    $queryCancel->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $queryCancel->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryCancel->execute();
    $resultsCancel = $queryCancel->fetch(PDO::FETCH_OBJ);
    $cancel = (int) $resultsCancel -> total;

    // Tổng số bán
    $querySold = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_sell_manage WHERE id_brand = :id_brand and id_product = :id_product");
    $querySold->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $querySold->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $querySold->execute();
    $resultsSold = $querySold->fetch(PDO::FETCH_OBJ);
    $sold = (int) $resultsSold -> total;

    $remaining= $output - $cancel - $sold;
    $check = $remaining - $quantity;
    if($remaining > 0){
        if($check < 0){
            echo 'Số lượng kệ chỉ còn '.$remaining.' mà thôi!';
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($check == 0){
            echo 'Lưu ý: Kệ sẽ hết!';
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }else{
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }
    }else{
        echo 'Sản phẩm ở kệ không có => không thể bán!';
        echo "<script>$('#submits').prop('disabled',true);</script>";
    }
}
?>