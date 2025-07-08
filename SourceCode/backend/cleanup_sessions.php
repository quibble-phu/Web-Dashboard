<?php include('../backend/track_session.php'); ?>
<?php
require_once 'condb.php';

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM employee WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !in_array($user['role'], ['admin', 'co-admin'])) {
    header('HTTP/1.1 403 Forbidden');
    echo "Permission denied.";
    exit;
}

// ตั้งเวลาตัด session
$cutoff = date('Y-m-d H:i:s', strtotime('-30 minutes'));

// ลบ session ที่หมดเวลาแล้ว
$stmt = $pdo->prepare("DELETE FROM user_sessions WHERE last_activity < ?");
if ($stmt->execute([$cutoff])) {
    $deleted = $stmt->rowCount(); // ได้จำนวนแถวที่ลบ
    echo "ลบ session เก่าที่ไม่ active เกิน 30 นาทีเรียบร้อยแล้ว: $deleted รายการ";
} else {
    http_response_code(500);
    echo "เกิดข้อผิดพลาดในการลบ session";
}
