<?php
  include './include/connect.php';
  include './include/func-slug.php';

    $id_user = (isset($_SESSION['logins']['id']))? $_SESSION['logins']['id']:[];
    // Gọi ra thông tin user
    $queryUser = $conn->prepare("SELECT us.*, br.name AS brand FROM tbl_user us join tbl_brand br ON br.id = us.id_brand WHERE us.status = 1 AND us.id = :user_id");
    $queryUser-> bindParam(':user_id', $id_user, PDO::PARAM_STR);
    $queryUser->execute();
    $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);
    // var_dump($resultsUser -> avatar); die();
    $err = "";
    $ok = "";
    $message = "";

    // Thay đổi mật khẩu
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['old-password'])){
        $old_password = $_POST['old-password'];
        $new_password = $_POST['new-password'];
        $re_password = $_POST['re-password'];

        // Kiểm tra mật khẩu cũ đã đúng chưa
        $checkPass = password_verify($old_password, $resultsUser->password);
        if($checkPass=='true'){
            $passHash = password_hash($new_password,PASSWORD_DEFAULT);
            $queryUser = $conn->prepare("UPDATE tbl_user SET password = :password WHERE id = :id");
            $queryUser-> bindParam(':password', $passHash, PDO::PARAM_STR);
            $queryUser-> bindParam(':id', $id_user, PDO::PARAM_STR);
            $queryUser->execute();
            if($queryUser){
                $ok = 1;
                $message = "Thay đổi mật khẩu thành công!";
            }else{
                $err = 1;
                $message = "Có lỗi xảy ra, vui lòng thử lại";
            }
        }else{
            $err = 1;
            $message = "Mật khẩu cũ không chính xác, vui lòng thử lại";
        }
    }
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản</title>
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
                    <h1>Thông tin cá nhân</h1>
                </div>
            </section>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Mã nhân viên:</span>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control disable" id="user_id" value="#<?php echo $resultsUser -> id ?>" name = "user_id">
                    </div>
                </div>
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Tên đăng nhập:</span>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control disable" id="user_username" value="<?php echo $resultsUser -> username?>" name = "user_username">
                    </div>
                </div>
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Tên hiển thị:</span>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control valid disable" id="user_fullname" name="user_fullname" value="<?php echo $resultsUser -> fullname?>" placeholder="VD: Nguyễn Văn A" aria-invalid="false">
                    </div>
                </div>
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Loại tài khoản</span>
                    <div class="col-md-6">
                        <input type="text" readonly class="form-control disable" id="user_power" name="user_power" value="<?php $power = $resultsUser -> power; 
                        if($power == 1){echo "Tài khoản quản trị";}
                        elseif($power == 2){echo "Tài khoản quản lý";}
                        elseif($power == 3){echo "Tài khoản nhân viên";}
                        else{echo "Tài khoản cộng tác viên";}
                        ?>">
                    </div>
                </div>
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Cơ sở</span>
                    <div class="col-md-6">
                        <input type="text"  readonly class="form-control disable" id="user_brand" name="user_brand" value="<?php echo $resultsUser -> brand?>" placeholder="">
                    </div>
                </div>
                <div class="form-group row search-item">
                    <span class="col-md-2 offset-md-2 col-form-label item-name">Mật khẩu:</span>
                    <div class="col-md-6">
                        <div class="form-text text-muted">
                            <a href="#" class = "btn-add" style="display: inline-block; margin-top: 5px;">Đổi mật khẩu</a>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
        <!-- /main-right -->
        <div class="form-act">
            <div class="form-act-edit">
                <div class="form-close">
                    <i class="fa-solid fa-x"></i>
                </div>
                <form action="" method="post" id = "frm-re-password">
                    <h2>Thay đổi mật khẩu</h2>
                    <div class="form-contact form-validator ">
                        <lable class="contact-title">Mật khẩu cũ</lable>
                        <input type="password" name="old-password" id="old-password">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-contact form-validator ">
                        <lable class="contact-title">Mật khẩu mới</lable>
                        <input type="password" name="new-password" id="new-password">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-contact form-validator ">
                        <lable class="contact-title">NHập lại mật khẩu</lable>
                        <input type="password" name="re-password" id="re-password">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-contact form-validator" id = "btn-submit">
                        <input type="submit" name ="add-form" value="Thay đổi" class="btn add-form" id = "add-form">
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            <a href="./profile.php" class="btn">OK</a>
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
            <a href="./profile.php" class="btn">OK</a>
        </div>
    </div>
    <?php }?>
<!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
    <script>
        Validator({
            form: '#frm-re-password',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [

                Validator.isRequired('#old-password'),
            
                Validator.isRequired('#new-password'),

                Validator.isRequired('#re-password'),
                Validator.isConfirmed('#re-password', function (){
                    return document.querySelector('#frm-re-password #new-password').value;
                }, 'Mật khẩu không trùng khớp'),
            ],
        });
    </script>
</body>
</html>