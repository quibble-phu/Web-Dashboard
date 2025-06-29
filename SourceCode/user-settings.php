<?php
session_start();
require "condb.php";

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

    <!-- Main Content -->
    <div id="content">
        <?php include('navbar-main.php'); ?>

        <div class="container-fluid p-4">
            <div class="container my-5">
                <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-start rounded-3 border shadow-lg custom-dark-box">


                    <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                        <h1 class="display-4 fw-bold lh-1 text-body-emphasis"><i class="bi bi-person-vcard-fill fs-1"></i> User Settings</h1>
                        <hr class="my-4">
                        <h3>User Info</h3>
                        <ul class="list-group">
                            <li class="list-group-item fs-5"><strong>Username:</strong> <?php echo $userdata['username']; ?></li>
                            <li class="list-group-item fs-5"><strong>Email:</strong> <?php echo $userdata['email']; ?></li>
                        </ul>

                    </div>
                    <div class="col-lg-4 offset-lg-1 p-0 overflow-hidden shadow-lg">
                        <img src="<?php echo $profileImage; ?>" alt="Profile image" class="img-fluid" width="720">
                    </div>
                </div>
            </div>




            <!-- แสดง avatar ปัจจุบัน -->
            <!-- แบบฟอร์มอัปโหลด Avatar -->
            <div class="container mt-5">
                <div class="card shadow-sm p-4">
                    <h4 class="mb-3">Change Profile Picture</h4>

                    <!-- แสดง avatar ปัจจุบัน -->
                    <div class="mb-3 text-center">
                        <img src="<?php echo $profileImage; ?>" alt="Current Avatar" width="150" height="150" class="rounded-circle border border-warning" style="object-fit: cover;">
                        <p class="mt-2 text-muted">Current avatar</p>
                    </div>

                    <!-- แบบฟอร์มอัปโหลด -->
                    <form action="update-avatar.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatar" class="form-label fw-semibold">Select new avatar</label>
                            <input class="form-control" type="file" name="avatar" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Upload Avatar</button>
                    </form>
                </div>
            </div>




        </div>



        <?php
        include('footer.php');
        ?>
    </div>





    <script src="main.js"></script>

    <?php include('script.php'); ?>
</body>

</html>