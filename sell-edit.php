<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $get_month = date('Y-m');
        $get_today = date('Y-m-d');
        // var_dump($getdate); die();
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        $id_act = 1;
        $id_sell = $_GET['id'];

        //gọi ra thông tin đơn hàng
         $querySell= $conn -> prepare("SELECT sell.*, br.name AS brandName, us.fullname, pro.name AS product, pay.name AS payment, frm.name AS fromWhere
        FROM tbl_sell_manage sell JOIN tbl_brand br ON br.id = sell.id_brand
        JOIN tbl_user us ON us.id = sell.id_user_sell
        JOIN tbl_product pro ON pro.id = sell.id_product
        JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status
        JOIN tbl_from_where frm ON frm.id = sell.id_from_where
        WHERE sell.id = :id");
        $querySell->bindParam(':id',$id_sell,PDO::PARAM_STR);
        $querySell-> execute();
        $resultsSell = $querySell->fetch(PDO::FETCH_OBJ);

        //nhân viên
        $queryUser= $conn -> prepare("SELECT * FROM tbl_user WHERE status = 1");
        $queryUser-> execute();
        $resultsUser = $queryUser->fetchAll(PDO::FETCH_OBJ);
        
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

        //thanh toán
        $queryPay= $conn -> prepare("SELECT * FROM tbl_payment_status WHERE status = 1");
        $queryPay-> execute();
        $resultsPay = $queryPay->fetchAll(PDO::FETCH_OBJ);

        //Đơn từ đâu
        $queryFrom= $conn -> prepare("SELECT * FROM tbl_from_where WHERE status = 1");
        $queryFrom-> execute();
        $resultsFrom = $queryFrom->fetchAll(PDO::FETCH_OBJ);



        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id_brand = $_POST["brand"];
            $id_user_sell = $_POST["user"];
            $date = $_POST["date"];
            $id_product = $_POST["product"];
            $quantity = $_POST["quantity"];
            $sale = $_POST["sale"];
            $plus = $_POST["plus"];
            $id_payment_status  = $_POST["payment"];
            $id_from_where = $_POST["from"];
            $note = $_POST["note"];
            // var_dump($id_user_sell); die();

            // Gọi ra số tiền của loại sản phẩm
            $queryPro = $conn -> prepare("SELECT * FROM tbl_product WHERE id = :id_product");
            $queryPro->bindParam(':id_product',$id_product, PDO::PARAM_STR);
            $queryPro->execute();
            $resultsPro = $queryPro->fetch(PDO::FETCH_OBJ);
            $price = $resultsPro -> price;

            // Tổng tiền
            if($id_payment_status == 5){
                $total = 0;
            }else{
                $total = $price * $quantity - $sale + $plus;
            }

            $queryWare= $conn -> prepare("UPDATE tbl_sell_manage SET id_brand = :id_brand, id_user_sell = :id_user_sell, date = :date, id_product = :id_product, quantity = :quantity, sale = :sale, plus = :plus, total = :total, id_payment_status = :id_payment_status , id_from_where = :id_from_where, id_user = :id_user, note = :note WHERE id = :id ");
            $queryWare->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare->bindParam(':id_user_sell',$id_user_sell,PDO::PARAM_STR);
            $queryWare->bindParam(':date',$date,PDO::PARAM_STR);
            $queryWare->bindParam(':id_product',$id_product,PDO::PARAM_STR);
            $queryWare->bindParam(':quantity',$quantity,PDO::PARAM_STR);
            $queryWare->bindParam(':sale',$sale,PDO::PARAM_STR);
            $queryWare->bindParam(':plus',$plus,PDO::PARAM_STR);
            $queryWare->bindParam(':total',$total,PDO::PARAM_STR);
            $queryWare->bindParam(':id_payment_status',$id_payment_status ,PDO::PARAM_STR);
            $queryWare->bindParam(':id_from_where',$id_from_where,PDO::PARAM_STR);
            $queryWare->bindParam(':id_user',$id_user,PDO::PARAM_STR);
            $queryWare->bindParam(':note',$note,PDO::PARAM_STR);
            $queryWare->bindParam(':id',$id_sell,PDO::PARAM_STR);
            $queryWare-> execute();
            if($queryWare){
                $msg = "Đơn đã được cập nhật chỉnh sửa!";
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
    <title>Admin | Bảng điều khiển</title>
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
                    <h1>Nhập đơn hàng</h1>
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
                            <option value="<?php echo $resultsSell -> id_brand ?>"><?php echo $resultsSell -> brandName ?></option>
                            <?php if($id_power != 3){ ?>
                                <?php foreach ($resultsBrand as $key => $value) { ?>
                                    <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                                <?php } }else{?>
                                    <option value="<?php echo $resultsBrandId -> id ?>" selected><?php echo $resultsBrandId -> name ?></option>
                                <?php } ?>
                            
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Người bán <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="user" id="user">
                            <option value="<?php echo $resultsSell -> id_user_sell ?>"><?php echo $resultsSell -> fullname ?></option>
                            <?php foreach ($resultsUser as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"<?php echo(($value -> id) == $id_user)?"selected":"" ?>><?php echo $value -> fullname ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ngày <span class="col-red">*</span></p>
                        <input type="date" name="date" id="date" class=" form-focus boder-ra-5" value ="<?php echo $resultsSell -> date ?>" >
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Chọn sản phẩm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="product" id="product" onChange="selectProd()">
                            <option value="<?php echo $resultsSell -> id_product ?>"><?php echo $resultsSell -> product ?></option>
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
                        <p class="item-name">Số lượng bán <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "quantity" id="quantity" value="<?php echo $resultsSell -> quantity ?>" placeholder = "" onchange = "checkQuantity()">
                        <p class="form-message" id ="quantity-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Giảm giá/trừ</p>
                        <input type="number" class="form-focus boder-ra-5" name = "sale" id="sale" value="<?php echo $resultsSell -> sale ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Cộng thêm</p>
                        <input type="number" class="form-focus boder-ra-5" name = "plus" id="plus" value="<?php echo $resultsSell -> plus ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Chọn hình thức thanh toán <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="payment" id="payment" onchange="totalPayment()">
                            <option value="<?php echo $resultsSell -> id_payment_status ?>"><?php echo $resultsSell -> payment ?></option>
                            <?php foreach ($resultsPay as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator" id = "total_payment">
                        <!-- code js đẩy vào -->
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Đơn từ đâu <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="from" id="from" onChange="selectProd()">
                             <option value="<?php echo $resultsSell -> id_from_where ?>"><?php echo $resultsSell -> fromWhere ?></option>
                            <?php foreach ($resultsFrom as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"><?php echo $resultsSell -> note ?></textarea>
                        <p class="form-message"></p>
                    </div>

                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit" id = "submits"  value="Tạo đơn" style = "width: 100%;height: 45px;font-size: 18px;">
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
                Validator.isRequired('#user', 'Vui lòng chọn người bán'),
                Validator.isRequired('#date', 'Vui lòng chọn ngày bán'),
                Validator.isRequired('#quantity', 'Vui lòng nhập số lượng'),
                Validator.isRequired('#payment', 'Vui lòng chọn hình thức TT'),
                Validator.isRequired('#from', 'Vui lòng chọn đơn từ đâu'),
            ],
        });
    </script>

    <!-- kiểm tra số lượng xuất ra-->
    <script>
        function checkQuantity(){
            var brand = $("#brand").val();
            var product = $("#product").val();
            var quantity= $("#quantity").val();
            jQuery.ajax({
            url: "./include/get-quantity-cancel.php?brand="+ brand + "&product="+ product+ "&quantity=" + quantity,
            success: function(data) {
                $("#quantity-message").html(data);
            },
            error: function() {}
            });
        };
    </script>

    <!-- Tính tổng tiền-->
    <script>
        function totalPayment(){
            var product = $("#product").val();
            var quantity = $("#quantity").val();
            var sale = $("#sale").val();
            var plus = $("#plus").val();
            var payment = $("#payment").val();
            
            jQuery.ajax({
            url: "./include/get-sell-import.php?quantity="+ quantity + "&sale="+ sale + "&plus=" + plus+ "&payment=" + payment + "&product=" + product,
            success: function(data) {
                $("#total_payment").html(data);
            },
            error: function() {}
            });
        };
    </script>

    <!-- select tìm kiếm -->
    <script>
        $(document).ready(function() { 
            $("#product").select2({
                placeholder: "Chọn sản phẩm",
                allowClear: true
            }); 
             $("#payment").select2({
                placeholder: "Chọn hình thức TT",
                allowClear: true
            }); 
            $("#from").select2({
                placeholder: "Chọn nguồn đơn",
                allowClear: true
            }); 
            
        });
    </script>
    <!-- Hiện chi tiết bài viết -->
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
</body>
</html>