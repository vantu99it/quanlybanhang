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
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand, am.remaining, am.cancel FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand JOIN tbl_amount am ON am.id_product = pro.id WHERE wa.id_act = 3 AND wa.id_brand = :id_brand");
            $queryWare->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryWare-> execute();
            $resultsWare = $queryWare->fetchAll(PDO::FETCH_OBJ);
        }else{
            $queryWare= $conn -> prepare("SELECT wa.*, us.username AS user, pro.name AS product, br.name AS brand, am.remaining, am.cancel FROM tbl_warehouse wa JOIN tbl_product pro ON pro.id = wa.id_product JOIN tbl_user us ON us.id = wa.id_user JOIN tbl_brand br ON br.id = wa.id_brand JOIN tbl_amount am ON am.id_product = pro.id WHERE wa.id_act = 3");
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
                    <h1>Quản lý hủy kệ</h1>
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
                            <th>SL hủy</th>
                            <th class = "full-screen">Tổng</th>
                            <th class = "full-screen">Kệ còn</th>
                            <th class = "full-screen">Cơ sở</th>
                            <th>Người nhập</th>
                            <th>Ngày nhập</th>
                            <th class = "full-screen">Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsWare as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p><?php echo $value -> product?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> quantity ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> cancel ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> remaining ?></p>
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
                filename: "huyke.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>