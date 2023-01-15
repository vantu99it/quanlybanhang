<?php 
    include './include/connect.php';
    $err = "";
  if($_SERVER["REQUEST_METHOD"] == "POST"){
        $user = $_POST['username'];
        $pass = $_POST['password'];

		$query= $conn -> prepare("SELECT * FROM tbl_user WHERE username=:username");
		$query-> bindParam(':username', $user, PDO::PARAM_STR);
		$query-> execute();
		$results = $query->fetch(PDO::FETCH_OBJ);

		if($query->rowCount() > 0)
		{
            $checkPass = password_verify($pass, $results->password);
            if($checkPass){
                $_SESSION['logins']['id']= $results -> id;
                $_SESSION['logins']['username']= $results -> username;
                $_SESSION['logins']['fullname']= $results -> fullname;
                $_SESSION['logins']['power']= $results -> power;
                $_SESSION['logins']['id_brand']= $results -> id_brand;
                header('location: dashboard.php');
            }else {
                $err = "1";
            }  
		} else{
			$err = "1";
		}
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Đăng nhập</title>
    <!-- link-css -->
    <?php include('include/link-css.php');?>
    <!-- /link-css -->
</head>
<body >
    <div class="body-login">
        <div class="container">
            <div class="login-form">
                <h2 class="login-title">Xin chào!</h2>
                <?php if($err == 1){ ?>
                <div class="errors">
                    <p>
                        <i class="fa-solid fa-triangle-exclamation" style=" margin-right: 5px;"></i>
                        Mật khẩu hoặc tài khoản không đúng, vui lòng thử lại
                    </p>
                </div>
                <?php } ?>
                <form action="#" method="post" id = "fm-login-admin">
                    <div class="form-login">
                        <div class="login form-group">
                            <label for="">Tên đăng nhập</label>
                            <input type="text" name="username" rules ="required" id="username" class ="login-input" placeholder=" ">
                            <p class="form-message"></p>
                        </div>
                        <div class="login form-group">
                            <label for="">Mật khẩu</label>
                            <input type="password" name="password" rules ="required" id="password" class ="login-input" placeholder=" ">
                            <p class="form-message"></p>
                        </div>
                        <a href="" class = "forgot-pass">Quên mật khẩu?</a>
                        <div class = "login-btn">
                        <input type="submit" name="login" value="Đăng nhập" class = "btn">
                        </div>
                            
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Javascript -->
    <!-- <script src="../js/validator.js"></script> -->
    <script>
        Validator('#fm-login-admin');
    </script>
</body>
</html>