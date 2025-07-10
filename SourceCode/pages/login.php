<?php include('../backend/track_session.php'); ?>
<?php
if (isset($_SESSION['user_id'])) {
    header("location: ../pages/dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login | PM</title>

    <?php include('../index/head.php'); ?>


    <style>
        body {
            background: #f5f6fa;
        }

        .login-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 6rem 2.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ffc107;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body>
    <?php include('../index/navbar.php'); ?>

    <!-- ✅ Login form -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="row w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto login-card">
                <form action="../backend/login_db.php" method="POST">
                    <h2 class="mb-4 text-center fw-bold">
                        <span style="color: black;">LOG</span>
                        <span style="color: #ffc107;">IN</span>
                    </h2>

                    <?php if (isset($_SESSION['error2'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error2'];
                            unset($_SESSION['error2']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="username2" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username2" placeholder="Enter your username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password2" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password2" id="password2" placeholder="min 6 character" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-warning w-100 mt-3" name="login">Login</button>
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
    <script src="../js/showpass.js"></script>
    <?php include('../index/footer.php'); ?>
    
</body>

</html>