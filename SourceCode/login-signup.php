<?php 
    session_start();

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web PM SHOP</title>
    <?php include('head.php'); ?>
    <link href="login-signup.css" rel="stylesheet">

   
</head>

<body>

    <!-- ✅ Navbar อยู่บนสุด -->
     <?php include('navbar.php');?>

    <!-- ✅ กล่อง Login/Signup -->
    <div class="container-box" id="container">
        <div class="form-container sign-up-container">
            <div class="auth-form">
                <form action="signup_db.php" method="POST" class="w-100 h-30">
                     <h2>
                        <span style="color: black;">SIGN</span>
                        <span style="color:rgb(30, 118, 25) ;">UP</span>
                    </h2>
                    <h6>
                        <?php if(isset($_SESSION['success'])){ ?>
                            <div class="alert alert-success" role="alert">
                                <?php 
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success'])
                                ?>

                            </div>
                        <?php } ?>

                        <?php if(isset($_SESSION['error'])){ ?>
                            <div class="alert alert-danger" role="alert">
                                <?php 
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error'])
                                ?>

                            </div>
                        <?php } ?>

                    </h6>
                    <input type="text" class="form-control" name="username" placeholder="Username" required />
                    <input type="email" class="form-control" name="email" placeholder="Email" required />
                    <input type="password" class="form-control" name="password" placeholder="Password (min 6 character)" required />
                    <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required />
                    <button class="btn btn-success mt-3 w-100" name="register">Submit</button>
                </form>
            </div>
        </div>

        <div class="form-container sign-in-container">
            <div class="auth-form">
                <form action="login_db.php" method="POST" class="w-100 h-50">
                    <h2>
                        <span style="color: black;">LOG</span>
                        <span style="color: #ffc100 ;">IN</span>
                    </h2>
                    <h6>
                        
                        <?php if(isset($_SESSION['error2'])){ ?>
                            <div class="alert alert-danger" role="alert">
                                <?php 
                                    echo $_SESSION['error2'];
                                    unset($_SESSION['error2'])
                                ?>

                            </div>
                        <?php } ?>

                    </h6>
                    <input type="text" class="form-control" name="username2" placeholder="Username" required />
                    <input type="password" class="form-control" name="password2" placeholder="Password (min 6 character)" required />
                    <button class="btn btn-warning mt-3 w-100" name="login">Submit</button>
                </form>
            </div>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h2>Signed up already ?</h2>
                    <p>Click here to Login</p>
                    <button class="btn btn-outline-info" id="signIn">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h2>Don't have an account ?</h2>
                    <p>Click here to Sign Up</p>
                    <button class="btn btn-outline-info" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
     <?php include('footer.php');?>

    <!--  สลับแผง -->
   <script src="login-signup.js"></script>

    <?php include('script.php'); ?>
</body>

</html>