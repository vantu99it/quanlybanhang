<?php
    $id_user = $_SESSION['logins']['id'];
    $id_power = $_SESSION['logins']['power'];
    $id_brand = $_SESSION['logins']['id_brand'];

    $queryBrandIds= $conn -> prepare("SELECT * FROM tbl_brand WHERE status = 1" );
    $queryBrandIds-> execute();
    $resultsBrandIds = $queryBrandIds->fetchAll(PDO::FETCH_OBJ);
?>
<div id="sidebar" class = "sidebar">
    <div class="sidebar-title">
        <h1>Menu quản trị</h1>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="dashboard.php">
                <iconify-icon class="icon" icon="fa:dashboard" width="24" height="24"></iconify-icon>
                Bảng điều khiển
            </a>
        </li>
        <li class="menu-item">
            <a href="#">
            <iconify-icon class="icon" icon="ic:twotone-post-add" width="24" height="24"></iconify-icon>
                Quản lý bán hàng
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <li class="menu-item-mini"><a href="./sell-import.php">Nhập đơn</a></li>
                <?php if($id_power == 3 && $id_brand == 1  ){ ?>
                    <li class="menu-item-mini"><a href="./sell-manage.php?brand=1">Cơ sở 1</a></li>
                <?php } elseif($id_power == 3 && $id_brand == 2  ){ ?>
                    <li class="menu-item-mini"><a href="./sell-manage.php?brand=2">Cơ sở 2</a></li>
                <?php } else {  
                    foreach ($resultsBrandIds as $key => $value) {
                ?>
                    <li class="menu-item-mini"><a href="./sell-manage.php?brand=<?php echo $value -> id ?>"><?php echo $value -> name ?></a></li>
                <?php } ?>
                    <li class="menu-item-mini"><a href="./sell-COD.php">Đơn COD</a></li>
                <?php }?>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#">
                <iconify-icon class="icon" icon="material-symbols:input-rounded" width="24" height="24"></iconify-icon>
                Quản lý nhập kho
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <li class="menu-item-mini"><a href="./import-warehouse.php">Nhập vào kho</a></li>
                <?php if($id_power != 3){ ?>
                    <li class="menu-item-mini"><a href="./import-manage.php">Hàng đã nhập</a></li>
                <?php } else { ?>
                    <li class="menu-item-mini"><a href="./import-manage.php?brand=<?php echo $id_brand ?>">Hàng đã nhập</a></li>
                <?php } ?>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#">
                <iconify-icon class="icon" icon="material-symbols:output-rounded" width="24" height="24"></iconify-icon>
                Quản lý xuất kho
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <li class="menu-item-mini"><a href="./output-warehouse.php">Xuất kho</a></li>
                <?php if($id_power != 3){ ?>
                    <li class="menu-item-mini"><a href="./output-manage.php">Hàng đã xuất</a></li>
                <?php } else { ?>
                    <li class="menu-item-mini"><a href="./output-manage.php?brand=<?php echo $id_brand ?>">Hàng đã xuất</a></li>
                <?php } ?>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#">
                <iconify-icon class="icon" icon="ic:outline-cancel-presentation" width="24" height="24"></iconify-icon>
                Quản lý hủy kệ
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <li class="menu-item-mini"><a href="./cancel-warehouse.php">Hủy kệ</a></li>
                <?php if($id_power != 3){ ?>
                    <li class="menu-item-mini"><a href="./cancel-manage.php">Hàng đã hủy</a></li>
                <?php } else { ?>
                    <li class="menu-item-mini"><a href="./cancel-manage.php?brand=<?php echo $id_brand ?>">Hàng đã hủy</a></li>
                <?php } ?>
            </ul>
        </li>

            <li class="menu-item">
                <a href="#">
                   <iconify-icon class="icon" icon="carbon:summary-kpi" width="24" height="24"></iconify-icon>
                    Quản lý báo cáo
                    <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
                </a>
                <ul class="sidebar-menu-mini">
                    <li class="menu-item-mini"><a href="./manage-total-warehouse.php">Kho hàng tổng</a></li>
                    <li class="menu-item-mini"><a href="./manage-total-warehouse-none.php">Hết hàng</a></li>
                    <?php if($id_power != 3){ ?>
                        <li class="menu-item-mini"><a href="./manage-top-sales.php?brand=1">Top doanh số</a></li>
                        <li class="menu-item-mini"><a href="./manage-top-quantity.php?brand=1">Top số lượng</a></li>
                        <li class="menu-item-mini"><a href="./collaborators.php?brand=1">Cộng tác viên</a></li>
                    <?php } ?>
                </ul>
            </li>

        <li class="menu-item">
            <a href="#">
                <iconify-icon class="icon" icon="fluent-mdl2:product-variant" width="24" height="24"></iconify-icon>
                Quản lý sản phẩm
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <?php if($id_power != 3){ ?>
                    <li class="menu-item-mini"><a href="./new-product.php">Thêm sản phẩm</a></li>
                <?php } ?>
                <li class="menu-item-mini"><a href="./manage-product.php">Quản lý sản phẩm</a></li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#">
               <iconify-icon class="icon" icon="material-symbols:playlist-add-check-circle-rounded" width="24" height="24"></iconify-icon>
                Chấm công
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <li class="menu-item-mini"><a href="./timekeeping.php">Chấm công</a></li>
                <?php foreach ($resultsBrandIds as $key => $value) { ?>
                <li class="menu-item-mini"><a href="./timekeeping-brand.php?brand=<?php echo $value -> id ?>">Bảng chấm công CS<?php echo $value -> id ?></a></li>
                <?php } ?>
            </ul>
        </li>
        
        <li class="menu-item">
            <a href="#">
                <iconify-icon class="icon" icon="mdi:user-card-details-outline" width="24" height="24"></iconify-icon>
                Quản lý tài khoản
                <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
            </a>
            <ul class="sidebar-menu-mini">
                <?php if($id_power != 3){ ?>
                    <li class="menu-item-mini"><a href="./new-user.php">Thêm người dùng</a></li>
                    <li class="menu-item-mini"><a href="./manage-user.php">Quản lý người dùng</a></li>
                <?php }?>
                <li class="menu-item-mini"><a href="./profile.php">Tài khoản cá nhân</a></li>
            </ul>
        </li>
        <?php if($id_power != 3){ ?>
            <li class="menu-item">
                <a href="#">
                   <iconify-icon class="icon" icon="foundation:burst-sale" width="24" height="24"></iconify-icon>
                    Quản lý giảm giá
                    <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
                </a>
                <ul class="sidebar-menu-mini">
                    <li class="menu-item-mini"><a href="./sale-new.php">Thêm mới</a></li>
                    <li class="menu-item-mini"><a href="./sale-manage.php">Quản lý</a></li>
                </ul>
            </li>
        <?php } ?>
        <?php if($id_power != 3){ ?>
            <li class="menu-item">
                <a href="#">
                    <iconify-icon class="icon" icon="ep:setting" width="24" height="24"></iconify-icon>
                    Thiết lập
                    <iconify-icon class="down" icon="bx:chevron-down" width="18" height="18"></iconify-icon>
                </a>
                <ul class="sidebar-menu-mini">
                    <li class="menu-item-mini"><a href="./contact-manager-new.php">Cơ sở</a></li>
                    <li class="menu-item-mini"><a href="./contact-manager.php">Hành động</a></li>
                    <li class="menu-item-mini"><a href="./contact-manager.php">Thanh toán</a></li>
                    <li class="menu-item-mini"><a href="./contact-manager.php">Nguồn đơn</a></li>
                </ul>
            </li>
        <?php } ?>
        <li class="menu-item">
            <a href="./logout.php" class="navlist">
                <iconify-icon class="icon" icon="octicon:sign-out-16" width="24" height="24"></iconify-icon>
                Thoát
            </a>
        </li>
    </ul>
</div>