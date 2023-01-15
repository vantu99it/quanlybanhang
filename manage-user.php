<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $queryUser= $conn -> prepare("SELECT u.*, br.name FROM tbl_user u JOIN tbl_brand br ON br.id = u.id_brand");
        $queryUser-> execute();
        $resultsUser = $queryUser->fetchAll(PDO::FETCH_OBJ);
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
                    <h1>Quản lý tài khoản</h1>
                </div>
                <div class="account-btn">
                    <button class="btn btn-post btn-add">Tạo mới</button>
                </div>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th class = "full-screen" >Họ tên</th>
                            <th>Tài khoản</th>
                            <th>Cơ sở</th>
                            <th>Quyền</th>
                            <th>Hành động</th>
                        </tr>
                        
                    </thead>
                    <tbody >
                        <?php foreach ($resultsUser as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td class = "full-screen" > 
                                    <p><?php echo $value -> fullname ?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> username ?></p>
                                </td>
                                <td>
                                    <p><?php echo $value -> name ?></p>
                                </td>
                                <td>
                                    <p> 
                                        <?php if($value -> power == 1){
                                            echo "Quản trị";
                                        }elseif($value -> power == 2){
                                            echo "Quản lý";
                                        }elseif($value -> power == 3){
                                            echo "Nhân viên";
                                        }
                                        ?>
                                    </p>
                                </td>
                                <td style = "text-align: center;">
                                    <a href="./edit-user.php?id=<?php echo $value -> id ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                    <a href="./categories.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa?');" ><i class="fa-solid fa-trash"></i>
                                    </a>

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