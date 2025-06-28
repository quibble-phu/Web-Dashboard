<?php 
    session_start();
    require"condb.php";

    if (!isset($_SESSION['user_id'])){
        header("location: login-signup.php?action=login");
    }


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('head.php'); ?>
    <link href="style.css" rel="stylesheet">
     <style>
    @media (prefers-color-scheme: dark) {
        body,
        #content {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .container{
            color: #000000 !important;
        }
        footer {
            color: black !important;
        }
            footer a,
            footer p {
            color: black !important;
        }
     }

  </style>
</head>

<body>
    <!-- Sidebar -->
    <div id="sidebar" class="bg-dark text-white position-fixed h-100">
        <div class="sidebar-header">
            <i class="fas fa-eye"></i>
            <span class="sidebar-text" style="color: #ffc100 ;">PM UNIT</span>
            
        </div>
        <!-- Search bar เพิ่มตรงนี้ -->
        <div class="sidebar-search px-3 py-2">
            <input type="text" class="form-control form-control-sm" id="sidebarSearch" placeholder="🔍 Search..." />
        </div>

        <div class="sidebar-nav flex-grow-1 d-flex flex-column">
            <a href="#"><i class="fas fa-home"></i> <span class="sidebar-text">Dashboard</span></a>
            <a href="#"><i class="fas fa-wrench"></i> <span class="sidebar-text">Maintenance Job</span></a>
            <a href="#"><i class="fas fa-exclamation-triangle"></i> <span class="sidebar-text">Work Issue</span></a>
            <a href="#"><i class="fas fa-users"></i> <span class="sidebar-text">Employee</span></a>
            <a href="#"><i class="fas fa-history"></i> <span class="sidebar-text">History</span></a>

            <a href="logout.php" class="text-danger mt-auto"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a>
        </div>
    </div>

    <!-- Main Content -->
    <div id="content">
        <nav class="navbar navbar-light bg-light border-bottom" id="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-warning " id="toggleSidebar">☰</button>
                <a href="index.php" class="text-decoration-none text-dark fw-bold">🏠 หน้าหลัก</a>

            </div>
            <?php 
        
                if (isset($_SESSION['user_id'])){
                    $user_id =$_SESSION['user_id'];
                }
                try{

                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $userdata =$stmt->fetch();

                }
                catch(PDOException $e){
                    echo "Registration Fail".$e->getmessage();
                }

            ?>
            <span class="navbar-brand mb-0 h1">Welcome, <?php echo $userdata['username'] ?></span>
        </nav>

        <div class="container-fluid p-4">
            <h2>🎯 Dash Board</h2>
            <p>ระบบแสดงข้อมูลการบำรุง</p>


        </div>



        <?php
        include('footer.php');
        ?>
    </div>





    <script src="main.js"></script>

    <?php include('script.php'); ?>
</body>

</html>