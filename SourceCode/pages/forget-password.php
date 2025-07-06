<?php
session_start();
require '../backend/condb.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // ตรวจสอบว่า email มีอยู่จริงใน employee หรือไม่ และดึง user_id ออกมา
    $stmt = $pdo->prepare("SELECT user_id FROM employee WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $user_id = $user['user_id'];

        // สร้าง token
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // ลบ token เก่า (ถ้ามี) สำหรับ user_id นี้
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user_id]);

        // บันทึก token ใหม่
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $token, $expires_at]);

        // สร้างลิงก์ reset password
        $reset_link = "http://localhost/Web_PM/SourceCode/pages/reset-password.php?token=$token";

        // แสดงลิงก์บนหน้าเว็บ (ในระบบจริงให้ส่งอีเมล)
        $_SESSION['reset_link'] = $reset_link;
        $_SESSION['reset_success'] = true;
    } else {
        $_SESSION['reset_error'] = "Email not found in the system.";
    }

    header("Location: forget-password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <?php include('../index/head.php'); ?>
</head>

<body class="bg-light">

    <?php include('../index/navbar.php'); ?>
    <div class="container mt-5">
        <div class="card mx-auto shadow" style="max-width: 500px;">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Forgot Password</h3>
                <?php if (isset($_SESSION['reset_success'])): ?>
                    <div class="alert alert-success">
                        Password reset link created successfully. <br>
                        <a href="<?= $_SESSION['reset_link']; ?>" target="_blank">Click here to reset password</a>
                    </div>
                    <?php unset($_SESSION['reset_success'], $_SESSION['reset_link']); ?>
                <?php elseif (isset($_SESSION['reset_error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['reset_error']; ?>
                    </div>
                    <?php unset($_SESSION['reset_error']); ?>
                <?php endif; ?>

                <form method="POST" action="forget-password.php">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Enter your registered email</label>
                        <input type="email" class="form-control" name="email" required placeholder="example@email.com">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>
                <div class="text-end mt-3">
                    <a href="login.php">Back to login</a>
                </div>
            </div>
        </div>
    </div>
     <?php include('../index/footer.php'); ?>
    <?php include('../index/script.php'); ?>
</body>

</html>
