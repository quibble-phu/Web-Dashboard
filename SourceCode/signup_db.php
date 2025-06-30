<?php   
    session_start();
    require 'condb.php';
    $minlenght = 6;
    $role ="user";

    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];

    }

    if (empty($username)){
        $_SESSION['error'] = "Please enter your username";
        header("location: login-signup.php?action=signup");
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Please enter a valid email address";
        header("location: login-signup.php?action=signup");
    }
    else if (strlen($password) < $minlenght){
        $_SESSION['error'] = "Please enter a valid password";
        header("location: login-signup.php?action=signup");
    }
    else if ($password !== $confirmpassword) {
        $_SESSION['error'] = "Your password do not match";
        header("location: login-signup.php?action=signup");
    }
    else {
        $checkusername =$pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $checkusername->execute([$username]);
        $usernameexists = $checkusername->fetchColumn();

        $checkemail =$pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkemail->execute([$email]);
        $useremailexists = $checkemail->fetchColumn();

        if ($usernameexists){
            $_SESSION['error'] = "Username already exists";
            header("location: login-signup.php?action=signup");

        }
        else if ($useremailexistsxists){
            $_SESSION['error'] = "Email already exists";
            header("location: login-signup.php?action=signup");

        }
        else{
            $hashedPassword = password_hash($password,PASSWORD_DEFAULT);

            try{
                $stmt =$pdo->prepare("INSERT INTO users(username,email,password,role) VALUES(?,?,?,?)");
                $stmt->execute([$username,$email,$hashedPassword,$role]);
                $_SESSION['success'] = "Registration Successfully";
                header("location: login-signup.php?action=signup");

            }catch(PDOException $e){
                $_SESSION['error'] = "Something went wrong,please try again";
                echo "Registration Fail".$e->getmessage();
                header("location: login-signup.php?action=signup");

            }
        }
    }



    


?>