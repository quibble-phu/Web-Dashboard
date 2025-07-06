<?php
require '../backend/condb.php';
session_start();

$user_id = $_POST['user_id'];
$otp = $_POST['otp'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match!";
    header("Location: ../pages/enter-otp.php?user_id=$user_id");
    exit;
}

// ตรวจสอบ OTP
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE user_id = ? AND otp = ? AND used = 0 AND expires_at >= NOW()");
$stmt->execute([$user_id, $otp]);
$otpData = $stmt->fetch();

if ($otpData) {
    // เปลี่ยนรหัสผ่าน
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE employee SET password = ? WHERE user_id = ?")->execute([$hashedPassword, $user_id]);

    // mark otp ว่าใช้แล้ว
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$otpData['id']]);

    $_SESSION['success'] = "Password reset successfully!";
    header("Location: ../pages/login.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid or expired OTP!";
    header("Location: ../pages/enter-otp.php?user_id=$user_id");
    exit;
}
