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

header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}


$current_user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$current_user_id]);
$current_user = $stmt->fetch();

if (!in_array($current_user['role'], ['admin', 'co-admin'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มี ID ที่จะลบ']);
    exit;
}

$target_id = $_POST['id'];

if ($target_id == $current_user_id) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบตัวเองได้']);
    exit;
}

// ดึงข้อมูลของ user ที่จะลบ
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$target_id]);
$target_user = $stmt->fetch();

if (!$target_user) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้']);
    exit;
}

if ($current_user['role'] === 'co-admin' && $target_user['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'co-admin ลบได้เฉพาะ user เท่านั้น']);
    exit;
}

// ลบ
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$target_id]);

echo json_encode(['success' => true, 'message' => 'ลบผู้ใช้เรียบร้อยแล้ว']);
exit;
