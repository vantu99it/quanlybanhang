<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        // $id_brand = $_SESSION['logins']['id_brand'];
        $today = date('Y-m-d');

        $err = "";
        $ok = "";
        $message = "";


        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $fromDate = $_POST["from-date"];
            $toDate = $_POST["to-date"];
            $id_brand =$_GET['brand'];
            // var_dump($fromDate);

            $querySell= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where WHERE sell.id_brand = :id_brand AND sell.date >= :fromDate AND sell.date <= :toDate ORDER BY sell.date DESC, sell.id ASC" );
            $querySell->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $querySell->bindParam(':fromDate',$fromDate,PDO::PARAM_STR);
            $querySell->bindParam(':toDate',$toDate,PDO::PARAM_STR);
            $querySell-> execute();
            $resultsSell = $querySell->fetchAll(PDO::FETCH_OBJ);

        }else{
            if(isset($_GET['brand']) && !isset($_GET['day']) || isset($_GET['brand']) && isset($_GET['day']) && $_GET['day'] == "today" ){
                $id_brand =$_GET['brand'];
                $querySell= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where WHERE sell.id_brand = :id_brand AND sell.date = :today ORDER BY sell.date DESC, sell.id ASC");
                $querySell->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
                $querySell->bindParam(':today',$today,PDO::PARAM_STR);
                $querySell-> execute();
                $resultsSell = $querySell->fetchAll(PDO::FETCH_OBJ);
            }
        }
       $sum=0;
        foreach ($resultsSell as $key => $value) {
            $sum += (int) $value->total;
        } 
        // X??a
        $id_brand =$_GET['brand'];
        if(isset($_REQUEST['del'])&&($_REQUEST['del'])){
            $delId = intval($_GET['del']);
            
            $query= $conn -> prepare("DELETE FROM tbl_sell_manage WHERE id = :id");
            $query->bindParam(':id',$delId,PDO::PARAM_STR);
            $query->execute();
            if($query){
                $ok = 1;
                $message = "???? x??a th??nh c??ng";
            }
            else{
                $err = 1;
                $message = "C?? l???i x???y ra, vui l??ng th??? l???i";
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
    <title>Admin | Qu???n l?? xu???t kho</title>
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
                    <h1>Qu???n l?? ????n h??ng c?? s??? <?php echo $_GET['brand'] ?></h1>
                </div>
                <div class="account-btn">
                    <a href="./sell-import.php" class="btn btn-post btn-add">Nh???p ????n</a>
                </div>
            </section>
            <section class="main-right-filter">
                <form action="" method="post">
                    <span>T???</span>
                    <input type="date" name="from-date" id="from-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($fromDate)? $fromDate : $today ?>">
                    <span>?????n</span>
                    <input type="date" name="to-date" id="to-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($toDate)? $toDate :$today ?>">
                    <input type="submit" value="L???c" class="btn btn-post btn-add">
                </form>
                <div class="account-btn">
                    <a href="./sell-manage.php?brand=<?php echo $_GET['brand']?>&day=today" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])&&!isset($_GET['today'])&&$_GET['today']= $today){echo "btn-active";}?>" >H??m nay</a>
                </div>
                <?php if($id_power != 3){ ?>
                    <div class="account-btn full-screen">
                        <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xu???t excel</button>
                    </div>
                <?php } ?>
            </section>
            <section class="main-right-filter">
                <p>T???ng ti???n: <b class="col-red">
                    <?php 
                        $bien = number_format($sum,0,",",".");
                        echo $bien."??";
                    ?>
                </b></p>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Ng??y b??n</th>
                            <th>S???n ph???m</th>
                            <th>S??? l?????ng</th>
                            <th>Gi???m</th>
                            <th>C???ng</th>
                            <th>T??nh tr???ng</th>
                            <th>T???ng</th>
                            <th>Ngu???n</th>
                            <th class = "full-screen">Ng?????i b??n</th>
                            <th class = "full-screen">Ghi ch??</th>
                            <th>H??nh ?????ng</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsSell as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create( $value -> date),"d-m")
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p><?php echo $value -> product?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> quantity ?></p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $tien = (int) $value -> sale;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."??";
                                        ?>
                                     </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $tien = (int) $value -> plus;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."??";
                                        ?>
                                     </p>
                                </td>
                                <td>
                                    <p><?php echo $value -> payment ?></p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $tien = (int) $value -> total;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."??";
                                        ?>
                                     </p>
                                </td>
                                <td>
                                    <p><?php echo $value -> from_where ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> fullname ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./sell-edit.php?id=<?php echo $value -> id ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                   <?php if($id_power != 3){ ?>
                                        <a href="./sell-manage.php?brand=<?php echo $id_brand?>&del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('B???n ch???c ch???n mu???n x??a?');" ><i class="fa-solid fa-trash"></i>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
    <!-- Th??ng b??o th??nh c??ng -->
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
            <a href="./sell-manage.php?brand=<?php echo $id_brand ?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
    <!-- Th??ng b??o th???t b???i -->
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
            <a href="./sell-manage.php?brand=<?php echo $id_brand ?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "quanlybanhang.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>