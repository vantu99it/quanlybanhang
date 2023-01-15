<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{

        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        $queryPro= $conn -> prepare("SELECT pr.*, clas.name AS classify, us.username, uni.name AS unit FROM tbl_product pr JOIN tbl_classify clas ON clas.id = pr.id_classify JOIN tbl_user us on us.id = pr.id_user JOIN tbl_unit uni ON uni.id = pr.id_unit WHERE pr.status = 1");
        $queryPro-> execute();
        $resultsPro = $queryPro->fetchAll(PDO::FETCH_OBJ);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Bảng điều khiển</title>
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
                        <a href="./new-user.php" class="btn btn-post btn-add">Tạo mới</a>
                    </div>
                <?php } ?>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th class = "full-screen" >Loại</th>
                            <th>Tên</th>
                            <th class = "full-screen">Chi tiết</th>
                            <th>Đơn vị</th>
                            <th class = "full-screen">SL/ĐV</th>
                            <th >Giá bán</th>
                            <th class = "full-screen">Ghi chú</th>
                            <th  >Hành động</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsPro as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td  class = "full-screen">
                                    <p><?php echo $value -> classify  ?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> name ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> detail ?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> unit ?></p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> num_unit ?></p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $tien = (int) $value -> price;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."đ";
                                        ?>
                                    </p>
                                </td>
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <?php if($id_power != 3){ ?>
                                        <a href="./edit-user.php?id=<?php echo $value -> id ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                        <a href="./categories.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php } ?>

                                    <?php if( $value -> status == 1){ ?>
                                        <i class="fa-solid fa-circle col-green" ></i>
                                    <?php }else{ ?>
                                        <i class="fa-solid fa-circle col-red" ></i>
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
</body>
</html>