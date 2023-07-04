<?php 
require_once("./connect.php");

if(!empty($_GET["sale"]) && !empty($_GET["pro"]) ) {
	$sale = $_GET["sale"];
	$id_product = $_GET["pro"];

    // Gọi ra số tiền của loại sản phẩm
    $queryPro = $conn -> prepare("SELECT * FROM tbl_product WHERE id = :id_product");
    $queryPro->bindParam(':id_product',$id_product, PDO::PARAM_STR);
    $queryPro->execute();
    $resultsPro = $queryPro->fetch(PDO::FETCH_OBJ);
    $price = $resultsPro -> price;

    $options = '';
    //sản phẩm
    $queryProd= $conn -> prepare("SELECT * FROM tbl_product WHERE status = 1");
    $queryProd-> execute();
    $resultsProd = $queryProd->fetchAll(PDO::FETCH_OBJ);
    foreach ($resultsProd as $key => $value) {
        $options .= '<option value="' . $value->id . '">' . $value->name . '</option>';
    }

    if($sale == "1"){
        echo '<div class="form-input form-validator">
                <p class="item-name">Giá ban đầu</p>
                <input type="number" class="form-focus boder-ra-5" name = "price-old" id="price-old" value="'.$price.'" placeholder = "" disabled>
                <p class="form-message"></p>
            </div>
            <div class="form-input form-validator">
                <p class="item-name">Giá giảm<span class="col-red">*</span></p>
                <input type="number" class="form-focus boder-ra-5" name = "price-news" id="price-news" value="" placeholder = "">
                <p class="form-message"></p>
            </div>';
    }else{
        echo '<div class="search-item form-validator">
                <p class="item-name">Sản phẩm tặng kèm <span class="col-red">*</span></p>
                <select  class="autobox form-focus boder-ra-5" name ="product-sale" id="product-sale" onChange="selectProdSale()">
                    <option value="">Chọn sản phẩm</option>'.$options.'</select>
                <p class="form-message"></p>
            </div>
            <div class="form-input form-validator" id = "nameProductSale">
            </div>
            <div class="form-input form-validator">
                <p class="item-name">Số lượng <span class="col-red">*</span></p>
                <input type="number" class="form-focus boder-ra-5" name = "numSale" id="numSale" value="" placeholder = "">
                <p class="form-message"></p>
            </div>';
    }
}
?>