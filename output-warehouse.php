<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
            header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        $id_act = 2;

        //sản phẩm
        $queryProd= $conn -> prepare("SELECT * FROM tbl_product WHERE status = 1");
        $queryProd-> execute();
        $resultsProd = $queryProd->fetchAll(PDO::FETCH_OBJ);
        //cơ sở
        $queryBrand= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1");
        $queryBrand-> execute();
        $resultsBrand = $queryBrand->fetchAll(PDO::FETCH_OBJ);

        //cơ sở theo id
        $queryBrandId= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1 AND id =:id");
        $queryBrandId->bindParam(':id',$id_brand,PDO::PARAM_STR);
        $queryBrandId-> execute();
        $resultsBrandId = $queryBrandId->fetch(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id_brand = $_POST["brand"];
            $id_product = $_POST["product"];
            $quantity = $_POST["quantity"];
            $note = $_POST["note"];
           

            $queryWare= $conn -> prepare("INSERT INTO tbl_warehouse (id_product, quantity, id_user, id_brand, id_act, note ) value (:id_product, :quantity, :id_user, :id_brand, :id_act, :note)");
            $queryWare->bindParam(':id_product',$id_product,PDO::PARAM_STR);
            $queryWare->bindParam(':quantity',$quantity,PDO::PARAM_STR);
            $queryWare->bindParam(':id_user',$id_user,PDO::PARAM_STR);
            $queryWare->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare->bindParam(':id_act',$id_act,PDO::PARAM_STR);
            $queryWare->bindParam(':note',$note,PDO::PARAM_STR);
            $queryWare-> execute();
            $lastInsertId = $conn->lastInsertId();
            if($lastInsertId){
                $msg = "Đã xuất kho thành công!";
            }else{
                $error = "Thất bại! Vui lòng thử lại!";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Xuất kho</title>
    <!-- link-css -->
    <?php include('include/link-css.php');?>
    <!-- /link-css -->
    
</head>
<body>
    <!-- header -->
    <?php include('include/header.php');?>
    <!-- /header -->
    <div id="main">
        <!-- sidebar -->
        <?php include('include/sidebar.php');?>
        <!-- /sidebar -->
        
        <!-- main-right -->
        <div id="main-right">
            <section class="main-right-title">
                <div class="form-title">
                    <h1>Xuất hàng ra kệ</h1>
                </div>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <?php if(isset($error)){ ?>
                        <div class="errorWrap">
                            <strong>Lỗi: </strong><span><?php echo $error; ?> </span>
                        </div>
                    <?php }elseif(isset($msg)){ ?>
                        <div class="succWrap">
                            <strong>Thành công: </strong><span><?php echo $msg; ?> </span>
                        </div>
                    <?php } ?>
                    <!-- input -->
                    <div class="search-item form-validator">
                        <p class="item-name">Cơ sở <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">
                            <?php if($id_power != 3){ ?>
                            <?php foreach ($resultsBrand as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } }else{?>
                                <option value="<?php echo $resultsBrandId -> id ?>" selected><?php echo $resultsBrandId -> name ?></option>
                            <?php }?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Chọn sản phẩm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="product" id="product" onChange="selectProd()">
                            <option value="">Chọn sản phẩm</option>
                            <?php foreach ($resultsProd as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator" id = "nameProduct">
                        <!-- code js đẩy vào -->
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Số lượng xuất ra <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "quantity" id="quantity" value="" placeholder = "" onchange="checkQuantity()">
                        <p class="form-message" id ="quantity-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú</p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit" value="Xuất kho" id = "submits" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->

    <!-- Bắt lỗi nhập vào -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#brand', 'Vui lòng chọn cơ sở'), 
                Validator.isRequired('#product', 'Vui lòng chọn sản phẩm'),
                Validator.isRequired('#quantity', 'Vui lòng nhập số lượng'),
                // Validator.isRequired('#note', 'Vui lòng ghi chú lí do'),
            ],
        });
    </script>
    <!-- select -->
    <script>
        $(document).ready(function() { 
            $("#product").select2({
                placeholder: "Chọn sản phẩm",
                allowClear: true
             }); 
        });
    </script>
    <!-- load chi tiết sản phẩm -->
    <script>
        function selectProd(){
            jQuery.ajax({
                url: "./include/get-product-ware.php",
                data: 'prod=' + $("#product").val(),
                type: "POST",
                success: function(data) {
                    var city = data;
                    console.log(city);
                    $("#nameProduct").html(data);
                },
                error: function() {}
            });
        };
    </script>
    <!-- kiểm tra số lượng xuất ra-->
    <script>
        function checkQuantity(){
            var brand = $("#brand").val();
            var product = $("#product").val();
            var quantity= $("#quantity").val();
            jQuery.ajax({
            url: "./include/get-quantity-output.php?brand="+ brand + "&product="+ product+ "&quantity=" + quantity,
            success: function(data) {
                $("#quantity-message").html(data);
            },
            error: function() {}
            });
        };
    </script>
</body>
</html>