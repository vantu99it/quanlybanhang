<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $get_month = date('Y-m');
        $get_today = date('Y-m-d');
        // var_dump($getdate); die();
        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = $_SESSION['logins']['id_brand'];

        //nhân viên
        $queryUser= $conn -> prepare("SELECT * FROM tbl_user WHERE status = 1 AND id = $id_user");
        $queryUser-> execute();
        $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);

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
            $time = $_POST["time"];
            $note = $_POST["note"];

            // kiểm tra đã tồn tại ngày hay chưa
            $queryDate= $conn -> prepare("SELECT * FROM tbl_timekeeping WHERE date = :date AND id_brand = :id_brand");
            $queryDate->bindParam(':date',$date,PDO::PARAM_STR);
            $queryDate->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
            $queryDate-> execute();
            // var_dump($time); die();
            if($queryDate->rowCount() == 0){
                if($time == '1'){
                    $queryTimeKeep= $conn -> prepare("INSERT INTO tbl_timekeeping (date, morning, id_brand, note) value (:date, :time, :id_brand, :note)");
                }elseif($time == '2'){
                    $queryTimeKeep= $conn -> prepare("INSERT INTO tbl_timekeeping (date, noon, id_brand, note) value (:date, :time, :id_brand, :note)");
                }elseif($time == '3'){
                    $queryTimeKeep= $conn -> prepare("INSERT INTO tbl_timekeeping (date, afternoon, id_brand, note) value (:date, :time, :id_brand, :note)");
                }else{
                    $queryTimeKeep= $conn -> prepare("INSERT INTO tbl_timekeeping (date, evening, id_brand, note) value (:date, :time, :id_brand, :note)");
                }
                $queryTimeKeep->bindParam(':date',$date,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':time',$id_user,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':note',$note,PDO::PARAM_STR);
                $queryTimeKeep-> execute();
                $lastInsertId = $conn->lastInsertId();
                if($lastInsertId){
                    $msg = "Đã chấm công thành công!";
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
            }
            else{
                if($time == '1'){
                    $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET morning = :time, note = :note WHERE date = :date AND id_brand = :id_brand ");
                }elseif($time == '2'){
                    $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET noon= :time, note = :note WHERE date = :date AND id_brand = :id_brand ");
                }elseif($time == '3'){
                    $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET afternoon= :time, note = :note WHERE date = :date AND id_brand = :id_brand ");
                }elseif($time == '4'){
                    $queryTimeKeep= $conn -> prepare("UPDATE tbl_timekeeping SET evening= :time, note = :note WHERE date = :date AND id_brand = :id_brand ");
                }else{
                    $error = "Thất bại! Vui lòng thử lại!";
                }
                $queryTimeKeep->bindParam(':date',$date,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':time',$id_user,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':id_brand',$id_brand,PDO::PARAM_STR);
                $queryTimeKeep->bindParam(':note',$note,PDO::PARAM_STR);
                $queryTimeKeep-> execute();
                $msg = "Đã chấm công thành công!";
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
    <title>Admin | Chấm công</title>
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
                    <h1>Nhập đơn hàng</h1>
                </div>
            </section>
            <section class="main-right-filter">
                <p><b class="col-red">Lưu ý: </b> Chấm công theo đúng ca làm. Nên chấm đúng ngày. Chỉ được chấm muộn sau 1 ngày. Sau 1 ngày hệ thống tự động khóa ngày. Nếu chấm sai vui lòng liên hệ quản lý để hỗ trợ sửa.</p>
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
                    <!-- input -->
                    <div class="form-input form-validator">
                        <p class="item-name">Xin chào: <b class="col-red"><?php echo $resultsUser->fullname ?></b></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Cơ sở <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="brand" id="brand">

                                <?php foreach ($resultsBrand as $key => $value) { ?>
                                    <option value="<?php echo $value -> id ?>" <?php echo ($resultsBrandId -> id == $value -> id)? "selected":"" ?>><?php echo $value -> name ?></option>
 
                                <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>

                    <div class="form-input form-validator">
                        <p class="item-name">Ngày <span class="col-red">*</span></p>
                        <input type="date" name="date" id="dateTime" class=" form-focus boder-ra-5" value ="<?php echo $get_today ?>" >
                        <p class="form-message"></p>
                    </div>

                    <div class="search-item form-validator">
                        <p class="item-name">Chọn ca làm <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="time" id="time"  onChange="selectTime()">
                           <option value="">Chọn ca làm</option>
                           <option value="1">Ca sáng</option>
                           <option value="2">Ca trưa</option>
                           <option value="3">Ca chiều</option>
                           <option value="4">Ca tối</option>
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
    
    <!-- Bắt lỗi nhập vào -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#time', 'Bạn chưa chọn ca để chấm công'),
            ],
        });
    </script>

    <!-- kiểm tra số lượng xuất ra -->
    <script>
        function selectTime(){
            var user = <?php echo json_encode($id_user); ?> ;
            var brand = $("#brand").val();
            var dateTime = $("#dateTime").val();
            var time= $("#time").val();
            console.log(user);
            jQuery.ajax({
            url: "./include/get-timekeeping.php?brand="+ brand + "&date="+ dateTime+ "&time=" + time + "&user="+ user,
            success: function(data) {
                $("#errors-message").html(data);
            },
            error: function() {}
            });
        };
    </script>

    <!-- ngày -->
    <script>
        $(document).ready(function () {
            const today = new Date();
            today.setDate(today.getDate() - 1);
            dateTime.min= today.toLocaleDateString('en-ca');
        });
    </script>
</body>
</html>