<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $queryBrand= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1");
        $queryBrand-> execute();
        $resultsBrand = $queryBrand->fetchAll(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $fullname = $_POST['fullname'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $rPassword = $_POST['password-confirmation'];
            $brand = $_POST['brand'];
            $power = $_POST['power'];

            $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE username = :username AND status =1");
            $queryCheck->bindParam(':username',$username,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);

            if($queryCheck->rowCount() <= 0){
                $passHash = password_hash($password,PASSWORD_DEFAULT);

                $queryUser= $conn -> prepare("INSERT INTO tbl_user (username, password, fullname, power, id_brand) value (:username, :password, :fullname, :power, :id_brand) ");
                $queryUser->bindParam(':username',$username,PDO::PARAM_STR);
                $queryUser->bindParam(':password',$passHash,PDO::PARAM_STR);
                $queryUser->bindParam(':fullname',$fullname,PDO::PARAM_STR);
                $queryUser->bindParam(':power',$power,PDO::PARAM_STR);
                $queryUser->bindParam(':id_brand',$brand,PDO::PARAM_STR);
                $queryUser-> execute();
                $lastInsertId = $conn->lastInsertId();
                if($lastInsertId){
                    $msg = "Tạo tài khoản thành công!";
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
            }else{
                $error = "Tên tài khoản này đã tồn tại!";
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
                        <input type="text" class="form-focus boder-ra-5" name = "fullname" id="fullName" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Tên tài khoản</p>
                        <input type="text" class="form-focus boder-ra-5" name = "username" id="username" value="" placeholder = "Tiếng việt không dấu viết liền">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Mật khẩu</p>
                        <input type="password" class="form-focus boder-ra-5" name = "password" id="password" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Nhập lại mật khẩu</p>
                        <input type="password" class="form-focus boder-ra-5" name = "password-confirmation" id="password-confirmation" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Thuộc cơ sở</p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">
                            <option value="">Chọn cơ sở</option>
                            <?php foreach ($resultsBrand as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Loại tài khoản</p>
                        <select  class="autobox form-focus boder-ra-5" name ="power" id="power">
                            <option value="">Chọn loại tài khoản</option>
                            <option value="1">Tài khoản quản trị</option>
                            <option value="2">Tài khoản quản lý</option>
                            <option value="3">Tài khoản nhân viên</option>
                            <option value="4">Tài khoản cộng tác viên</option>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form" style = "width: 50%">
                        <input type="submit" name="submit-form" class="btn btn-submit"  value="Tạo mới" style = "width: 100%;height: 45px;font-size: 18px;">
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
                Validator.isRequired('#password'),
                Validator.isRequired('#password-confirmation'),
                Validator.isConfirmed('#password-confirmation', function (){
                    return document.querySelector('#frm-post #password').value;
                }, 'Mật khẩu không trùng khớp'),
                Validator.isRequired('#brand', 'Vui lòng chọn cơ sở'),
                Validator.isRequired('#power', 'Vui lòng phân quyền'),
            ],
        });
    </script>
    <script>
        $(document).ready(function() { 
            $("#brand").select2({
                placeholder: 'Chọn cơ sở',
                allowClear: true
             }); 
        });
    </script>
</body>
</html>