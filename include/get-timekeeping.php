<?php 
require_once("./connect.php");

if(!empty($_GET["brand"]) && !empty($_GET["date"]) && !empty($_GET["time"]) && !empty($_GET["user"])) {
	$id_brand = $_GET["brand"];
	$date = $_GET["date"];
	$time = $_GET["time"];
	$id_user = $_GET["user"];

    $query = $conn -> prepare("SELECT tk.*, br.name as nameBrand FROM tbl_timekeeping tk JOIN tbl_brand br on br.id = tk.id_brand WHERE tk.id_brand = :id_brand AND tk.date = :date");
    $query->bindParam(':id_brand',$id_brand, PDO::PARAM_STR);
    $query->bindParam(':date',$date, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_OBJ);

    

    if($query -> rowCount() > 0){
        $dateTime = date_format(date_create( $results -> date),"d/m/Y");
        $createdTime = date_format(date_create( $results -> created_at)," H:i:s - d/m/Y");
        $nameBrand =  $results -> nameBrand;
        if($time == 1 && $results -> morning != 0){
            $timeText = "Ca sáng";
            $time_check = $results -> morning;
            $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
            $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
            $nameUser = $resultsCheck -> fullname;
        
            echo "<b>".$timeText." ".$nameBrand."</b> ngày <b>".$dateTime."</b> đã do <b>".$nameUser." </b> chấm công lúc ".$createdTime;
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($time == 2 && $results -> noon != 0){
            $timeText = "Ca trưa";
            $time_check = $results -> noon;
            $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
            $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
            $nameUser = $resultsCheck -> fullname;
            
            echo "<b>".$timeText." ".$nameBrand."</b> ngày <b>".$dateTime."</b> đã do <b>".$nameUser." </b> chấm công lúc ".$createdTime;
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($time == 3 && $results -> afternoon != 0){
            $timeText = "Ca chiều";
            $time_check = $results -> afternoon;
            $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
            $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
            $nameUser = $resultsCheck -> fullname;
            
            echo "<b>".$timeText." ".$nameBrand."</b> ngày <b>".$dateTime."</b> đã do <b>".$nameUser." </b> chấm công lúc ".$createdTime;
            echo "<script>$('#submits').prop('disabled',true);</script>";
        }elseif($time == 4 && $results -> evening != 0){
            $timeText = "Ca tối";
            $time_check = $results -> evening;
            $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
            $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
            $nameUser = $resultsCheck -> fullname;
            
            echo "<b>".$timeText." ".$nameBrand."</b> ngày <b>".$dateTime."</b> đã do <b>".$nameUser." </b> chấm công lúc ".$createdTime;
            echo "<script>$('#submits').prop('disabled',true);</script>";    
        }else{
           echo "<script>$('#submits').prop('disabled',false);</script>";
        }
    }else{
        echo "<script>$('#submits').prop('disabled',false);</script>";
    }
}
?>