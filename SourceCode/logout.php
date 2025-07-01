<?php
session_start();
require 'condb.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $session_id = session_id();

    // ลบเฉพาะ session นี้
    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ? AND session_id = ?");
    $stmt->execute([$user_id, $session_id]);

    session_destroy();
}

header("Location: login-signup.php");
exit;
?>