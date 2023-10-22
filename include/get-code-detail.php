<?php 
require_once("./connect.php");

if(!empty($_GET["id_cod"]) ) {
	$id_cod = $_GET["id_cod"];

    // Gọi ra số tiền của loại sản phẩm
    $queryCodCheck = $conn -> prepare("SELECT cod.*,cod.updated_ad AS update_cod, sell.*, pro.name AS product_name, brand.name AS brand_nam, user.fullname AS user_name, fr_where.name AS from_where, pay.name AS payment  FROM tbl_cod cod JOIN tbl_sell_manage sell ON cod.id_sell_manager = sell.id JOIN tbl_product pro ON pro.id = sell.id_product JOIN tbl_brand brand ON brand.id = sell.id_brand JOIN tbl_user user ON user.id = sell.id_user JOIN tbl_from_where fr_where ON fr_where.id = sell.id_from_where JOIN tbl_payment_status pay ON pay.id = sell.id_payment_status  WHERE sell.id = :id_cod");
    $queryCodCheck->bindParam(':id_cod',$id_cod, PDO::PARAM_STR);
    $queryCodCheck->execute();
    $resultsCodCheck = $queryCodCheck->fetch(PDO::FETCH_OBJ);

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
                <td><?php echo $id_cod ?></td>
            </tr>
            <tr>
                <td>Ngày bán</td>
                <td>
                    <p>
                        <?php 
                            echo date_format(date_create( $resultsCodCheck -> date),"d-m-Y")
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Sản phẩm</td>
                <td>
                    <p><?php echo $resultsCodCheck -> product_name?></p>
                </td>
            </tr>
            <tr>
                <td>Sl, Tổng</td>
                <td>
                    <p><?php echo $resultsCodCheck -> quantity ?> - 
                        <b> (
                            <?php 
                            $tien = (int) $resultsCodCheck -> total;
                            $bien = number_format($tien,0,",",".");
                            echo $bien."đ";
                            ?>
                            )
                        </b>
                    </p>
                </td>
            </tr>
            
            <tr>
                <td>Nguồn đơn</td>
                <td>
                    <p><?php echo $resultsCodCheck -> from_where ?></p>
                </td>
            </tr>
            <tr>
                <td>ĐVVC - COD</td>
                <td>
                    <p><?php 
                            $id_payment_status = $resultsCodCheck -> id_payment_status ;
                            $cod = $resultsCodCheck -> cod;
                            switch ($id_payment_status){
                                case 3:
                                    $payment = "COD Viettel";
                                    break;
                                case 4:
                                    $payment = "COD GHTK";
                                    break;
                                default:
                                    break;
                            }
                    echo $payment.' <b style = "color: red;">('.$cod.')</b>' ; 
                    ?></p>
                </td>
            </tr>
            <tr>
                <td>Thông tin</td>
                <td>
                    <a href="tel:+84<?php echo $resultsCodCheck -> phone ?>"><b><?php echo $resultsCodCheck -> phone ?></b></a>
                    <p><?php echo $resultsCodCheck -> information_line ?></p>
                </td>
            </tr>
            <tr>
                <td>Tình trạng TT</td>
                <td>
                    <p><?php 
                    $status =  $resultsCodCheck -> status ;
                    $status_info = "";
                    switch ($status) {
                                case 0:
                                    $status_info = "Lên đơn";
                                    break;
                                case 2:
                                    $status_info = "Đang giao";
                                    break;
                                case 3:
                                    $status_info = "Đã giao";
                                    break;
                                case 4:
                                    $status_info = "Lưu kho";
                                    break;
                                case 5:
                                    $status_info = "Hoàn về";
                                    break;
                                default:
                                    $status_info = "Đang vận chuyển";
                                    break;
                            }
                            echo $status_info;
                    ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td>
                    <p><?php echo $resultsCodCheck -> note ?></p>
                </td>
            </tr>
            <tr>
                <td>TG nhập</td>
                <td>
                    <p>
                        <?php 
                            echo date_format(date_create( $resultsCodCheck -> created_ad),"d-m-Y H:i:s");
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td>TG sửa</td>
                <td>
                    <p>
                        <?php 
                        if($resultsCodCheck -> update_cod != ""){
                        echo date_format(date_create( $resultsCodCheck -> update_cod),"d-m-Y H:i:s");
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