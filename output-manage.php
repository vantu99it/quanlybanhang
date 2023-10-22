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
                $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 AND wa.id_brand = :id_brand AND wa.created_ad <= :toDate AND wa.created_ad >= :fromDate ORDER BY wa.created_ad DESC");
                $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
                $queryWare->bindParam('toDate',$toDate,PDO::PARAM_STR);
                $queryWare->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
                $queryWare-> execute();
                $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
            }
            else{
                $id_brand = $_GET['brand'];
                $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 AND wa.id_brand = :id_brand AND wa.created_ad <= :today AND wa.created_ad >= :previous_date ORDER BY wa.created_ad DESC");
                $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
                $queryWare->bindParam('today',$today,PDO::PARAM_STR);
                $queryWare->bindParam('previous_date',$previous_date,PDO::PARAM_STR);
                $queryWare-> execute();
                $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
            }
        }else{
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $fromDate = $_POST["from-date"].$hs;
                $toDate = $_POST["to-date"].$he;

                $fromDate_fill = $_POST["from-date"];
                $toDate_fill = $_POST["to-date"];

                $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 AND wa.created_ad <= :toDate AND wa.created_ad >= :fromDate ORDER BY wa.created_ad DESC");
                $queryWare->bindParam('toDate',$toDate,PDO::PARAM_STR);
                $queryWare->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
                $queryWare-> execute();
                $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
            }
            else{
                $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 AND wa.created_ad <= :today AND wa.created_ad >= :previous_date ORDER BY wa.created_ad DESC");
                $queryWare->bindParam('today',$today,PDO::PARAM_STR);
                $queryWare->bindParam('previous_date',$previous_date,PDO::PARAM_STR);
                $queryWare-> execute();
                $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
            }
        }

        // Xóa 
        if(isset($_REQUEST['del'])&&($_REQUEST['del'])){
            $delId = intval($_GET['del']);
            $id_brand_get =$_GET['brand'];

            $query= $conn -> prepare("DELETE FROM tbl_warehouse WHERE id = :id");
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
    <title>Admin | Quản lý xuất kho</title>
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
                    <h1>Quản lý xuất kho CS<?php echo $id_brand?></h1>
                </div>
                <div class="account-btn">
                    <a href="./output-warehouse.php" class="btn btn-post btn-add">Xuất kho</a>
                </div>
            </section>
            <?php if($id_power != 3){ ?>
                <section class="main-right-filter">
                    <div class="account-btn">
                        <a href="./output-manage.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >Tất cả</a>
                    </div>
                    <div class="account-btn">
                        <a href="./output-manage.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Cơ sở 1</a>
                    </div>
                    <div class="account-btn">
                        <a href="./output-manage.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
                    </div>
                    <div class="account-btn full-screen">
                        <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xuất excel</button>
                    </div>
                    <div class="account-btn">
                        <button class="btn btn-post btn-add filter-btn" id = "filter-btn">Lọc</button>
                    </div>
                </section>
            <?php } else{?>
                <section class="main-right-filter">
                    <div class="account-btn">
                        <button class="btn btn-post btn-add filter-btn" id = "filter-btn">Lọc</button>
                    </div>
                </section>
            <?php }?>
            <section class="main-right-filter filter-none">
                <form action="" method="post">
                    <span>Từ</span>
                    <input type="date" name="from-date" id="from-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($fromDate_fill)? $fromDate_fill : $previous_date_fill ?>">
                    <span>Đến</span>
                    <input type="date" name="to-date" id="to-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo isset($toDate_fill)? $toDate_fill :$today_fill ?>">
                    <input type="submit" value="Lọc" class="btn btn-post btn-add">
                </form>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Sản phẩm</th>
                            <th class = "full-screen">Tổng</th>
                            <th>SL Xuất</th>
                            <th class = "full-screen">Kho còn</th>
                            <th class = "full-screen">Cơ sở</th>
                            <th>Người xuất</th>
                            <th>Ngày xuất</th>
                            <th class = "full-screen">Ghi chú</th>
                            <?php if($id_power != 3){ ?>
                                <th class = "noExcel">Hành động</th>
                            <?php }?>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsWare as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p><?php echo $value -> product?></p>
                                </td>
                                
                                <td class = "full-screen">
                                    <p>
                                        <?php 
                                            // Tổng số xuất ra
                                            $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_brand = :id_brand and id_product = :id_product");
                                            $queryOutput->bindParam(':id_brand',$value -> id_brand, PDO::PARAM_STR);
                                            $queryOutput->bindParam(':id_product',$value -> id_product, PDO::PARAM_STR);
                                            $queryOutput->execute();
                                            $resultsOutput = $queryOutput->fetch(PDO::FETCH_OBJ);
                                            $output= (int)$resultsOutput->total;
                                            echo $output;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p><?php echo $value -> quantity ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p>
                                        <?php 
                                            // Tổng số đã nhập vào
                                            $queryInput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 1 AND id_brand = :id_brand and id_product = :id_product");
                                            $queryInput->bindParam(':id_brand',$value -> id_brand, PDO::PARAM_STR);
                                            $queryInput->bindParam(':id_product',$value -> id_product, PDO::PARAM_STR);
                                            $queryInput->execute();
                                            $resultsInput = $queryInput->fetch(PDO::FETCH_OBJ);
                                            $input = (int) $resultsInput-> total;
                                            $checkTotal = $input - $output;
                                            echo $checkTotal;
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> brand ?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> user ?></p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create( $value -> created_ad),"d-m-Y")
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <?php if($id_power != 3){ ?>
                                    <td style = "text-align: center;">
                                        <a href="./edit-output-warehouse.php?id=<?php echo $value -> id ?>&brand=<?php echo $value -> id_brand ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                        <a href="./output-manage.php?del=<?php echo $value -> id ?>&brand=<?php echo $value -> id_brand ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
                                    </td>
                                <?php } ?>
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
            <a href="./output-manage.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
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
            <a href="./output-manage.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>

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