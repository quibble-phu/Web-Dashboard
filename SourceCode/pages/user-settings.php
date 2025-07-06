<?php include('../backend/track_session.php'); ?>
<?php

if (!isset($_SESSION['user_id'])) {
    header("location: ../pages/login.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('../index/head.php'); ?>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">

</head>

<body>
    <!-- Sidebar -->
    <?php include('../index/menu.php'); ?>

    <!-- Main Content -->
    <div id="content">
        <?php include('../index/navbar-main.php'); ?>

        <div class="container-fluid p-4">
            <div class="container my-5">
                <div class="row p-4 pb-0 pe-lg-0 pt-lg-5 align-items-start rounded-3 border shadow-lg custom-dark-box">


                    <div class="col-lg-7 p-3 p-lg-5 pt-lg-3">
                        <h1 class="display-5 fw-bold lh-1 text-body-emphasis"><i class="bi bi-person-vcard-fill fs-1"></i> User Infomation</h1>
                        <hr class="my-4">
                        <ul class="list-group">
                            <li class="list-group-item fs-5"><strong>Username:</strong> <?php echo $userdata['username']; ?></li>
                            <li class="list-group-item fs-5"><strong>Email:</strong> <?php echo $userdata['email']; ?></li>
                            <li class="list-group-item fs-5"><strong>Name:</strong> <?php echo $userdata['first_name']; ?></li>
                            <li class="list-group-item fs-5"><strong>Surname:</strong> <?php echo $userdata['last_name']; ?></li>
                            <li class="list-group-item fs-5"><strong>Team:</strong> <?php echo $userdata['team']; ?></li>
                            <li class="list-group-item fs-5"><strong>Position:</strong> <?php echo $userdata['position']; ?></li>
                            <li class="list-group-item fs-5"><strong>Role:</strong> <?php echo $userdata['role']; ?></li>
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
                    <h1 class="display-5 fw-bold lh-1 text-body-emphasis"><i class="bi bi-gear-fill fs-1"></i> User Settings</h1>
                    <hr class="my-4">
                    <h4 class="mb-3">Change Profile Picture</h4>

                    <!-- แสดง avatar ปัจจุบัน -->
                    <div class="mb-3 text-center">
                        <img src="<?php echo $profileImage; ?>" alt="Current Avatar" width="150" height="150" class="rounded-circle border border-warning" style="object-fit: cover;">
                        <p class="mt-2 text-muted">Current avatar</p>
                    </div>

                    <!-- แบบฟอร์มอัปโหลด -->
                    <form action="../backend/update-avatar.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="avatar" class="form-label fw-semibold">Select new avatar</label>
                            <input class="form-control" type="file" name="avatar" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Upload Avatar</button>
                    </form>


                    <!-- Change Password Form -->
                    <h4 class="mb-3 mt-5">Change Password</h4>

                    <form id="changePasswordForm" action="../backend/change-password.php" method="post">
                        <!-- Old Password -->

                        <!-- Old Password -->
                        <div class="mb-3 position-relative">
                            <label for="old_password" class="form-label fw-semibold">Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="form-control" required style="padding-right: 40px;"  placeholder="min 6 character">
                            <button type="button" id="toggleOldPassword"
                                style="position: absolute; top: calc(50% + 3px); right: 10px; border: none; background: transparent; padding: 0; cursor: pointer; height: 24px; width: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye-slash" id="eyeIconOld"></i>
                            </button>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3 position-relative">
                            <label for="new_password" class="form-label fw-semibold">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" minlength="6" required style="padding-right: 40px;" placeholder="min 6 character">
                            <button type="button" id="toggleNewPassword"
                                style="position: absolute; top: calc(50% + 3px); right: 10px; border: none; background: transparent; padding: 0; cursor: pointer; height: 24px; width: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye-slash" id="eyeIconNew"></i>
                            </button>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3 position-relative">
                            <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="6" required style="padding-right: 40px;"  placeholder="min 6 character">
                            <button type="button" id="toggleConfirmPassword"
                                style="position: absolute; top: calc(50% + 3px); right: 10px; border: none; background: transparent; padding: 0; cursor: pointer; height: 24px; width: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye-slash" id="eyeIconConfirm"></i>
                            </button>
                        </div>



                        <button type="submit" class="btn btn-danger w-100">Change Password</button>
                    </form>
                    <!--  forget -->
                        <div class="d-flex justify-content-end align-items-center mt-3" style="font-size: 0.9rem;">
                            <span class="me-2">Forgot password?</span>
                            <a href="../pages/forget-password.php" class="me-2">Reset Password</a>
                            <span class="me-2">or</span>
                            <a href="../pages/contact.php">Contact admin</a>
                        </div>


                </div>
            </div>





        </div>



        <?php
        include('../index/footer.php');
        ?>
        <?php include('../index/script.php'); ?>
    </div>
    <script src="../js/showpass.js"></script>
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault(); // หยุด form submit ชั่วคราว

            Swal.fire({
                title: 'Confirm Password Change',
                text: "Are you sure you want to change your password?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, change it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // กดยืนยันจริงๆ ให้ submit form
                    e.target.submit();
                }
            });
        });
    </script>
    <?php if (isset($_SESSION['upload_status'])): ?>
        <script>
            Swal.fire({
                icon: '<?= $_SESSION['upload_status']; ?>',
                title: '<?= $_SESSION['upload_status'] === 'success' ? 'Success' : 'Error'; ?>',
                text: '<?= $_SESSION['upload_message']; ?>',
                confirmButtonColor: '#ffc107'
            }).then(() => {
                window.location.href = 'user-settings.php';
            });
        </script>
    <?php
        unset($_SESSION['upload_status']);
        unset($_SESSION['upload_message']);
    endif;
    ?>


    <script src="../js/main.js"></script>
    <script src="../js/popup.js"></script>

</body>

</html>