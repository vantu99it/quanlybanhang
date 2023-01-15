<?php
    include 'include/connect.php';
    // session_destroy();
    unset($_SESSION['logins']);
    header('location: index.php');
?>