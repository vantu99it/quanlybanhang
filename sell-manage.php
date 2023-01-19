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
            // Theo cơ sở
            $id_brand = $_GET['brand'];
            $queryWare= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where WHERE wa.id_brand = :id_brand");
            $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
        }else{
            $queryWare= $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where ");
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
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
                    <h1>Quản lý đơn hàng</h1>
                </div>
                <div class="account-btn">
                    <a href="./output-warehouse.php" class="btn btn-post btn-add">Nhập đơn</a>
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
                </section>
                <!-- nút lọc -->
                <section class="main-right-filter">
                    <div class="account-btn">
                        <a href="./output-manage.php" class="btn btn-post btn-add <?php if(!isset($_GET['brand'])){echo "btn-active";}?>" >Theo người bán</a>
                    </div>
                    <div class="account-btn">
                        <a href="./output-manage.php?brand=1" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 1){echo "btn-active";}?>">Theo người nhập</a>
                    </div>
                    <div class="account-btn">
                        <a href="./output-manage.php?brand=2" class="btn btn-post btn-add <?php if(isset($_GET['brand']) && $_GET['brand'] == 2){echo "btn-active";}?>">Cơ sở 2</a>
                    </div>
                </section>
            <?php } ?>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th class = "full-screen">Ngày bán</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th class = "full-screen">Giảm</th>
                            <th class = "full-screen">Cộng</th>
                            <th class = "full-screen">Tình trạng</th>
                            <th>Tổng</th>
                            <th>Nguồn</th>
                            <th class = "full-screen">Người bán</th>
                            <th class = "full-screen">Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsWare as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p>
                                        <?php 
                                            echo date_format(date_create( $value -> date),"d-m-Y")
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
                                <td class = "full-screen">
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
                                <td>
                                     <p><?php echo $value -> fullname ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./edit-user.php?id=<?php echo $value -> id ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                   <?php if($id_power != 3){ ?>
                                        <a href="./categories.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
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