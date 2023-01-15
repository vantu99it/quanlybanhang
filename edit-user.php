<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id = $_GET['id'];

        $queryUser= $conn -> prepare("SELECT u.*, br.name FROM tbl_user u join tbl_brand br ON br.id = u.id_brand WHERE u.id = :id");
        $queryUser->bindParam(':id',$id,PDO::PARAM_STR);
        $queryUser-> execute();
        $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);

        $queryBrand= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1");
        $queryBrand-> execute();
        $resultsBrand = $queryBrand->fetchAll(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $fullname = $_POST['fullname'];
            $password = $_POST['password'];
            $rPassword = $_POST['password-confirmation'];
            $brand = $_POST['brand'];
            $power = $_POST['power'];

            if($password != "" && $rPassword != "" ){
                $passHash = password_hash($password,PASSWORD_DEFAULT);

                $queryUser= $conn -> prepare("UPDATE tbl_user SET password= :password, fullname = :fullname, power = :power, id_brand = :id_brand WHERE id = :id ");
                $queryUser->bindParam(':password',$passHash,PDO::PARAM_STR);
                $queryUser->bindParam(':fullname',$fullname,PDO::PARAM_STR);
                $queryUser->bindParam(':power',$power,PDO::PARAM_STR);
                $queryUser->bindParam(':id_brand',$brand,PDO::PARAM_STR);
                $queryUser->bindParam(':id',$id,PDO::PARAM_STR);
                $queryUser-> execute();
                if($queryUser){
                    $msg = "Cập nhật thành công!";
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
            }else{
                $queryUser= $conn -> prepare("UPDATE tbl_user SET fullname = :fullname, power = :power, id_brand = :id_brand WHERE id = :id ");
                $queryUser->bindParam(':fullname',$fullname,PDO::PARAM_STR);
                $queryUser->bindParam(':power',$power,PDO::PARAM_STR);
                $queryUser->bindParam(':id_brand',$brand,PDO::PARAM_STR);
                $queryUser->bindParam(':id',$id,PDO::PARAM_STR);
                $queryUser-> execute();
                if($queryUser){
                    $msg = "Thành công!";
                    header("Location: ./edit-user.php?id=".$id);
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
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
                    <h1>Tạo mới tài khoản người dùng</h1>
                </div>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <?php if(isset($error)){ ?>
                        <div class="errorWrap">
                            <strong>Lỗi: </strong><span><?php echo $error; ?> </span>
                        </div>
                    <?php }elseif(isset($msg)){ ?>
                        <div class="succWrap">
                            <strong>Thành công: </strong><span><?php echo $msg; ?> </span>
                        </div>
                    <?php } ?>
                    <div class="form-input form-validator">
                        <p class="item-name">Họ và tên</p>
                        <input type="text" class="form-focus boder-ra-5" name = "fullname" id="fullName" value="<?php echo $resultsUser -> fullname ?>" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Tên tài khoản</p>
                        <input type="text" class="form-focus boder-ra-5" name = "username" id="username" value="<?php echo $resultsUser -> username ?>" disabled>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Mật khẩu</p>
                        <input type="password" class="form-focus boder-ra-5" name = "password" id="password" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Nhập lại mật khẩu</p>
                        <input type="password" class="form-focus boder-ra-5" name = "password-confirmation" id="password-confirmation" value="" >
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Thuộc cơ sở</p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">
                            <!-- <option value="">Chọn cơ sở</option> -->
                            <option value="<?php echo $resultsUser -> id_brand ?>" selected><?php echo $resultsUser -> name ?></option>
                            <?php foreach ($resultsBrand as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Loại tài khoản</p>
                        <select  class="autobox form-focus boder-ra-5" name ="power" id="power">
                            <option value="1" <?php echo (($resultsUser -> power )== 1)?"selected":"" ?>>Tài khoản quản trị</option>
                            <option value="2" <?php echo (($resultsUser -> power )== 2)?"selected":"" ?>>Tài khoản quản lý</option>
                            <option value="3" <?php echo (($resultsUser -> power )== 3)?"selected":"" ?>>Tài khoản nhân viên</option>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form" style = "width: 50%">
                        <input type="submit" name="submit-form" class="btn btn-submit"  value="Cập nhật" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#fullName', 'Vui lòng đầy đủ họ tên'), 
                Validator.isRequired('#username', 'Vui lòng nhập tên tài khoản'),
                Validator.isConfirmed('#password-confirmation', function (){
                    return document.querySelector('#frm-post #password').value;
                }, 'Mật khẩu không trùng khớp'),
                Validator.isRequired('#brand', 'Vui lòng chọn cơ sở'),
                Validator.isRequired('#power', 'Vui lòng phân quyền'),
            ],
        });
    </script>
</body>
</html>