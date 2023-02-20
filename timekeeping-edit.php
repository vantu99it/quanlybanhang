<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $err = "";
        $ok = "";
        $message = "";

        $id_time = $_GET['id'];
        $id_brand_edit = $_GET['brand'];

        $get_month = date('Y-m');
        $get_today = date('Y-m-d');
        // var_dump($getdate); die();
        $id_user = $_SESSION['logins']['id'];
        // $id_power = $_SESSION['logins']['power'];
        // $id_brand = $_SESSION['logins']['id_brand'];

        //gọi ra thông tin chấm công
        $queryTime= $conn -> prepare("SELECT keep.*, br.name AS brand FROM tbl_timekeeping keep JOIN tbl_brand br ON br.id = keep.id_brand WHERE keep.id =:id");
        $queryTime->bindParam(':id',$id_time,PDO::PARAM_STR);
        $queryTime-> execute();
        $resultsTime = $queryTime->fetch(PDO::FETCH_OBJ);

        //nhân viên theo id
        $queryUser= $conn -> prepare("SELECT * FROM tbl_user WHERE status = 1 AND id = $id_user");
        $queryUser-> execute();
        $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);

        //nhân viên
        $queryUsers= $conn -> prepare("SELECT * FROM tbl_user WHERE status = 1 AND power != 1");
        $queryUsers-> execute();
        $resultsUsers = $queryUsers->fetchAll(PDO::FETCH_OBJ);

        //cơ sở
        $queryBrand= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1");
        $queryBrand-> execute();
        $resultsBrand = $queryBrand->fetchAll(PDO::FETCH_OBJ);

        //cơ sở theo id
        $queryBrandId= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1 AND id =:id");
        $queryBrandId->bindParam(':id',$id_brand,PDO::PARAM_STR);
        $queryBrandId-> execute();
        $resultsBrandId = $queryBrandId->fetch(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id_brand = $_POST["brand"];
            $date = $_POST["date"];
            $id_user = $_POST["user"];
            $time = $_POST["time"];
            $note = $_POST["note"];

            // kiểm tra đã tồn tại ngày hay chưa
            $queryDate= $conn -> prepare("SELECT * FROM tbl_timekeeping WHERE date = :date AND id_brand = :id_brand");
            $queryDate->bindParam(':date',$date,PDO::PARAM_STR);
            $queryDate->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $queryDate-> execute();
            $resultsDate = $queryDate->fetch(PDO::FETCH_OBJ);

            $noteStory =  $resultsDate->note;
            $noteNew = $noteStory."/.".$note;

            if($time == '1'){
                $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET morning = :time, note = :note WHERE id = :id ");
            }elseif($time == '2'){
                $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET noon= :time, note = :note WHERE id = :id ");
            }elseif($time == '3'){
                $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET afternoon= :time, note = :note WHERE id = :id ");
            }elseif($time == '4'){
                $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET evening= :time, note = :note WHERE id = :id ");
            }else{
                $error = "Thất bại! Vui lòng thử lại!";
            }
            $queryTimeKeep->bindParam(':time',$id_user,PDO::PARAM_STR);
            $queryTimeKeep->bindParam(':id',$id_time,PDO::PARAM_STR);
            $queryTimeKeep->bindParam(':note',$noteNew,PDO::PARAM_STR);
            $queryTimeKeep-> execute();
           if($queryTimeKeep){
                $ok = 1;
                $message = "Đã cập nhật thành công!";
            }
            else{
                $err = 1;
                $message = "Có lỗi xảy ra, vui lòng thử lại!";
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
    <title>Admin | Chỉnh sửa chấm công</title>
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
                    <h1>Chỉnh sửa chấm công</h1>
                </div>
            </section>
            <section class="main-right-filter">
                <p><b class="col-red">Lưu ý: </b> Bạn đang thực hiện chỉnh sửa chấm công. Vui lòng kiểm tra kỹ trước khi chỉnh sửa.</p>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <!-- input -->
                    <div class="form-input form-validator">
                        <p class="item-name">Xin chào: <b class="col-red"><?php echo $resultsUser->fullname ?></b></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Cơ sở <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">
                            <option value="<?php echo $resultsTime -> id_brand ?>" ><?php echo $resultsTime -> brand ?></option>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ngày <span class="col-red">*</span></p>
                        <input type="date" name="date" id="dateTime" class=" form-focus boder-ra-5" value ="<?php echo $resultsTime->date ?>"  >
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">Người được chấm công<span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="user" id="user">
                            <option value="">Chọn người sửa chấm công</option>
                            <?php foreach ($resultsUsers as $key => $value) {?>
                                <option value="<?php echo $value -> id ?>" ><?php echo $value -> fullname ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Chọn ca làm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="time" id="time" onchange="selectTime()">
                           <option value="">Chọn ca làm</option>
                            <?php echo ($resultsTime->morning != 0)?'<option value="1">Ca sáng</option>':""?>
                            <?php echo ($resultsTime->noon != 0)?'<option value="2">Ca trưa</option>':""?>
                            <?php echo ($resultsTime->afternoon != 0)?'<option value="3">Ca chiều</option>':""?>
                            <?php echo ($resultsTime->evening != 0)?'<option value="4">Ca tối</option>':""?>
                        </select>
                         <p class="form-message"></p>
                         <p class="form-message" id ="errors-message"></p>
                    </div>

                    <div class="form-input form-validator" id = "errMess">
                        <!-- code js đẩy vào -->
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ghi chú </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>

                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit" id = "submits"  value="CHẤM CÔNG" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
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
            <a href="<?php echo ($id_brand_edit==1)?"./timekeeping-brand-1.php": "./timekeeping-brand-2.php"?>" class="btn">OK</a>
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
            <a href="./timekeeping-edit.php?id=<?php echo $id_time?>&brand=<?php $id_brand_edit?>" class="btn">OK</a>        
        </div>
    </div>
    <?php }?>

    
    <!-- Bắt lỗi nhập vào -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#time', 'Chưa chọn ca để sửa chấm công'),
                Validator.isRequired('#user', 'Chưa chọn người để sửa chấm công'),
            ],
        });
    </script>

    <!-- kiểm tra số lượng xuất ra -->
    <script>
        function selectTime(){
            var user = $("#user").val();
            var brand = $("#brand").val();
            var dateTime = $("#dateTime").val();
            var time= $("#time").val();
            // console.log(user);
            // console.log(brand);
            // console.log(dateTime);
            // console.log(time);
            jQuery.ajax({
            url: "./include/get-timekeeping-edit.php?brand="+ brand + "&date="+ dateTime+ "&time=" + time + "&user="+ user,
            success: function(data) {
                $("#errors-message").html(data);
            },
            error: function() {}
            });
        };
    </script>

</body>
</html>