<?php
session_start();
require "condb.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login-signup.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้ที่ login อยู่
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$current_user_id]);
$current_user = $stmt->fetch();

// ตรวจสอบสิทธิ์
if (!in_array($current_user['role'], ['admin', 'co-admin'])) {
    header("Location: main.php");
    exit;
}

// ตรวจสอบว่ามี id ที่จะลบถูกส่งมาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: admin-panel.php");
    exit;
}

$target_user_id = $_GET['id'];

// ดึงข้อมูลของ user ที่จะลบ
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$target_user_id]);
$target_user = $stmt->fetch();

// เริ่ม HTML output เพื่อให้ใช้ Swal ได้
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete User</title>
    <?php include('script.php');  ?>
</head>
<body>
<?php
if (!$target_user) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบผู้ใช้',
            text: 'ไม่พบผู้ใช้ที่ต้องการลบ'
        }).then(() => {
            window.location.href = 'admin-panel.php';
        });
    </script>";
    exit;
}

if ($target_user_id == $current_user_id) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ลบตัวเองไม่ได้',
            text: 'คุณไม่สามารถลบบัญชีของตัวเองได้'
        }).then(() => {
            window.location.href = 'admin-panel.php';
        });
    </script>";
    exit;
}

if ($current_user['role'] === 'co-admin' && $target_user['role'] !== 'user') {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'สิทธิ์ไม่เพียงพอ',
            text: 'คุณไม่สามารถลบ admin หรือ co-admin ได้'
        }).then(() => {
            window.location.href = 'admin-panel.php';
        });
    </script>";
    exit;
}

// ✅ ลบจริง
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$target_user_id]);

echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: 'ลบผู้ใช้เรียบร้อยแล้ว',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'admin-panel.php';
    });
</script>";
exit;
?>
</body>
</html>
