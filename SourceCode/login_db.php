<?php 
    session_start();
    require"condb.php";
    $minlenght = 6;

    if (isset($_POST['login'])) {
        $username = $_POST['username2'];
        $password = $_POST['password2'];
        
    }
    if (empty($username)){
        $_SESSION['error2'] = "Please enter your username";
        header("location: login-signup.php?action=login");
    }
    else if (empty($password)) {
        $_SESSION['error2'] = "Please enter your password";
        header("location: login-signup.php?action=login");
    }
    else if (strlen($password) < $minlenght){
        $_SESSION['error2'] = "Please enter a valid password";
        header("location: login-signup.php?action=login");
    }
    else{
        try{

            $stmt =$pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $userdata = $stmt->fetch();

            if ($userdata && password_verify($password,$userdata['password'])){
                $_SESSION['user_id'] = $userdata['id'];
                 header("location: main.php");
                }
             else {
                $_SESSION['error2'] = "Invalid username or password";
               header("location: login-signup.php?action=login");
                }
            } catch(PDOException $e){
            $_SESSION['error2'] = "Something went wrong,please try again";
            echo "Registration Fail".$e->getmessage();
            header("location: login-signup.php?action=login");

            }

        }


?>