<?php include('../backend/track_session.php'); ?>
<?php

require_once('../backend/condb.php');

$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userdata = $stmt->fetch();

if (!in_array($userdata['role'], ['admin', 'co-admin'])) {
    header("location: ../pages/dashboard.php");
    exit;
}

$minlength = 6;

if (isset($_POST['register'])) {
    // รับค่าจากฟอร์ม
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $team = ($_POST['team']);
    $position = ($_POST['position']);
    $role = $_POST['role'];

    // ตรวจสอบความถูกต้อง
    if (empty($username)) {
        $_SESSION['error'] = "Please enter your username";
    } else if (empty($firstname)) {
        $_SESSION['error'] = "Please enter your firstname";
    } else if (empty($lastname)) {
        $_SESSION['error'] = "Please enter your lastname";
    } else if (empty($team)) {
        $_SESSION['error'] = "Please enter your team";
    } else if (empty($position)) {
        $_SESSION['error'] = "Please enter your position";
    } else if (empty($role)) {
        $_SESSION['error'] = "Please select a role";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address";
    } else if (strlen($password) < $minlength) {
        $_SESSION['error'] = "Password must be at least $minlength characters";
    } else if ($password !== $confirmpassword) {
        $_SESSION['error'] = "Passwords do not match";
    }

    // ถ้ามี error ให้ redirect กลับ
    if (isset($_SESSION['error'])) {
        header("location: ../pages/signup.php");
        exit;
    }

    // ตรวจสอบว่ามี username/email ซ้ำหรือไม่
    $checkUsername = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE username = ?");
    $checkUsername->execute([$username]);
    if ($checkUsername->fetchColumn()) {
        $_SESSION['error'] = "Username already exists";
        header("location: ../pages/signup.php");
        exit;
    }

    $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetchColumn()) {
        $_SESSION['error'] = "Email already exists";
        header("location: ../pages/signup.php");
        exit;
    }

    // เข้ารหัสรหัสผ่าน
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // บันทึกลงฐานข้อมูล
    try {
        $stmt = $pdo->prepare("INSERT INTO employee (username, email, password, first_name, last_name, team, position, role) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $username,
            $email,
            $hashedPassword,
            $firstname,
            $lastname,
            $team,
            $position,
            $role
        ]);

        $_SESSION['success'] = "Registration successful!";
        header("location: ../pages/signup.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        error_log("Signup error: " . $e->getMessage());
        header("location: ../pages/signup.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid access.";
    header("location: ../pages/signup.php");
    exit;
}
?>
