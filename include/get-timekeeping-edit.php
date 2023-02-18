<?php 
require_once("./connect.php");

if(!empty($_GET["brand"]) && !empty($_GET["date"]) && !empty($_GET["time"]) && !empty($_GET["user"])) {
	$id_brand = $_GET["brand"];
	$date = $_GET["date"];
	$time = $_GET["time"];
	$id_user = $_GET["user"];

    //người
    $queryUser= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
    $queryUser->bindParam('id',$id_user,PDO::PARAM_STR);
    $queryUser-> execute();
    $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);
    $userNew= $resultsUser -> fullname;

    $query = $conn -> prepare("SELECT tk.*, br.name as nameBrand FROM tbl_timekeeping tk JOIN tbl_brand br on br.id = tk.id_brand WHERE tk.id_brand = :id_brand AND tk.date = :date");
    $query->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $query->bindParam(':date',$date, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_OBJ);

    $dateTime = date_format(date_create( $date),"d/m/Y");
    $brand = $results->nameBrand;

    if($time == 1 && $results -> morning != 0){
        $timeText = "Ca sáng";
        $time_check = $results -> morning;

        $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
        $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
        $queryCheck-> execute();
        $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
        $nameUser = $resultsCheck -> fullname;
    
        echo "<b>Lưu ý:</b> Bạn đang chỉnh sửa chấm công <b>".$brand."</b> từ <b>(".$nameUser." - ".$timeText." - ".$dateTime.")</b> sang <b>(".$userNew." - ".$timeText." - ".$dateTime.")</b>";
    }elseif($time == 2 && $results -> noon != 0){
        $timeText = "Ca trưa";
        $time_check = $results -> noon;
        $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
        $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
        $queryCheck-> execute();
        $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
        $nameUser = $resultsCheck -> fullname;
        
        echo "<b>Lưu ý:</b> Bạn đang chỉnh sửa chấm công <b>".$brand."</b> từ <b>(".$nameUser." - ".$timeText." - ".$dateTime.")</b> sang <b>(".$userNew." - ".$timeText." - ".$dateTime.")</b>";
    }elseif($time == 3 && $results -> afternoon != 0){
        $timeText = "Ca chiều";
        $time_check = $results -> afternoon;
        $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
        $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
        $queryCheck-> execute();
        $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
        $nameUser = $resultsCheck -> fullname;
        
        echo "<b>Lưu ý:</b> Bạn đang chỉnh sửa chấm công <b>".$brand."</b> từ <b>(".$nameUser." - ".$timeText." - ".$dateTime.")</b> sang <b>(".$userNew." - ".$timeText." - ".$dateTime.")</b>";
    }elseif($time == 4 && $results -> evening != 0){
        $timeText = "Ca tối";
        $time_check = $results -> evening;
        $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
        $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
        $queryCheck-> execute();
        $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
        $nameUser = $resultsCheck -> fullname;
        
        echo "<b>Lưu ý:</b> Bạn đang chỉnh sửa chấm công <b>".$brand."</b> từ <b>(".$nameUser." - ".$timeText." - ".$dateTime.")</b> sang <b>(".$userNew." - ".$timeText." - ".$dateTime.")</b>";    
    }else{
        echo "Kiểm tra lại!";
    }
}
?>