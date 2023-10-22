<?php 
require_once("./connect.php");

if(!empty($_GET["payment"])) {
	$payment = $_GET["payment"];
    if($payment == 3 || $payment == 4){
    echo '
        <div class="form-input form-validator">
            <p class="item-name col-red">Mã vận đơn <span>*</span></p>
            <input type="text" class="form-focus boder-ra-5" name = "cod" id="cod" value="" placeholder = "">
            <p class="form-message"></p>
        </div>
        <div class="form-input form-validator">
            <p class="item-name col-red">SĐT khách <span>*</span></p>
            <input type="phone" class="form-focus boder-ra-5" name = "phone-cod" id="phone-cod" value="" placeholder = "">
            <p class="form-message"></p>
        </div>
        <div class="form-input form-validator">
            <p class="item-name col-red" >Thông tin (tên - địa chỉ) <span>*</span></p>
            <textarea name="information_line" id="information_line" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
            <p class="form-message"></p>
        </div>
        <div class="search-item form-validator">
            <p class="item-name col-red">Tình trạng <span >*</span></p>
            <select  class="autobox form-focus boder-ra-5" name ="cod-status" id="cod-status"">
                <option value="0">Đang lên đơn</option>
                <option value="1">Đang vận chuyển</option>
                <option value="2">Đang giao</option>
                <option value="3">Đã giao</option>
                <option value="4">Lưu kho</option>
                <option value="5">Hoàn đơn</option>
            </select>
            <p class="form-message"></p>
        </div>';
    }
    else{
        echo "";
    }
}
?>