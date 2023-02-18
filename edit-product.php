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

        //gọi ra loại sản phẩm
        $queryClass= $conn -> prepare("SELECT * FROM tbl_classify WHERE status = 1");
        $queryClass-> execute();
        $resultsClass = $queryClass->fetchAll(PDO::FETCH_OBJ);

        //gọi ra đơn vị sản phẩm
        $queryUnit= $conn -> prepare("SELECT * FROM tbl_unit WHERE status = 1");
        $queryUnit-> execute();
        $resultsUnit = $queryUnit->fetchAll(PDO::FETCH_OBJ);
       
        if(isset($_GET['id'])){
            $id_pro = isset($_GET['id']) ? $_GET['id'] : "";

            // gọi ra thông tin sản phẩm theo id
            $queryPro= $conn -> prepare("SELECT pro.*, unit.name AS name_unit, class.name AS name_classify FROM tbl_product pro JOIN tbl_classify class ON class.id = pro.id_classify JOIN tbl_unit unit ON unit.id = pro.id_unit WHERE pro.id = :id");
            $queryPro->bindParam(':id',$id_pro,PDO::PARAM_STR);
            $queryPro-> execute();
            $resultsPro = $queryPro->fetch(PDO::FETCH_OBJ);
        
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $classify = $_POST['classify'];
                $name = $_POST['name'];
                $detail = $_POST['detail'];
                $unit = $_POST['unit'];
                $num_unit = $_POST['num-unit'];
                $price = $_POST['price'];
                $note = $_POST['note'];
                $status = $_POST['status'];

                //update 
                $queryProd= $conn -> prepare("UPDATE tbl_product SET name = :name, detail = :detail, id_classify = :id_classify, id_unit = :id_unit, num_unit = :num_unit, price = :price, note = :note, id_user = :id_user, status = :status WHERE  id = :id");
                $queryProd->bindParam(':name',$name,PDO::PARAM_STR);
                $queryProd->bindParam(':detail',$detail,PDO::PARAM_STR);
                $queryProd->bindParam(':id_classify',$classify,PDO::PARAM_STR);
                $queryProd->bindParam(':id_unit',$unit,PDO::PARAM_STR);
                $queryProd->bindParam(':num_unit',$num_unit,PDO::PARAM_STR);
                $queryProd->bindParam(':price',$price,PDO::PARAM_STR);
                $queryProd->bindParam(':note',$note,PDO::PARAM_STR);
                $queryProd->bindParam(':id_user',$id_user,PDO::PARAM_STR);
                $queryProd->bindParam(':status',$status,PDO::PARAM_STR);
                $queryProd->bindParam(':id',$id_pro,PDO::PARAM_STR);
                $queryProd-> execute();
                if($queryProd){
                    $ok = 1;
                    $message = "Đã cập nhật thành công!";
                }
                else{
                    $err = 1;
                    $message = "Có lỗi xảy ra, vui lòng thử lại";
                }
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
    <title>Admin | Chỉnh sửa sản phẩm</title>
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
                    <h1>Chỉnh sửa sản phẩm</h1>
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
                            <option value="<?php echo $resultsPro -> id_classify ?>"><?php echo $resultsPro -> name_classify ?></option>
                            <?php foreach ($resultsClass as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Tên sản phẩm <span class="col-red">*</span></p>
                        <input type="text" class="form-focus boder-ra-5" name = "name" id="name" value="<?php echo $resultsPro -> name ?>" placeholder = "Nhập tên">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Chi tiết sản phẩm <span class="col-red">*</span></p>
                        <textarea name="detail" id="detail" cols="10" rows="5" class="form-focus boder-ra-5 textarea"><?php echo $resultsPro -> detail ?></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Đơn vị <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="unit" id="unit">
                            <option value="<?php echo $resultsPro -> id_unit ?>"><?php echo $resultsPro -> name_unit ?></option>
                            <?php foreach ($resultsUnit as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Số lương/đơn vị <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "num-unit" id="num-unit" value="<?php echo $resultsPro -> num_unit ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Giá bán <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "price" id="price" value="<?php echo $resultsPro -> price ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"><?php echo $resultsPro -> note ?></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="status" style = "margin-top: 10px;">
                        <p class="item-name" style = "font-size: 16px; font-weight: 700; margin-bottom: 5px">Trạng thái hiển thị </p>
                        <label style = "margin-right: 15px;">
                            <input type="radio" name="status" id="" value ="1" <?php if($resultsPro->status == 1) echo  "checked = 'Checked'" ?>> Hiện
                        </label>
                        <label>
                            <input type="radio" name="status" value ="0" <?php if($resultsPro->status == 0) echo  "checked = 'Checked'" ?> id="" > Ẩn
                        </label>
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
            <a href="./manage-product.php" class="btn">OK</a>
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
            <a href="./edit-product.php?id=<?php echo $id_pro ?>" class="btn">OK</a>        
        </div>
    </div>
    <?php }?>


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