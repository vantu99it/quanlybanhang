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
        $err = "";
        $ok ="";
        $id_act = 1;
        $id_code = $_GET['id'];

        //gọi ra thông tin đơn hàng
        $queryCod= $conn -> prepare("SELECT cod.*, fr.name AS from_where, sell.quantity AS quantity, sell.total AS total,pro.name AS product FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id JOIN tbl_from_where fr ON fr.id = sell.id_from_where JOIN tbl_product pro ON pro.id = sell.id_product WHERE cod.id_cod = :id");
        $queryCod->bindParam(':id',$id_code,PDO::PARAM_STR);
        $queryCod-> execute();
        $resultsCod = $queryCod->fetch(PDO::FETCH_OBJ);

        // gọi ra các sản phầm kèm theo
        $queryCodPro= $conn -> prepare("SELECT pro.name AS product, cod.id_cod  FROM tbl_cod cod JOIN tbl_sell_manage sell ON sell.id = cod.id_sell_manager JOIN tbl_product pro ON pro.id = sell.id_product WHERE cod.cod = :cod");
        $queryCodPro->bindParam(':cod',$resultsCod -> cod,PDO::PARAM_STR);
        $queryCodPro-> execute();
        $resultsCodPro = $queryCodPro->fetchAll(PDO::FETCH_OBJ);

        // cập nhật
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $cod_status = $_POST["cod-status"];

            $queryUpCode= $conn -> prepare("UPDATE tbl_cod SET status = :cod_status, updated_ad = :now_day WHERE cod = :cod ");
            $queryUpCode->bindParam(':cod_status',$cod_status,PDO::PARAM_STR);
            $queryUpCode->bindParam(':cod',$resultsCod -> cod,PDO::PARAM_STR);
            $queryUpCode->bindParam(':now_day',$now_day,PDO::PARAM_STR);
            $queryUpCode-> execute();
            if($queryUpCode){
                $message = "Đã cập nhật tình trạng mới!";
                $ok = 1;
            }else{
                $message = "Thất bại! Vui lòng thử lại!";
                $err = 1;
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
    <title>Admin | Chỉnh sửa cod</title>
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
                    <h1>Chỉnh sửa trạng thái đơn hàng</h1>
                </div>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <!-- info -->
                    <div class="infor-cod">
                        <h3 style = "margin-bottom: 10px;"><strong>Thông tin về đơn hàng:</strong></h3>
                        <p style = "margin-bottom: 10px;"><strong>Tên sản phẩm: </strong> <?php echo $resultsCod -> product ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Số lượng: </strong> <?php echo $resultsCod -> quantity ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Tổng tiền: </strong> <?php echo $resultsCod -> total ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Nguồn đơn: </strong><?php echo $resultsCod -> from_where ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Ngày lên đơn: </strong><?php echo date_format(date_create( $resultsCod -> created_at),"d-m-Y H:i:s")  ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Ngày cập nhật: </strong><?php echo date_format(date_create( $resultsCod -> updated_ad),"d-m-Y H:i:s")  ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Chi tiết đơn hàng: </strong> <?php echo $resultsCod -> phone;echo " - "  ;echo $resultsCod -> information_line  ?></p>
                        <p style = "margin-bottom: 10px;"><strong>Sản phẩm kèm đơn: </strong> 
                            <?php if($resultsCodPro){
                                foreach ($resultsCodPro as $key => $value) {
                                if($value -> id_cod != $id_code){
                                    echo "</br>";
                                    echo "+ ".$value -> product;
                                }
                                } 
                            }?>
                        </p>
                        <p style = "margin-bottom: 10px; color: red"><strong>Mã vận đơn: <?php echo $resultsCod -> cod ?> </strong>
                        <hr>
                    </div>

                    <?php 
                    $status =  $resultsCod -> status ;
                    $status_info = "";
                    switch ($status) {
                        case 0:
                            $status_info = "Lên đơn";
                            break;
                        case 2:
                            $status_info = "Đang giao";
                            break;
                        case 3:
                            $status_info = "Đã giao";
                            break;
                        case 4:
                            $status_info = "Lưu kho";
                            break;
                        case 5:
                            $status_info = "Hoàn về";
                            break;
                        default:
                            $status_info = "Đang vận chuyển";
                            break;
                    }
                    ?>
                    <!-- input -->
                    <div class="search-item form-validator">
                        <p class="item-name col-red">Tình trạng <span >*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="cod-status" id="cod-status">
                            <option value="<?php echo $status ?>"><?php echo $status_info ?></option>
                            <option value="0">Đang lên đơn</option>
                            <option value="1">Đang vận chuyển</option>
                            <option value="2">Đang giao</option>
                            <option value="3">Đã giao</option>
                            <option value="4">Lưu kho</option>
                            <option value="5">Hoàn đơn</option>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit" id = "submits"  value="Cập nhật" style = "width: 100%;height: 45px;font-size: 18px;">
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
            <a href="./sell-COD.php" class="btn">OK</a>
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
            <a href="/sell-cod-edit.php?id=<?php echo $_GET['id']; ?>" class="btn">OK</a>        
        </div>
    </div>
    <?php }?>

    <!-- select tìm kiếm -->
    <script>
        $(document).ready(function() { 
            $("#cod-status").select2({
                allowClear: true
            }); 
            
        });
    </script>
</body>
</html>