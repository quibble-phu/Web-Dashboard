<?php
session_start();
require "condb.php";

if (isset($_SESSION['user_id'])) {
    $session_id = session_id();
    $user_id = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');

     $stmt = $pdo->prepare("
    INSERT INTO user_sessions(user_id, session_id, last_activity, created_at)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)
    ");
    $stmt->execute([$user_id, $session_id, $now, $now]);
}

if (!isset($_SESSION['user_id'])) {
    header("location: login-signup.php?action=login");
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('head.php'); ?>
    <link href="style.css" rel="stylesheet">
     <link href="colormode.css" rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <?php include('menu.php'); ?>
    <!-- Sidebar end-->

    <!-- Main Content -->
    <div id="content">

        <!-- navbar main -->
        <?php include('navbar-main.php'); ?>
        <!-- navbar main end -->

        <!-- content -->
        <div class="container-fluid p-4">
            <div class="container-fluid p-4">
            <div class="container my-5">
                <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-start rounded-3 border shadow-lg custom-dark-box">


                    <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                        
                        <h1 class="display-4 fw-bold lh-1 text-body-emphasis"><i class="bi bi-database-fill-gear fs-1"></i> Reporting System</h1>
                        <hr class="my-4">
                        <h3>ระบบแสดงข้อมูลต่างๆ</h3>


                    </div>
                    <div class="col-lg-4 offset-lg-1 p-0 overflow-hidden shadow-lg">
                        <img src="Pic/hero.png" alt="Hero" class="img-fluid" width="720">
                    </div>
                </div>
            </div>


        </div>
        <!-- content end -->

        <!-- foooter -->
        <?php
        include('footer.php');
        ?>


    </div>
    <!-- Main content end -->



    <!-- on off menu -->
    <script src="main.js"></script>

    <?php include('script.php'); ?>
    <script src="popup.js"></script>
</body>

</html>