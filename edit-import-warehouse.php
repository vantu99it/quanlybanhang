<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
            header('location:index.php');
    }else{

        $err = "";
        $ok = "";
        $message = "";

        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        $id_act = 1;
        $id_import = $_GET['id'];
        $id_brand_import = $_GET['brand'];


        // Gọi ra thông tin 
        $queryImport= $conn -> prepare("SELECT wa.*, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_brand br ON br.id = wa.id_brand WHERE id_act = :id_act AND wa.id = :id");
        $queryImport->bindParam(':id',$id_import,PDO::PARAM_STR);
        $queryImport->bindParam(':id_act',$id_act,PDO::PARAM_STR);
        $queryImport-> execute();
        $resultsImport = $queryImport->fetch(PDO::FETCH_OBJ);
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

            $queryWare= $conn -> prepare("UPDATE tbl_warehouse SET id_product = :id_product, quantity = :quantity, id_user = :id_user, id_brand = :id_brand, id_act = :id_act, note = :note, update_ad = NOW() WHERE id = :id");
            $queryWare->bindParam(':id_product',$id_product,PDO::PARAM_STR);
            $queryWare->bindParam(':quantity',$quantity,PDO::PARAM_STR);
            $queryWare->bindParam(':id_user',$id_user,PDO::PARAM_STR);
            $queryWare->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare->bindParam(':id_act',$id_act,PDO::PARAM_STR);
            $queryWare->bindParam(':note',$note,PDO::PARAM_STR);
            $queryWare->bindParam(':id',$id_import,PDO::PARAM_STR);
            $queryWare-> execute();
            if($queryWare){
                $ok = 1;
                $message = "Đã cập nhật thành công!";
            }
            else{
                $err = 1;
                $message = "Có lỗi xảy ra, vui lòng thử lại";
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
    <title>Admin | Chỉnh sửa nhập kho</title>
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
                    <h1>Nhập hàng vào kho</h1>
                </div>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <!-- input -->
                    <div class="search-item form-validator">
                        <p class="item-name">Cơ sở <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">
                            <option value="<?php echo $resultsImport -> id_brand ?>"><?php echo $resultsImport -> brand ?></option>
                            <?php foreach ($resultsBrand as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php }?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Chọn sản phẩm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="product" id="product" onChange="selectProd()">
                            <option value="<?php echo $resultsImport -> id_product ?>"><?php echo $resultsImport -> product ?></option>
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
                        <p class="item-name">Số lượng nhập vào <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "quantity" id="quantity" value="<?php echo $resultsImport -> quantity ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"><?php echo $resultsImport -> note ?></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit"  value="Cập nhật" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->

    <!-- Thông báo thành công -->
    <?php if($ok == 1){ ?>
    <div class="noti">
        <div class="success-checkmark">
            <div class="check-icon">
                <span class="icon-line line-tip"></span>
                <span class="icon-line line-long"></span>
                <div class="icon-circle"></div>
                <div class="icon-fix"></div>
            </div>
            <div class="notification">
                <p>
                     <?php echo $message ?>
                </p>
            </div>
            <a href="./import-manage.php?brand=<?php echo $id_brand_import ?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
    <!-- Thông báo thất bại -->
    <?php if($err == 1){ ?>
    <div class="noti">
        <div class="error-banmark">
            <div class="ban-icon">
                <span class="icon-line line-long-invert"></span>
                <span class="icon-line line-long"></span>
                <div class="icon-circle"></div>
                <div class="icon-fix"></div>
            </div>
            <div class="notification">
                <p>
                     <?php echo $message ?>
                </p>
            </div>
            <a href="./edit-import-warehouse.php?id=<?php echo $id_import?>&brand=<?php $id_brand_import?>" class="btn">OK</a>        
        </div>
    </div>
    <?php }?>

    <script>
        $(document).ready(function() { 
            $("#product").select2({
                placeholder: "Chọn sản phẩm",
                allowClear: true
             }); 
        });
    </script>
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
                Validator.numberMin('#quantity',1 ,'Số lượng phải lớn hơn 1'),
            ],
        });
    </script>
</body>
</html>