<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../backend/condb.php');

if (!empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // ตรวจสอบ user_id ว่ามีใน employee หรือไม่ (ถ้าต้องการ)
    $check = $pdo->prepare("SELECT 1 FROM employee WHERE user_id = ?");
    $check->execute([$user_id]);
    if ($check->fetch()) {
        $session_id = session_id();
        $now = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $stmt = $pdo->prepare("
            INSERT INTO user_sessions(user_id, session_id, ip_address, user_agent, last_activity, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)
        ");
        $stmt->execute([$user_id, $session_id, $ip, $user_agent, $now, $now]);
    }
}
