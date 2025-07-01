<?php
session_start();
require 'condb.php';

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

if (!isset($_SESSION['user_id'])) {
    header("Location: login-signup.php?action=login");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $date = date('Ymd');
    $newFileName = "profile_" . $user_id . "_" . $date . "." . $ext;
    $targetDir = "uploads/";
    $targetPath = $targetDir . $newFileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$newFileName, $user_id]);

        $_SESSION['success'] = "อัปโหลดรูปโปรไฟล์สำเร็จ";
        header("Location: user-settings.php");
        exit();
    } else {
        $_SESSION['error'] = "ไม่สามารถอัปโหลดไฟล์ได้";
        header("Location: user-settings.php");
        exit();
    }
} else {
    $_SESSION['error'] = "กรุณาเลือกไฟล์";
    header("Location: user-settings.php");
    exit();
}
?>
