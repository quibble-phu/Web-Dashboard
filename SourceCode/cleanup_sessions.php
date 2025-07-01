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
    header('HTTP/1.1 403 Forbidden'); //ส่ง 403 บอกว่าไม่มีสิทธิ์
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !in_array($user['role'], ['admin', 'co-admin'])) {
    header('HTTP/1.1 403 Forbidden'); //ส่ง 403 บอกว่าไม่มีสิทธิ์
    echo "Permission denied.";
    exit;
}

$cutoff = date('Y-m-d H:i:s', strtotime('-30 minutes'));
$stmt = $pdo->prepare("DELETE FROM user_sessions WHERE last_activity < ?");
if ($stmt->execute([$cutoff])) {
    echo "ลบ session เก่าที่ไม่ active เกิน 30 นาทีเรียบร้อยแล้ว";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการลบ session";
}
