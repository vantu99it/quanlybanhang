<?php 
    include './include/connect.php';
    include './include/func-slug.php';
    if (!isset($_SESSION['logins'])) {
        header('location:index.php');
    }else{

         $err = "";
        $ok = "";
        $message = "";

        $id_user = $_SESSION['logins']['id'];
        $id_power = $_SESSION['logins']['power'];
        $id_brand = 2;

        if(date('d')<22){
            $monthFrom =(int) date('m')-1;
            $monthFrom = ($monthFrom < 10) ? "0".$monthFrom : $monthFrom;
            $monthTo =(int) date('m');
            $monthTo = ($monthTo < 10) ? "0".$monthTo : $monthTo;
        }else{
            $monthFrom =(int) date('m');
            $monthFrom = ($monthFrom < 10) ? "0".$monthFrom : $monthFrom;
            $monthTo =(int) date('m')+1;
            $monthTo = ($monthTo < 10) ? "0".$monthTo : $monthTo;
        }

        $year =date('Y');
        
        $fromDate = $year."-".$monthFrom."-22";
        $toDate = $year."-".$monthTo."-21";

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $fromDate = $_POST["from-date"];
            $toDate = $_POST["to-date"];
            // var_dump($toDate); die();
            $id_brand = 2;

            $queryTime= $conn -> prepare("SELECT tk.*, br.name FROM tbl_timekeeping tk JOIN tbl_brand br on br.id = tk.id_brand WHERE tk.id_brand = :id_brand AND tk.date >= :fromDate AND tk.date <= :toDate ORDER BY tk.date ASC" );
            $queryTime->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryTime->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
            $queryTime->bindParam('toDate',$toDate,PDO::PARAM_STR);
            $queryTime-> execute();
            $resultsTime = $queryTime->fetchAll(PDO::FETCH_OBJ);

        }else{
            $id_brand = 2;

            $queryTime= $conn -> prepare("SELECT tk.*, br.name FROM tbl_timekeeping tk JOIN tbl_brand br on br.id = tk.id_brand WHERE tk.id_brand = :id_brand AND tk.date >= :fromDate AND tk.date <= :toDate ORDER BY tk.date ASC" );
            $queryTime->bindParam('id_brand',$id_brand,PDO::PARAM_STR);
            $queryTime->bindParam('fromDate',$fromDate,PDO::PARAM_STR);
            $queryTime->bindParam('toDate',$toDate,PDO::PARAM_STR);
            $queryTime-> execute();
            $resultsTime = $queryTime->fetchAll(PDO::FETCH_OBJ);
        }

        // Xóa 
        if(isset($_REQUEST['del'])&&($_REQUEST['del'])){
            $delId = intval($_GET['del']);

            $query= $conn -> prepare("DELETE FROM tbl_timekeeping WHERE id = :id");
            $query->bindParam(':id',$delId,PDO::PARAM_STR);
            $query->execute();
            if($query){
                $ok = 1;
                $message = "Đã xóa thành công";
            }
            else{
                $err = 1;
                $message = "Có lỗi xảy ra, vui lòng thử lại";
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
    <title>Admin |Chấm công cơ sở 2</title>
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
                    <h1>Bảng chấm công CS2</h1>
                </div>
                <div class="account-btn">
                    <a href="./timekeeping.php" class="btn btn-post btn-add">Chấm công</a>
                </div>
            </section>
            <section class="main-right-filter">
                <form action="" method="post">
                    <span>Từ</span>
                    <input type="date" name="from-date" id="from-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo  $fromDate?>">
                    <span>Đến</span>
                    <input type="date" name="to-date" id="to-date" class=" form-focus boder-ra-5" style =" height: 30px; padding: 0 8px; margin: 0 5px; max-width: 120px" value = "<?php echo $toDate ?>">
                    <input type="submit" value="Lọc" class="btn btn-post btn-add">
                </form>
                <?php if($id_power != 3){ ?>
                    <div class="account-btn full-screen">
                        <button class="btn btn-post btn-add" onclick = "tableToExcel()">Xuất excel</button>
                    </div>
                <?php } ?>
            </section>
            </section>
            <div class="main-right-table">
                <table class="table table-bordered table-post-list" id = "table-manage">
                    <thead>
                        <tr>
                            <th class = "full-screen" >STT</th>
                            <th>Ngày chấm</th>
                            <th>Ca sáng</th>
                            <th>Ca trưa</th>
                            <th>Ca chiều</th>
                            <th>Ca tối</th>
                            <th class = "full-screen">Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody >
                        <?php foreach ( $resultsTime as $key => $value) { ?>
                            <tr>
                                <td class = "full-screen"><?php echo $key+1 ?></td>
                                <td>
                                    <p>
                                        <?php $date = date_format(date_create( $value ->  date),"N");
                                            if($date == 1){echo "Thứ 2, ";}
                                            elseif($date == 2){echo "Thứ 3, ";}
                                            elseif($date == 3){echo "Thứ 4, ";}
                                            elseif($date == 4){echo "Thứ 5, ";}
                                            elseif($date == 5){echo "Thứ 6, ";}
                                            elseif($date == 6){echo "Thứ 7, ";}
                                            else{echo "Chủ nhật, ";}
                                            echo date_format(date_create( $value -> date),"d-m-Y");
                                            ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $time_check = $value ->morning;
                                            if($time_check != 0){
                                                $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
                                                $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
                                                $queryCheck-> execute();
                                                $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
                                                echo $resultsCheck -> fullname;
                                            }else{
                                                echo "-";                                            
                                            }
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $time_check = $value ->noon;
                                            if($time_check != 0){
                                                $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
                                                $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
                                                $queryCheck-> execute();
                                                $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
                                                echo $resultsCheck -> fullname;
                                            }else{
                                                echo "-";
                                            }
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $time_check = $value ->afternoon;
                                            if($time_check != 0){
                                                $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
                                                $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
                                                $queryCheck-> execute();
                                                $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
                                                echo $resultsCheck -> fullname;
                                            }else{
                                                echo "-";                                            
                                            }
                                        ?>
                                    </p>
                                </td>
                                <td>
                                    <p>
                                        <?php 
                                            $time_check = $value ->evening;
                                            if($time_check != 0){
                                                $queryCheck= $conn -> prepare("SELECT * FROM tbl_user WHERE id = :id" );
                                                $queryCheck->bindParam('id',$time_check,PDO::PARAM_STR);
                                                $queryCheck-> execute();
                                                $resultsCheck = $queryCheck->fetch(PDO::FETCH_OBJ);
                                                echo $resultsCheck -> fullname;
                                            }else{
                                                echo "-";                                            
                                            }
                                        ?>
                                    </p>
                                </td>
                                
                                <td class = "full-screen">
                                    <p><?php echo $value -> note ?></p>
                                </td>
                                <td style = "text-align: center;">
                                    <?php if($id_power != 3){ ?>
                                        <a href="./timekeeping-edit.php?id=<?php echo $value -> id ?>&brand=<?php echo $value -> id_brand ?>" class="btn-setting btn-edit colo-blue" style = "margin: 0 5px;"><i class="fa-regular fa-pen-to-square"></i></a>

                                        <a href="./timekeeping-brand-2.php?del=<?php echo $value -> id ?>" class="btn-setting col-red" style = "margin: 0 5px;" onclick="return confirm('Bạn chắc chắn muốn xóa? Lưu ý: Xóa chấm công sẽ xóa hết chấm công của ngày! Cân nhắc kỹ!');" ><i class="fa-solid fa-trash"></i>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
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
            <a href="./timekeeping-brand-12.php" class="btn">OK</a>
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
            <a href="./timekeeping-brand-2.php" class="btn">OK</a>        
        </div>
    </div>
    <?php }?>

    <script>
        function tableToExcel(){
            $("#table-manage").table2excel({
                exclude: ".noExcel",
                filename: "chamcongCS2.xls", 
                preserveColors: false
            });
        }
    </script>
</body>
</html>
