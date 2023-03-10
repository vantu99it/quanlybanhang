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
        
        //sản phẩm
        $queryProd= $conn -> prepare("SELECT * FROM tbl_product WHERE status = 1");
        $queryProd-> execute();
        $resultsProd = $queryProd->fetchAll(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id_product = $_POST["product"];
            $classify = $_POST["classify"];
            if(isset($_FILES["upload-img"])){
                $imagePNG = $_FILES["upload-img"]["name"];
                $imageName = vn2en($imagePNG);  
                $target_dir = "./image/";
                $target_file = $target_dir.$imageName;
                move_uploaded_file($_FILES["upload-img"]["tmp_name"],'./image/'.$imageName);       
            }
            $date_start = $_POST["dateStart"];
            $date_end = $_POST["dateEnd"];
            $note = $_POST["note"];

            $price_sale = isset($_POST["price-news"]) ? $_POST["price-news"]: 0;
            $id_product_sale = isset($_POST["product-sale"]) ? $_POST["product-sale"]: 0;
            $quantity = isset($_POST["numSale"]) ? $_POST["numSale"]: 0;
            

            $querySale= $conn -> prepare("INSERT INTO tbl_sale (id_product, classify, price_sale, id_product_sale, quantity, image, date_start, date_end, note) value (:id_product, :classify, :price_sale, :id_product_sale, :quantity, :image, :date_start, :date_end, :note )");
            $querySale->bindParam(':id_product',$id_product,PDO::PARAM_STR);
            $querySale->bindParam(':classify',$classify,PDO::PARAM_STR);
            $querySale->bindParam(':price_sale',$price_sale,PDO::PARAM_STR);
            $querySale->bindParam(':id_product_sale',$id_product_sale,PDO::PARAM_STR);
            $querySale->bindParam(':quantity',$quantity,PDO::PARAM_STR);
            $querySale->bindParam(':image',$target_file,PDO::PARAM_STR);
            $querySale->bindParam(':date_start',$date_start,PDO::PARAM_STR);
            $querySale->bindParam(':date_end',$date_end,PDO::PARAM_STR);
            $querySale->bindParam(':note',$note,PDO::PARAM_STR);
            $querySale-> execute();
            $lastInsertId = $conn->lastInsertId();
            if($lastInsertId){
                $msg = "Khởi tạo thành công!";
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
            <form action="" method="post" id = "frm-post" enctype="multipart/form-data">
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
                    <div class="search-item form-validator">
                        <p class="item-name">Loại khuyễn mãi<span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="classify" id="classify" onChange="selectSale()">
                            <option value="">Chọn loại áp dụng</option>
                            <option value="1">Giảm giá</option>
                            <option value="2">Tặng kèm</option>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div id="addSale">
                       
                    </div>
                    
                    <div class="input-file form-validator">
                        <p class="item-name">Tải ảnh lên <span class="col-red">*</span></p>
                        <div style = "height: 40px;">
                            <div class="input-img">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                Tải ảnh
                                <input type="file" class="upload-img" name="upload-img" id="upload-img" onchange = "ImageFileAsUrl()">
                            </div>
                        </div>
                        <div id="display-img">
                        </div>
                        <div id = "remove" >
                            <!-- btn-xóa ảnh -->
                        </div>
                        <span class="form-message"></span>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Thời gian bắt đầu<span class="col-red">*</span></p>
                        <input type="date" class="form-focus boder-ra-5" name = "dateStart" id="dateStart" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Thời gian kết thúc<span class="col-red">*</span></p>
                        <input type="date" class="form-focus boder-ra-5" name = "dateEnd" id="dateEnd" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>

                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit" id = "submits"  value="KHỞI TẠO" style = "width: 100%;height: 45px;font-size: 18px;">
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
                Validator.isRequired('#product', 'Vui lòng chọn sản phẩm'),
                Validator.isRequired('#classify', 'Vui lòng chọn loại KM'),
                Validator.isRequired('#upload-img', 'Vui lòng tải ảnh lên'),
                Validator.isRequired('#dateStart', 'Vui lòng chọn ngày'),
                Validator.isRequired('#dateEnd', 'Vui lòng ngày'),
                // Validator.isRequired('#price-news', 'Vui lòng số tiền giảm'),
                // Validator.numberMin('#price-news', 1000, 'Số tiền phải >= 1.000đ'),
                // Validator.isRequired('#product-sale', 'Vui lòng chọn SP tặng kèm'),
                // Validator.isRequired('#numSale', 'Vui lòng nhập số lượng'),

            ],
        });
    </script>

    <!-- select tìm kiếm -->
    <script>
        $(document).ready(function() { 
            $("#product").select2({
                placeholder: "Chọn sản phẩm",
                allowClear: true
            }); 
            $("#classify").select2({
                placeholder: "Chọn loại khuyễn mãi",
                allowClear: true
            }); 
            
        });
    </script>
    <!-- Hiện form nhập -->
    <script>
        function selectSale(){
            var sale = $("#classify").val();
            var product = $("#product").val();
            jQuery.ajax({
                url: "./include/get-sale.php?pro="+ product+ "&sale=" + sale,
                success: function(data) {
                    var city = data;
                    console.log(city);
                    $("#addSale").html(data);
                    $("#product-sale").select2({
                    placeholder: "Chọn sản phẩm",
                    allowClear: true
                    }); 
                },
                error: function() {}
            });
            
        };
    </script>
    <!-- Hiện chi tiết bài viết -->
    <script>
        function selectProd(){
            jQuery.ajax({
                url: "./include/get-product-ware.php",
                data: 'prod=' + $("#product").val(),
                type: "POST",
                success: function(data) {
                    // var city = data;
                    // console.log(city);
                    $("#nameProduct").html(data);
                },
                error: function() {}
            });
        };
    </script>
    <script>
        function selectProdSale(){
            jQuery.ajax({
                url: "./include/get-product-ware.php",
                data: 'prod=' + $("#product-sale").val(),
                type: "POST",
                success: function(data) {
                    // var city = data;
                    // console.log(city);
                    $("#nameProductSale").html(data);
                },
                error: function() {}
            });
        };
    </script>
    <script>
        // Hiện ảnh
        function ImageFileAsUrl() {
        var fileSelected = document.getElementById("upload-img").files;
        // console.log(fileSelected.length);
        if (fileSelected.length > 0) {
            for (var i = 0; i < fileSelected.length; i++) {
            var fileToLoad = fileSelected[i];
            var fileReader = new FileReader();
            fileReader.onload = function (fileLoaderEvent) {
                var srcData = fileLoaderEvent.target.result;
                var newImage = document.createElement("img");
                newImage.src = srcData;
                newImage.id = "js-remove-img";
                document.getElementById("display-img").appendChild(newImage);
                document.getElementById(
                "remove"
                ).innerHTML = `<a onclick="removeImg()" class="btn" id ="delete-btn">Xóa ảnh</a>`;
            };
            fileReader.readAsDataURL(fileToLoad);
            }
        }
        }
        function removeImg() {
        const element = document.getElementById("js-remove-img");
        const delete_btn = document.getElementById("delete-btn");
        element.remove();
        delete_btn.remove();
        }
    </script>
    <script>
        $(document).ready(function () {
            dateStart.min = new Date().toISOString().split("T")[0];
            dateEnd.min = new Date().toISOString().split("T")[0];
        });
    </script>
</body>
</html>