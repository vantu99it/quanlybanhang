<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];

        $queryClass= $conn -> prepare("SELECT * FROM tbl_classify WHERE status = 1");
        $queryClass-> execute();
        $resultsClass = $queryClass->fetchAll(PDO::FETCH_OBJ);

        $queryUnit= $conn -> prepare("SELECT * FROM tbl_unit WHERE status = 1");
        $queryUnit-> execute();
        $resultsUnit = $queryUnit->fetchAll(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $classify = $_POST['classify'];
            $name = $_POST['name'];
            $detail = $_POST['detail'];
            $unit = $_POST['unit'];
            $num_unit = $_POST['num-unit'];
            $price = $_POST['price'];
            $note = $_POST['note'];

            // var_dump($note); die();

            $queryCheck= $conn -> prepare("SELECT * FROM tbl_product WHERE name = :name AND status = 1");
            $queryCheck->bindParam(':name',$name,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);

            if($queryCheck->rowCount() <= 0){

                $queryProd= $conn -> prepare("INSERT INTO tbl_product (name, detail, id_classify, id_unit, num_unit, price, note, id_user ) value (:name, :detail, :id_classify, :id_unit, :num_unit, :price, :note, :id_user ) ");
                $queryProd->bindParam(':name',$name,PDO::PARAM_STR);
                $queryProd->bindParam(':detail',$detail,PDO::PARAM_STR);
                $queryProd->bindParam(':id_classify',$classify,PDO::PARAM_STR);
                $queryProd->bindParam(':id_unit',$unit,PDO::PARAM_STR);
                $queryProd->bindParam(':num_unit',$num_unit,PDO::PARAM_STR);
                $queryProd->bindParam(':price',$price,PDO::PARAM_STR);
                $queryProd->bindParam(':note',$note,PDO::PARAM_STR);
                $queryProd->bindParam(':id_user',$id_user,PDO::PARAM_STR);
                $queryProd-> execute();
                $results = $queryProd->fetchAll(PDO::FETCH_OBJ);
                $lastInsertId = $conn->lastInsertId();
                if($lastInsertId){
                    $msg = "Tạo sản phẩm thành công!";
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
            }else{
                $error = "Tên sản phẩm này đã tồn tại!";
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
                    <h1>Tạo mới sản phẩm</h1>
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
                    <div class="search-item form-validator">
                        <p class="item-name">Loại sản phẩm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="classify" id="classify">
                            <option value="">Chọn loại sản phẩm</option>
                            <?php foreach ($resultsClass as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Tên sản phẩm <span class="col-red">*</span></p>
                        <input type="text" class="form-focus boder-ra-5" name = "name" id="name" value="" placeholder = "Nhập tên">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Chi tiết sản phẩm <span class="col-red">*</span></p>
                        <textarea name="detail" id="detail" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Đơn vị <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="unit" id="unit">
                            <option value="">Chọn đơn vị</option> 
                            <?php foreach ($resultsUnit as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Số lương/đơn vị <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "num-unit" id="num-unit" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Giá bán <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "price" id="price" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit"  value="Tạo mới" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#classify', 'Vui lòng chọn loại sản phẩm'), 
                Validator.isRequired('#name', 'Vui lòng nhập tên sản phẩm'),
                Validator.isRequired('#detail', 'Vui lòng nhập chi tiết'),
                Validator.isRequired('#unit', 'Vui lòng chọn đơn vị'),
                Validator.isRequired('#num-unit', 'Vui lòng nhập SL/ĐV'),
                Validator.isRequired('#price', 'Vui lòng nhập giá bán'),
            ],
        });
    </script>
    <script>
        $(document).ready(function() { 
            // Loại sản phẩm
            $("#classify").select2({
                placeholder: 'Chọn loại sản phẩm',
                allowClear: true
            }); 
            // đơn vị
            $("#unit").select2({
                placeholder: 'Chọn đơn vị',
                allowClear: true
            }); 
        });
    </script>
</body>
</html>