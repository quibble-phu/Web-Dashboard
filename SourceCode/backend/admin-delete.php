<?php include('../backend/track_session.php'); ?>
<?php

require_once('../backend/condb.php');


header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userdata = $stmt->fetch();

if (!in_array($userdata['role'], ['admin', 'co-admin'])) {
    header("location: ../pages/main.php");
    exit;
}


$current_user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
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
$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
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
$stmt = $pdo->prepare("DELETE FROM employee WHERE user_id = ?");
$stmt->execute([$target_id]);

echo json_encode(['success' => true, 'message' => 'ลบผู้ใช้เรียบร้อยแล้ว']);
exit;
