<?php 
require_once("./connect.php");

if(!empty($_POST["prod"])) {
	$id = $_POST["prod"];

    $query = $conn -> prepare("SELECT pr.*, u.name as unit FROM tbl_product pr join tbl_unit u on u.id = pr.id_unit WHERE pr.id = :id");
    $query->bindParam(':id',$id, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_OBJ);
    if($query -> rowCount() > 0){
        echo '<input type="text" class="form-focus boder-ra-5" name = "" id="" value="'.$results->detail.'" placeholder = "" disabled>';
    }
}
?>