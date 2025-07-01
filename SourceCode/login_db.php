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