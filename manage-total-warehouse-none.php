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
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        $queryPro= $conn -> prepare("SELECT pro.* FROM tbl_product pro JOIN tbl_classify class ON class.id = pro.id_classify WHERE pro.status = 1 ORDER BY class.id ASC");
        $queryPro-> execute();
        $resultsPro = $queryPro->fetchAll(PDO::FETCH_OBJ);

        if(isset($_GET['brand'])){
            $id_brand = $_GET['brand'];
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 AND wa.id_brand = :id_brand ORDER BY wa.created_ad DESC");
            $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
        }else{
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 2 ORDER BY wa.created_ad DESC");
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
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
    <title>Admin | Quản lý hàng hóa</title>
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
                    <h1>Quản lý hàng hết <?php echo isset($_GET['brand'])?"cơ sở ".$_GET['brand']:""?></h1>
                </div>
            </section>
            <?php if($id_power != 3){ ?>
                <section class="main-right-filter">
                    <div class="account-btn">
                        <a href="./manage-total-warehouse-none.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >Tất cả</a>
                    </div>
                    <div class="account-btn">
                        <a href="./manage-total-warehouse-none.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Cơ sở 1</a>
                    </div>
                    <div class="account-btn">
                        <a href="./manage-total-warehouse-none.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
                    </div>
                    <div class="account-btn full-screen">
                        <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xuất excel</button>
                    </div>
                </section>
            <?php } ?>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Sản phẩm</th>
                            <th class = "full-screen">Tổng nhập</th>
                            <th class = "full-screen">Tổng xuất</th>
                            <th class = "full-screen">Tổng hủy</th>
                            <th class = "full-screen">Tổng bán</th>
                            <th>Kho còn</th>
                            <th>Kệ còn</th>
                            <th>Tổng còn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsPro as $key => $value) { 
                            // Tổng số nhập vào kho
                            if(isset($_GET['brand'])){
                                $queryInput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 1 AND id_product = :id_product AND id_brand = :id_brand");
                                $queryInput->bindParam(':id_brand',$_GET['brand'], PDO::PARAM_STR);
                            }else{
                                $queryInput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 1 AND id_product = :id_product");
                            }
                            $queryInput->bindParam(':id_product',$value -> id, PDO::PARAM_STR);
                            $queryInput->execute();
                            $resultsInput = $queryInput->fetch(PDO::FETCH_OBJ);
                            $input= (int)$resultsInput->total;

                            // Tổng số xuất
                            if(isset($_GET['brand'])){
                                $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_product = :id_product AND id_brand = :id_brand");
                                $queryOutput->bindParam(':id_brand',$_GET['brand'], PDO::PARAM_STR);
                            }else{
                                $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_product = :id_product");
                            }
                            $queryOutput->bindParam(':id_product',$value -> id, PDO::PARAM_STR);
                            $queryOutput->execute();
                            $resultsOutput = $queryOutput->fetch(PDO::FETCH_OBJ);
                            $output= (int)$resultsOutput->total;

                            // Tổng số hủy
                            if(isset($_GET['brand'])){
                                $queryCancel = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 3 AND id_product = :id_product AND id_brand = :id_brand");
                                $queryCancel->bindParam(':id_brand',$_GET['brand'], PDO::PARAM_STR);
                            }else{
                                $queryCancel = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 3 AND id_product = :id_product");
                            }
                            $queryCancel->bindParam(':id_product',$value -> id, PDO::PARAM_STR);
                            $queryCancel->execute();
                            $resultsCancel = $queryCancel->fetch(PDO::FETCH_OBJ);
                            $cancel= (int)$resultsCancel->total;
                            
                            // Tổng số bán
                            if(isset($_GET['brand'])){
                                $querySold = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_sell_manage WHERE id_product = :id_product AND id_brand = :id_brand");
                                $querySold->bindParam(':id_brand',$_GET['brand'], PDO::PARAM_STR);
                            }else{
                                $querySold = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_sell_manage WHERE id_product = :id_product");
                            }
                            
                            $querySold->bindParam(':id_product',$value -> id, PDO::PARAM_STR);
                            $querySold->execute();
                            $resultsSold = $querySold->fetch(PDO::FETCH_OBJ);
                            $sold = (int) $resultsSold -> total;

                             $remainingStock = $input - $output;
                            $remainingShelf = $output - $cancel - $sold;
                            $remainingTotal =  $remainingStock + $remainingShelf;
                            if( $remainingTotal == 0){
                        ?>
                            
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td style = "text-align: left;">
                                    <p><?php echo $value -> name?></p>
                                </td>
                                
                                <td class = "full-screen">
                                    <p>
                                        <?php echo $input;
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p>
                                        <?php echo $output;
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p>
                                        <?php echo $cancel;
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p>
                                        <?php echo $sold;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                       
                                        echo $remainingStock;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                        
                                        echo $remainingShelf;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                        echo $remainingTotal;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php if( $remainingTotal >= 1){ ?>
                                        <i class="fa-solid fa-circle col-green" ></i> <span class="col-green full-screen">Còn hàng</span>
                                        <?php }else{ ?>
                                            <i class="fa-solid fa-circle col-red" ></i> <span class="col-red full-screen" >Hết hàng</span>
                                        <?php } ?>
                                    </p>
                                </td>
                            </tr>
                        <?php }} ?>
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
            <a href="./manage-total-warehouse-none.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
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
            <a href="./manage-total-warehouse-none.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>

    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "hanghet.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>