<?php include('../backend/track_session.php'); ?>
<?php
session_start();
require_once 'condb.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$minLength = 6;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (strlen($newPassword) < $minLength) {
        $_SESSION['upload_status'] = 'error';
        $_SESSION['upload_message'] = 'New password must be at least 6 characters.';
        header("Location: ../pages/user-settings.php");
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        $_SESSION['upload_status'] = 'error';
        $_SESSION['upload_message'] = 'New passwords do not match.';
        header("Location: ../pages/user-settings.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT password FROM employee WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($oldPassword, $user['password'])) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE employee SET password = ? WHERE user_id = ?");
            $updateStmt->execute([$hashedNewPassword, $user_id]);

            $_SESSION['upload_status'] = 'success';
            $_SESSION['upload_message'] = 'Password changed successfully.';
        } else {
            $_SESSION['upload_status'] = 'error';
            $_SESSION['upload_message'] = 'Incorrect old password.';
        }
    } catch (PDOException $e) {
        $_SESSION['upload_status'] = 'error';
        $_SESSION['upload_message'] = 'Something went wrong.';
    }

    header("Location: ../pages/user-settings.php");
    exit;
}
?>
