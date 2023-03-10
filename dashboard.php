<?php 
    include './include/connect.php';
    include './include/func-slug.php';

    $querySale= $conn -> prepare("SELECT sale.*, pro.name AS product, pro.price FROM tbl_sale sale JOIN tbl_product pro ON pro.id = sale.id_product WHERE sale.date_start <= NOW() AND sale.date_end >= NOW() ORDER BY sale.classify ASC");
    $querySale-> execute();
    $resultsSale = $querySale->fetchAll(PDO::FETCH_OBJ);

    // xóa
    $queryDeleteSale= $conn -> prepare("DELETE FROM tbl_sale WHERE date_end < NOW()");
    $queryDeleteSale-> execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Bảng điều khiển</title>
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
            <section class="main-right-title" style = "margin-bottom: 5px;">
                <div class="form-title">
                    <h1>Sản phẩm giảm giá</h1>
                </div>
            </section>
            <?php if($querySale->rowCount() > 0){ ?>
            <div class="frame">
                <?php foreach ($resultsSale as $key => $value) {?>
                    <div class="frame-item">
                        <div class="frame-image">
                            <img src="<?php echo $value -> image ?>" alt="image">
                            <div class="frame-type">
                                <?php if($value->classify == 1 ){ ?>
                                    <span>Giảm giá</span>
                                <?php }else{ ?> 
                                    <span>Tặng kèm</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="frame-info">
                            <div class="frame-name">
                                <span><?php echo $value -> product ?></span>
                            </div>
                            <div class="frame-price">
                                <?php if($value->classify == 1 ){ ?>
                                    <p class="price-old">
                                        <?php 
                                            $tien = (int) $value -> price;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."đ";
                                        ?>
                                    </p>
                                    <p>còn</p>
                                    <p class="price-news">
                                        <?php 
                                            $tien = (int) $value -> price_sale;
                                            $bien = number_format($tien,0,",",".");
                                            echo $bien."đ";
                                        ?>
                                    </p>
                                <?php }else{ 
                                        $queryProSale= $conn -> prepare(" SELECT name FROM tbl_product WHERE id = :id");
                                        $queryProSale->bindParam(':id',$value-> id_product_sale,PDO::PARAM_STR);
                                        $queryProSale-> execute();
                                        $resultsProSale = $queryProSale->fetch(PDO::FETCH_OBJ);
                                        
                                    ?>
                                    <p>Tặng:<span class="product-num"><?php echo $value->quantity; ?></span><span class= "product-sale"><?php echo $resultsProSale -> name; ?></span></p>
                                <?php } ?>
                            </div>
                            <p class="frame-date">
                                <span> Từ: 
                                    <b>
                                        <?php 
                                            echo date_format(date_create($value -> date_start),"d-m-Y")
                                        ?>
                                    </b>
                                </span>
                                <span>Đến:  
                                    <b>
                                        <?php 
                                            echo date_format(date_create($value -> date_end),"d-m-Y")
                                        ?>
                                    </b>
                                </span>
                            </p>
                            <p class="frame-note">
                                <?php echo $value -> note ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
                
            </div>
            <?php } else{
                echo '<h2 style = "margin-top: 12px;">Không có sản phẩm giảm giá nào được áp dụng!</h2>';
            } ?>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
</body>
</html>