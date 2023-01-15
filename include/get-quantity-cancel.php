<?php 
require_once("./connect.php");

if(!empty($_GET["brand"]) && !empty($_GET["product"]) && !empty($_GET["quantity"])) {
	$id_brand = $_GET["brand"];
	$id_product = $_GET["product"];
	$quantity = $_GET["quantity"];

    $query = $conn -> prepare("SELECT * FROM tbl_amount WHERE id_brand = :id_brand and id_product = :id_product");
    $query->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $query->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_OBJ);

    if($query->rowCount() > 0){
        $output = (int)$results->output;
        $check = $output - $quantity;

        if($check < 0){
            echo 'Số lượng kệ chỉ còn '.$output.' mà thôi!';
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($check == 0){
            echo 'Lưu ý: Kệ sẽ hết!';
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }else{
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }
    }else{
        echo 'Sản phẩm kệ không có!';
        echo "<script>$('#submits').prop('disabled',true);</script>";
    }
}
?>