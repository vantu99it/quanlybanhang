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
        $input = (int)$results->input;
        $check = $input - $quantity;

        if($check < 0){
            echo 'Số lượng kệ chỉ còn '.$input.' -> không thể xuất';
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($check == 0){
            echo 'Lưu ý: Nếu xuất '.$quantity.' kho sẽ hết!';
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }else{
            echo "<script>$('#submits').prop('disabled',false);</script>";
        }
    }else{
        echo 'Sản phẩm trong kho không có!';
        echo "<script>$('#submits').prop('disabled',true);</script>";
    }
}
?>