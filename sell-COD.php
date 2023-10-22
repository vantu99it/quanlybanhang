<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $hs = " 00:00:00";
        $he = " 23:59:59";
        $err = "";
        $ok = "";
        $message = "";
        $today = date('Y-m-d 23:59:59');
        $previous_date = date('Y-m-d 00:00:00', strtotime('-60 days', strtotime($today)));

        $today_fill = date('Y-m-d');
        $previous_date_fill = date('Y-m-d', strtotime('-60 days', strtotime($today)));

        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];
        if(isset($_GET['brand'])){
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $fromDate = $_POST["from-date"].$hs;
                $toDate = $_POST["to-date"].$he;

                $fromDate_fill = $_POST["from-date"];
                $toDate_fill = $_POST["to-date"];

                $id_brand = $_GET['brand'];

                $queryCOD= $conn -> prepare("SELECT cod.*, sell.* FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id WHERE  sell.id_brand = :id_brand AND sell.created_ad <= :toDate AND sell.created_ad >= :fromDate ORDER BY sell.created_ad DESC");
                $queryCOD->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
                $queryCOD->bindParam('toDate',$toDate,PDO::PARAM_STR);
                $queryCOD->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
                $queryCOD-> execute();
                $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
            }
            else{
                $id_brand = $_GET['brand'];
                $queryCOD= $conn -> prepare("SELECT cod.*, sell.* FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id WHERE sell.id_brand = :id_brand AND sell.created_ad <= :today AND sell.created_ad >= :previous_date ORDER BY sell.created_ad DESC");
                $queryCOD->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
                $queryCOD->bindParam('today',$today,PDO::PARAM_STR);
                $queryCOD->bindParam('previous_date',$previous_date,PDO::PARAM_STR);
                $queryCOD-> execute();
                $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
            }
        }else{
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $fromDate = $_POST["from-date"].$hs;
                $toDate = $_POST["to-date"].$he;

                $fromDate_fill = $_POST["from-date"];
                $toDate_fill = $_POST["to-date"];

                $queryCOD= $conn -> prepare("SELECT cod.*, sell.* FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id WHERE sell.created_ad <= :toDate AND sell.created_ad >= :fromDate ORDER BY sell.created_ad DESC");
                $queryCOD->bindParam('toDate',$toDate,PDO::PARAM_STR);
                $queryCOD->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
                $queryCOD-> execute();
                $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
            }
            else{
                $queryCOD= $conn -> prepare("SELECT cod.*, sell.* FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id WHERE sell.created_ad <= :today AND sell.created_ad >= :previous_date ORDER BY sell.created_ad DESC");
                $queryCOD->bindParam('today',$today,PDO::PARAM_STR);
                $queryCOD->bindParam('previous_date',$previous_date,PDO::PARAM_STR);
                $queryCOD-> execute();
                $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
            }
        }
    }
    $sum=0;
    foreach ($resultsCOD as $key => $value) {
        $sum += (int) $value->total;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Quản lý đơn COD</title>
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
            <section class="main-right-title" style = "margin-bottom: 5px;">
                <div class="form-title">
                    <h1>Quản lý COD</h1>
                </div>
                <div class="account-btn">
                    <a href="./sell-import.php" class="btn btn-post btn-add">Nhập đơn</a>
                </div>
            </section>
            <section class="main-right-filter">
                <div class="account-btn">
                    <a href="./sell-COD.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >Tất cả</a>
                </div>
                <div class="account-btn">
                    <a href="./sell-COD.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Cơ sở 1</a>
                </div>
                <div class="account-btn">
                    <a href="./sell-COD.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
                </div>
                <div class="account-btn full-screen">
                    <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xuất excel</button>
                </div>
                <div  div class="account-btn">
                    <button class="btn btn-post btn-add filter-btn" id = "filter-btn">Lọc</button>
                </div>
            </section>
            <section class="main-right-filter filter-none">
                <form action="" method="post">
                    <span>Từ</span>
                    <input type="date" name="from-date" id="from-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($fromDate_fill)? $fromDate_fill : $previous_date_fill ?>">
                    <span>Đến</span>
                    <input type="date" name="to-date" id="to-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($toDate_fill)? $toDate_fill :$today_fill ?>">
                    <input type="submit" value="Lọc" class="btn btn-post btn-add">
                </form>
            </section>
            <section class="main-right-filter">
                <p>Tổng tiền: <b class="col-red">
                    <?php 
                        $bien = number_format($sum,0,",",".");
                        echo $bien."đ";
                    ?>
                </b></p>
                <div class="question">
                    <i class="fa-regular fa-circle-question"></i>
                    <div class="question-info">
                        <p>Giải thích</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Màu</th>
                                    <th>Ý nghĩa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color: #ccc;"></i> </td>
                                    <td>Đang lên đơn</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color:#f5f5f5 ;"></i> </td>
                                    <td>Đang vận chuyển</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color:#81D9EF;"></i> </td>
                                    <td>Đang giao</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color: #31bf03;"></i> </td>
                                    <td>Đã giao</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color: #F2D77D;"></i> </td>
                                    <td>Lưu kho</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid fa-circle" style = "color: #FF6161;"></i> </td>
                                    <td>Hoàn về</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Ngày bán</th>
                            <th>ĐVVC</th>
                            <th>Vận đơn</th>
                            <th>SĐT</th>
                            <th>Thông tin</th>
                            <th>Tình trạng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsCOD as $key => $value) { 
                            $id_payment_status = $value->id_payment_status;
                            $payment = "";
                            $status = $value->status;
                            $status_info = "";
                            $bg_tr = "";
                            switch ($id_payment_status){
                                case 3:
                                    $payment = "COD Viettel";
                                    break;
                                case 4:
                                    $payment = "COD GHTK";
                                    break;
                                default:
                                    break;
                            }
                            switch ($status) {
                                case 0:
                                    $status_info = "Lên đơn";
                                    $bg_tr = "#ccc";
                                    break;
                                case 2:
                                    $status_info = "Đang giao";
                                    $bg_tr = "#81D9EF";
                                    break;
                                case 3:
                                    $status_info = "Đã giao";
                                    $bg_tr = "#31bf03";
                                    break;
                                case 4:
                                    $status_info = "Lưu kho";
                                    $bg_tr = "#F2D77D";
                                    break;
                                case 5:
                                    $status_info = "Hoàn về";
                                    $bg_tr = "#FF6161";
                                    break;
                                default:
                                    $status_info = "Đang vận chuyển";
                                    break;
                            }
                            ?>
                             
                            <tr style = "background: <?php echo $bg_tr ?>" > 
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create( $value -> date),"d-m")
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p><?php echo $payment?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> cod?></p>
                                </td>
                                <td>
                                    <a href="tel:+<?php echo $value -> phone?>" style = "color: #000"><b><?php echo $value -> phone?></b></a>
                                </td>
                                <td>
                                    <p><?php echo $value -> information_line?></p>
                                </td>
                                <td>
                                     <p><b><?php echo $status_info ?></b></p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./sell-cod-edit.php?id=<?php echo $value -> id_cod ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>
                                   
                                    <button class="post-button-cod" data-id="<?php echo $value -> id ?>" style = "margin: 0 5px; border: none;"  >
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /main-right -->
    </div>
    <div id="result">
        
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->

    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "xuatkho.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>