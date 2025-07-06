<?php include('../backend/track_session.php'); ?>
<?php
require_once 'condb.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $timestamp = time();
    $newFileName = "profile_" . $user_id . "_" . $timestamp . "." . $ext;

    $targetDir = "../uploads/";
    $targetPath = $targetDir . $newFileName;

    // ลบรูปเก่า
    $stmt = $pdo->prepare("SELECT profile_image FROM employee WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $oldData = $stmt->fetch();
    if ($oldData && !empty($oldData['profile_image'])) {
        $oldFile = $targetDir . $oldData['profile_image'];
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
    }

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $stmt = $pdo->prepare("UPDATE employee SET profile_image = ? WHERE user_id = ?");
        $stmt->execute([$newFileName, $user_id]);

        $_SESSION['upload_status'] = 'success';
        $_SESSION['upload_message'] = 'อัปโหลดรูปโปรไฟล์สำเร็จแล้ว!';
    } else {
        $_SESSION['upload_status'] = 'error';
        $_SESSION['upload_message'] = 'ไม่สามารถอัปโหลดไฟล์ได้';
    }
} else {
    $_SESSION['upload_status'] = 'error';
    $_SESSION['upload_message'] = 'กรุณาเลือกไฟล์ภาพ';
}

header("Location: ../pages/user-settings.php");
exit;
