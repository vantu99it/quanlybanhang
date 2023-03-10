<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{
        $id_user = $_SESSION['logins']['id'];

        $queryClass= $conn -> prepare("SELECT * FROM tbl_classify WHERE status = 1");
        $queryClass-> execute();
        $resultsClass = $queryClass->fetchAll(PDO::FETCH_OBJ);

        $queryUnit= $conn -> prepare("SELECT * FROM tbl_unit WHERE status = 1");
        $queryUnit-> execute();
        $resultsUnit = $queryUnit->fetchAll(PDO::FETCH_OBJ);

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $classify = $_POST['classify'];
            $name = $_POST['name'];
            $detail = $_POST['detail'];
            $unit = $_POST['unit'];
            $num_unit = $_POST['num-unit'];
            $price = $_POST['price'];
            $note = $_POST['note'];

            // var_dump($note); die();

            $queryCheck= $conn -> prepare("SELECT * FROM tbl_product WHERE name = :name AND status = 1");
            $queryCheck->bindParam(':name',$name,PDO::PARAM_STR);
            $queryCheck-> execute();
            $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);

            if($queryCheck->rowCount() <= 0){

                $queryProd= $conn -> prepare("INSERT INTO tbl_product (name, detail, id_classify, id_unit, num_unit, price, note, id_user ) value (:name, :detail, :id_classify, :id_unit, :num_unit, :price, :note, :id_user ) ");
                $queryProd->bindParam(':name',$name,PDO::PARAM_STR);
                $queryProd->bindParam(':detail',$detail,PDO::PARAM_STR);
                $queryProd->bindParam(':id_classify',$classify,PDO::PARAM_STR);
                $queryProd->bindParam(':id_unit',$unit,PDO::PARAM_STR);
                $queryProd->bindParam(':num_unit',$num_unit,PDO::PARAM_STR);
                $queryProd->bindParam(':price',$price,PDO::PARAM_STR);
                $queryProd->bindParam(':note',$note,PDO::PARAM_STR);
                $queryProd->bindParam(':id_user',$id_user,PDO::PARAM_STR);
                $queryProd-> execute();
                $results = $queryProd->fetchAll(PDO::FETCH_OBJ);
                $lastInsertId = $conn->lastInsertId();
                if($lastInsertId){
                    $msg = "T???o s???n ph???m th??nh c??ng!";
                }else{
                    $error = "Th???t b???i! Vui l??ng th??? l???i!";
                }
            }else{
                $error = "T??n s???n ph???m n??y ???? t???n t???i!";
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
    <title>Admin | B???ng ??i???u khi???n</title>
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
                    <h1>T???o m???i s???n ph???m</h1>
                </div>
            </section>
            <form action="" method="post" id = "frm-post">
                <div class="input-new">
                    <?php if(isset($error)){ ?>
                        <div class="errorWrap">
                            <strong>L???i: </strong><span><?php echo $error; ?> </span>
                        </div>
                    <?php }elseif(isset($msg)){ ?>
                        <div class="succWrap">
                            <strong>Th??nh c??ng: </strong><span><?php echo $msg; ?> </span>
                        </div>
                    <?php } ?>
                    <div class="search-item form-validator">
                        <p class="item-name">Lo???i s???n ph???m <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="classify" id="classify">
                            <option value="">Ch???n lo???i s???n ph???m</option>
                            <?php foreach ($resultsClass as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">T??n s???n ph???m <span class="col-red">*</span></p>
                        <input type="text" class="form-focus boder-ra-5" name = "name" id="name" value="" placeholder = "Nh???p t??n">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Chi ti???t s???n ph???m <span class="col-red">*</span></p>
                        <textarea name="detail" id="detail" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="search-item form-validator">
                        <p class="item-name">????n v??? <span class="col-red">*</span></p>
                        <select  class="autobox form-focus boder-ra-5" name ="unit" id="unit">
                            <option value="">Ch???n ????n v???</option> 
                            <?php foreach ($resultsUnit as $key => $value) { ?>
                                <option value="<?php echo $value -> id ?>"><?php echo $value -> name ?></option>
                            <?php } ?>
                        </select>
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">S??? l????ng/????n v??? <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "num-unit" id="num-unit" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Gi?? b??n <span class="col-red">*</span></p>
                        <input type="number" class="form-focus boder-ra-5" name = "price" id="price" value="" placeholder = "">
                        <p class="form-message"></p>
                    </div>
                    <div class="form-input form-validator">
                        <p class="item-name">Ghi ch?? </p>
                        <textarea name="note" id="note" cols="10" rows="5" class="form-focus boder-ra-5 textarea"></textarea>
                        <p class="form-message"></p>
                    </div>
                    <div class="submit-form">
                        <input type="submit" name="submit-form" class="btn btn-submit"  value="T???o m???i" style = "width: 100%;height: 45px;font-size: 18px;">
                    </div>
                </div>
            </form>
        </div>
        <!-- /main-right -->
    </div>
    <!-- footer + js -->
    <?php include('include/footer.php');?>
    <!-- /footer + js -->
    <script>
        Validator({
            form: '#frm-post',
            formGroupSelector: '.form-validator',
            errorSelector: ".form-message",
            rules: [
                Validator.isRequired('#classify', 'Vui l??ng ch???n lo???i s???n ph???m'), 
                Validator.isRequired('#name', 'Vui l??ng nh???p t??n s???n ph???m'),
                Validator.isRequired('#detail', 'Vui l??ng nh???p chi ti???t'),
                Validator.isRequired('#unit', 'Vui l??ng ch???n ????n v???'),
                Validator.isRequired('#num-unit', 'Vui l??ng nh???p SL/??V'),
                Validator.isRequired('#price', 'Vui l??ng nh???p gi?? b??n'),
            ],
        });
    </script>
    <script>
        $(document).ready(function() { 
            // Lo???i s???n ph???m
            $("#classify").select2({
                placeholder: 'Ch???n lo???i s???n ph???m',
                allowClear: true
            }); 
            // ????n v???
            $("#unit").select2({
                placeholder: 'Ch???n ????n v???',
                allowClear: true
            }); 
        });
    </script>
</body>
</html>