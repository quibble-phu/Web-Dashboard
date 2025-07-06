<?php
session_start();
require 'condb.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้เข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบว่าเป็น admin หรือ co-admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!in_array($user['role'], ['admin', 'co-admin'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์']);
    exit;
}

if (!isset($_POST['session_id'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มี session ที่จะลบ']);
    exit;
}

$session_id = $_POST['session_id'];
$stmt = $pdo->prepare("DELETE FROM user_sessions WHERE session_id = ?");
$stmt->execute([$session_id]);


echo json_encode(['success' => true, 'message' => 'ลบ session สำเร็จ']);
