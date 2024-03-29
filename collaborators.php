<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        // $id_brand = $_SESSION['logins']['id_brand'];

        if(date('d')<13){
            $monthFrom =(int) date('m')-1;
            $monthFrom = ($monthFrom < 10) ? "0".$monthFrom : $monthFrom;
            $monthTo =(int) date('m');
            $monthTo = ($monthTo < 10) ? "0".$monthTo : $monthTo;
        }else{
            $monthFrom =(int) date('m');
            $monthFrom = ($monthFrom < 10) ? "0".$monthFrom : $monthFrom;
            $monthTo =(int) date('m')+1;
            $monthTo = ($monthTo < 10) ? "0".$monthTo : $monthTo;
        }
        $year =date('Y');
        
        if(isset($_GET['brand']) && $_GET['brand'] == 1){
            $fromDate = $year."-".$monthFrom."-14";
            $toDate = $year."-".$monthTo."-13";
        }else{
             $fromDate = $year."-".$monthFrom."-22";
            $toDate = $year."-".$monthTo."-21";
        }

        // var_dump($fromDate);
        // var_dump($toDate); die();


        $err = "";
        $ok = "";
        $message = "";
        $id_brand =$_GET['brand'];

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $fromDate = $_POST["from-date"];
            $toDate = $_POST["to-date"];
            $id_brand =$_GET['brand'];
            // var_dump($fromDate);

            $querySell= $conn -> prepare("SELECT sell.*, frm.name as from_where, pro.name AS product, pay.name AS payment, us.fullname as fullname FROM tbl_sell_manage sell JOIN tbl_from_where frm ON frm.id = sell.id_from_where JOIN tbl_product pro ON pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_user us ON us.id = sell.id_user WHERE frm.slug = 'CTV' AND sell.date <= :toDate AND sell.date >= :fromDate AND sell.id_brand = :id_brand ORDER BY sell.date ASC" );
            $querySell->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $querySell->bindParam(':fromDate',$fromDate,PDO::PARAM_STR);
            $querySell->bindParam(':toDate',$toDate,PDO::PARAM_STR);
            $querySell-> execute();
            $resultsSell = $querySell->fetchAll(PDO::FETCH_OBJ);
        }else{
            $querySell= $conn -> prepare("SELECT sell.*, frm.name as from_where, pro.name AS product, pay.name AS payment, us.fullname as fullname FROM tbl_sell_manage sell JOIN tbl_from_where frm ON frm.id = sell.id_from_where JOIN tbl_product pro ON pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_user us ON us.id = sell.id_user WHERE frm.slug = 'CTV' AND sell.date <= :toDate AND sell.date >= :fromDate AND sell.id_brand = :id_brand ORDER BY sell.date ASC" );
            $querySell->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $querySell->bindParam(':fromDate',$fromDate,PDO::PARAM_STR);
            $querySell->bindParam(':toDate',$toDate,PDO::PARAM_STR);
            $querySell-> execute();
            $resultsSell = $querySell->fetchAll(PDO::FETCH_OBJ);

        }

        $sum=0;
        foreach ($resultsSell as $key => $value) {
            $sum += (int) $value->total;
        } 

        // Xóa
       
        if(isset($_REQUEST['del'])&&($_REQUEST['del'])){
            $delId = intval($_GET['del']);
            
            $query= $conn -> prepare("DELETE FROM tbl_sell_manage WHERE id = :id");
            $query->bindParam(':id',$delId,PDO::PARAM_STR);
            $query->execute();
            if($query){
                $ok = 1;
                $message = "Đã xóa thành công";
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
    <title>Admin | Đơn CTV</title>
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
                    <h1>Đơn CTV <?php echo $_GET['brand'] ?></h1>
                </div>
            </section>
            <section class="main-right-filter">
                <div class="account-btn">
                    <a href="./collaborators.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Cơ sở 1</a>
                </div>
                <div class="account-btn">
                    <a href="./collaborators.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
                </div>
                <form action="" method="post">
                    <span>Từ</span>
                    <input type="date" name="from-date" id="from-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($fromDate)? $fromDate : $today ?>">
                    <span>Đến</span>
                    <input type="date" name="to-date" id="to-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($toDate)? $toDate :$today ?>">
                    <input type="submit" value="Lọc" class="btn btn-post btn-add">
                </form>
                <?php if($id_power != 3){ ?>
                    <div class="account-btn full-screen">
                        <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xuất excel</button>
                    </div>
                <?php } ?>
            </section>
            <section class="main-right-filter">
                <p>Tổng tiền: <b class="col-red">
                    <?php 
                        $bien = number_format($sum,0,",",".");
                        echo $bien."đ";
                    ?>
                </b></p>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Ngày bán</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giảm</th>
                            <th>Cộng</th>
                            <th>Tình trạng</th>
                            <th>Tổng</th>
                            <th>Nguồn</th>
                            <th class = "full-screen">Người bán</th>
                            <th class = "full-screen">Ghi chú</th>
                            <th>Hành động</th>
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
                                            echo $bien."đ";
                                        ?>
                                     </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $tien = (int) $value -> plus;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."đ";
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
                                            echo $bien."đ";
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
                                        <a href="./sell-manage.php?brand=<?php echo $id_brand?>&del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
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
            <a href="./sell-manage.php?brand=<?php echo $id_brand ?>" class="btn">OK</a>
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
            <a href="./sell-manage.php?brand=<?php echo $id_brand ?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "topsoluong.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>