<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];
        if(isset($_GET['brand'])){
            $id_brand = $_GET['brand'];

            $queryCOD= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname, br.name AS brand FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where JOIN tbl_brand br ON br.id = sell.id_brand WHERE pay.type = 'COD' AND sell.id_brand = id_brand ORDER BY sell.date ASC, sell.id ASC");
            $queryCOD->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryCOD-> execute();
            $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
        }else{
            $queryCOD= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname, br.name AS brand FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where JOIN tbl_brand br ON br.id = sell.id_brand WHERE pay.type = 'COD' ORDER BY sell.date ASC, sell.id ASC");
            $queryCOD-> execute();
            $resultsCOD = $queryCOD->fetchAll(PDO::FETCH_OBJ);
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
    <title>Admin | Qu???n l?? ????n COD</title>
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
                    <h1>Qu???n l?? COD</h1>
                </div>
                <div class="account-btn">
                    <a href="./sell-import.php" class="btn btn-post btn-add">Nh???p ????n</a>
                </div>
            </section>
            <section class="main-right-filter">
                <div class="account-btn">
                    <a href="./sell-COD.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >T???t c???</a>
                </div>
                <div class="account-btn">
                    <a href="./sell-COD.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">C?? s??? 1</a>
                </div>
                <div class="account-btn">
                    <a href="./sell-COD.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">C?? s??? 2</a>
                </div>
                <div class="account-btn full-screen">
                    <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xu???t excel</button>
                </div>
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
                            <th class = "full-screen">C?? s???</th>
                            <th class = "full-screen">Ghi ch??</th>
                            <th>H??nh ?????ng</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsCOD as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create( $value -> date),"d-m")
                                        ?>
                                    </p>
                                </td>
                                <td style = "text-align: left;">
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
                                    <p><?php echo $value -> brand ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./edit-user.php?id=<?php echo $value -> id ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                   <?php if($id_power != 3){ ?>
                                        <a href="./categories.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('B???n ch???c ch???n mu???n x??a?');" ><i class="fa-solid fa-trash"></i>
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