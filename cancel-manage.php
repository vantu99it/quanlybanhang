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
        if(isset($_GET['brand'])){
            $id_brand = $_GET['brand'];
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 3 AND wa.id_brand = :id_brand ORDER BY wa.created_ad DESC");
            $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
        }else{
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand WHERE wa.id_act = 3 ORDER BY wa.created_ad DESC");
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
    <title>Admin | Quản lý hủy kệ</title>
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
                    <h1>Quản lý hủy kệ CS<?php echo $id_brand?></h1>
                </div>
                <div class="account-btn">
                    <a href="./cancel-warehouse.php" class="btn btn-post btn-add">Hủy kệ</a>
                </div>
            </section>
            <?php if($id_power != 3){ ?>
                <section class="main-right-filter">
                    <div class="account-btn">
                        <a href="./cancel-manage.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >Tất cả</a>
                    </div>
                    <div class="account-btn">
                        <a href="./cancel-manage.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Cơ sở 1</a>
                    </div>
                    <div class="account-btn">
                        <a href="./cancel-manage.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
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
                            <th class = "full-screen">Kệ còn</th>
                            <th>SL hủy</th>
                            <th class = "full-screen">Tổng hủy</th>
                            <th class = "full-screen">Cơ sở</th>
                            <th>Người hủy</th>
                            <th>Ngày hủy</th>
                            <th class = "full-screen">Ghi chú</th>
                            <?php if($id_power != 3){ ?>
                                <th>Hành động</th>
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
                                            // Tổng số đã hủy
                                            $queryCancel = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 3 AND id_brand = :id_brand and id_product = :id_product");
                                            $queryCancel->bindParam(':id_brand',$value -> id_brand, PDO::PARAM_STR);
                                            $queryCancel->bindParam(':id_product',$value -> id_product, PDO::PARAM_STR);
                                            $queryCancel->execute();
                                            $resultsCancel = $queryCancel->fetch(PDO::FETCH_OBJ);
                                            $cancel = (int) $resultsCancel -> total;

                                            // Tổng số xuất ra
                                            $queryOutput = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_warehouse WHERE id_act = 2 AND id_brand = :id_brand and id_product = :id_product");
                                            $queryOutput->bindParam(':id_brand',$value -> id_brand, PDO::PARAM_STR);
                                            $queryOutput->bindParam(':id_product',$value -> id_product, PDO::PARAM_STR);
                                            $queryOutput->execute();
                                            $resultsOutput = $queryOutput->fetch(PDO::FETCH_OBJ);
                                            $output= (int)$resultsOutput->total;

                                            // Tổng số bán
                                            $querySold = $conn -> prepare("SELECT  SUM(quantity) as total FROM tbl_sell_manage WHERE id_brand = :id_brand and id_product = :id_product");
                                            $querySold->bindParam(':id_brand',$value -> id_brand, PDO::PARAM_STR);
                                            $querySold->bindParam(':id_product',$value -> id_product, PDO::PARAM_STR);
                                            $querySold->execute();
                                            $resultsSold = $querySold->fetch(PDO::FETCH_OBJ);
                                            $sold = (int) $resultsSold -> total;

                                            $remaining= $output - $cancel - $sold;
                                            echo $remaining;
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p><?php echo $value -> quantity ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p>
                                        <?php 
                                            echo $cancel;
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
                                        <a href="./edit-cancel-warehouse.php?id=<?php echo $value -> id ?>&brand=<?php echo $value -> id_brand ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a href="./cancel-manage.php?del=<?php echo $value -> id ?>&brand=<?php echo $value -> id_brand ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
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
            <a href="./cancel-manage.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
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
            <a href="./cancel-manage.php?brand=<?php echo $id_brand_get?>" class="btn">OK</a>
        </div>
    </div>
    <?php }?>

    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "huyke.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>