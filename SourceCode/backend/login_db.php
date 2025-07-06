<?php include('../backend/track_session.php'); ?>
<?php 

$minlength = 6;

if (isset($_POST['login'])) {
    $username = trim($_POST['username2']);
    $password = $_POST['password2'];

    if (empty($username)) {
        $_SESSION['error2'] = "Please enter your username";
        header("Location: ../pages/login.php");
        exit;
    } 
    else if (empty($password)) {
        $_SESSION['error2'] = "Please enter your password";
        header("Location: ../pages/login.php");
        exit;
    }
    else if (strlen($password) < $minlength) {
        $_SESSION['error2'] = "Please enter a valid password (at least {$minlength} characters)";
        header("Location: ../pages/login.php");
        exit;
    }
    else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM employee WHERE username = ?");
            $stmt->execute([$username]);
            $userdata = $stmt->fetch();

            if ($userdata && password_verify($password, $userdata['password'])) {
                // เก็บ session user_id ให้ตรงกับคอลัมน์ในฐานข้อมูล (user_id หรือ id)
                $_SESSION['user_id'] = $userdata['user_id']; // หรือ $userdata['id'] ขึ้นกับ schema

                // บันทึก session เข้า user_sessions
                $session_id = session_id();
                $now = date('Y-m-d H:i:s');
                $ip = $_SERVER['REMOTE_ADDR'];
                $user_agent = $_SERVER['HTTP_USER_AGENT'];

                $stmt2 = $pdo->prepare("
                    INSERT INTO user_sessions(user_id, session_id, ip_address, user_agent, last_activity, created_at)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)
                ");
                $stmt2->execute([$userdata['user_id'], $session_id, $ip, $user_agent, $now, $now]);

                header("Location: ../pages/main.php");
                exit;
            } else {
                $_SESSION['error2'] = "Invalid username or password";
                header("Location: ../pages/login.php");
                exit;
            }
        } catch(PDOException $e) {
            $_SESSION['error2'] = "Something went wrong, please try again later.";
           
            header("Location: ../pages/login.php");
            exit;
        }
    }
} else {
    // หากเปิดไฟล์นี้ตรงๆ โดยไม่ผ่าน POST
    header("Location: ../pages/login.php");
    exit;
}
?>
