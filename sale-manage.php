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

        $querySale= $conn -> prepare("SELECT sale.*, pro.name AS product, pro.price FROM tbl_sale sale JOIN tbl_product pro ON pro.id = sale.id_product WHERE sale.date_start <= NOW() AND sale.date_end >= NOW() ORDER BY sale.classify ASC");
        $querySale-> execute();
        $resultsSale = $querySale->fetchAll(PDO::FETCH_OBJ);

        if(isset($_REQUEST['del'])){
            $delId = intval($_GET['del']);

            $queryDeleteSale= $conn -> prepare("DELETE FROM tbl_sale WHERE id = :id");
            $queryDeleteSale->bindParam(':id',$delId,PDO::PARAM_STR);
            $queryDeleteSale-> execute();
            if($queryDeleteSale){
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
    <title>Admin | Sản phẩm giảm giá </title>
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
                    <h1>Quản lý sản phẩm</h1>
                </div>
                 <?php if($id_power != 3){ ?>
                    <div class="account-btn">
                        <a href="./sale-new.php" class="btn btn-post btn-add">Tạo mới</a>
                    </div>
                <?php } ?>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th >STT</th>
                            <th >Sản phẩm</th>
                            <th>Giá giảm</th>
                            <th>SP tặng</th>
                            <th >Số lượng</th>
                            <th >Từ ngày</th>
                            <th >Đến ngày</th>
                            <th >Ghi chú</th>
                            <th >Hành động</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsSale as $key => $value) { ?>
                            <tr>
                                <td ><?php echo $key+1 ?></td>
                                <td>
                                    <p><?php echo $value -> product?></p>
                                </td>
                                <td>
                                    <p> 
                                        <?php 
                                            if($value-> price_sale != 0){
                                            $tien = (int) $value -> price_sale;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."đ";
                                            }else{
                                                echo "";
                                            }
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p><?php  if($value-> id_product_sale != 0){
                                        $queryProSale= $conn -> prepare(" SELECT name FROM tbl_product WHERE id = :id");
                                        $queryProSale->bindParam(':id',$value-> id_product_sale,PDO::PARAM_STR);
                                        $queryProSale-> execute();
                                        $resultsProSale = $queryProSale->fetch(PDO::FETCH_OBJ);
                                        echo $resultsProSale->name;
                                    }
                                    else{
                                        echo "";
                                    }
                                    ?></p>
                                </td>
                                <td>
                                    <p><?php echo ($value -> quantity)!=0 ? $value -> quantity : "" ?></p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create($value -> date_start),"d-m-Y")
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create($value -> date_end),"d-m-Y")
                                        ?>
                                    </p>
                                </td>
                                <td >
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./sale-manage.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
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
            <a href="./sale-manage.php" class="btn">OK</a>
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
            <a href="./sale-manage.php" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
</body>
</html>