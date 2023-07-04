<?php 
require_once("./connect.php");

if(!empty($_GET["id_detail"]) ) {
	$id_product_check = $_GET["id_detail"];
    // Gọi ra số tiền của loại sản phẩm
    $queryProCheck = $conn -> prepare("SELECT sell.*, pro.name AS product, pay.name as payment, frm.name AS from_where, us.fullname AS fullname FROM tbl_sell_manage sell JOIN tbl_user us ON us.id = sell.id_user_sell  JOIN tbl_product pro on pro.id = sell.id_product JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status JOIN tbl_from_where frm ON frm.id = sell.id_from_where WHERE sell.id = :id_product_check");
    $queryProCheck->bindParam(':id_product_check',$id_product_check, PDO::PARAM_STR);
    $queryProCheck->execute();
    $resultsProCheck = $queryProCheck->fetch(PDO::FETCH_OBJ);
    // $price = $resultsProCheck -> price;
    $id_user = $resultsProCheck -> id_user;

    $queryUser = $conn -> prepare("SELECT fullname FROM tbl_user WHERE id = :id_user");
    $queryUser->bindParam(':id_user',$id_user, PDO::PARAM_STR);
    $queryUser->execute();
    $resultsUser = $queryUser->fetch(PDO::FETCH_OBJ);
    $fullName = $resultsUser -> fullname;

}
?>
<div class="table-detail">
    <button id = "close_x">
        <i class="fa-solid fa-circle-xmark"></i>
    </button>
    <table class="table table-bordered table-post-list" id = "table-manage">
        <thead>
            <tr>
                <th>Thông tin</th>
                <th>Chi tiết</th>
            </tr>
            
        </thead>
        <tbody >
            <tr>
                <td><p>ID</p></td>
                <td><?php echo $id_product_check ?></td>
            </tr>
            <tr>
                <td>Ngày bán</td>
                <td>
                    <p>
                        <?php 
                            echo date_format(date_create( $resultsProCheck -> date),"d-m")
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Sản phẩm</td>
                <td>
                    <p><?php echo $resultsProCheck -> product?></p>
                </td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td>
                    <p><?php echo $resultsProCheck -> quantity ?></p>
                </td>
            </tr>
            <tr>
                <td>Giảm</td>
                <td>
                    <p>
                        <?php 
                            $tien = (int) $resultsProCheck -> sale;
                            $bien = number_format($tien,0,",",".");
                            echo $bien."đ";
                        ?>
                        </p>
                </td>
            </tr>
            <tr>
                <td>Cộng</td>
                <td>
                    <p>
                        <?php 
                            $tien = (int) $resultsProCheck -> plus;
                            $bien = number_format($tien,0,",",".");
                            echo $bien."đ";
                        ?>
                        </p>
                </td>
            </tr>
            <tr>
                <td>Tình trạng TT</td>
                <td>
                    <p><?php echo $resultsProCheck -> payment ?></p>
                </td>
            </tr>
            <tr>
                <td>Tổng tiền</td>
                <td>
                    <p>
                        <?php 
                            $tien = (int) $resultsProCheck -> total;
                            $bien = number_format($tien,0,",",".");
                            echo $bien."đ";
                        ?>
                        </p>
                </td>
            </tr>
            <tr>
                <td>Nguồn đơn</td>
                <td>
                    <p><?php echo $resultsProCheck -> from_where ?></p>
                </td>
            </tr>
            <tr>
                <td>Người bán</td>
                <td>
                    <p><?php echo $resultsProCheck -> fullname ?></p>
                </td>
            </tr>
            <tr>
                <td>Người nhập</td>
                <td>
                    <p><?php echo $fullName ?></p>
                </td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td>
                    <p><?php echo $resultsProCheck -> note ?></p>
                </td>
            </tr>
            <tr>
                <td>TG nhập</td>
                <td>
                    <p>
                        <?php 
                            echo date_format(date_create( $resultsProCheck -> created_ad),"d-m-Y H:i:s");
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>TG sửa</td>
                <td>
                    <p>
                        <?php 
                        if($resultsProCheck -> update_ad != ""){
                        echo date_format(date_create( $resultsProCheck -> update_ad),"d-m-Y H:i:s");
                        }
                        else{
                            echo "";
                        }
                        ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
</div>