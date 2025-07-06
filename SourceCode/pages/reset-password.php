<?php
session_start();
require '../backend/condb.php';

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT pr.id, pr.user_id, pr.expires_at, pr.used, e.email FROM password_resets pr JOIN employee e ON pr.user_id = e.user_id WHERE pr.token = ?");
$stmt->execute([$token]);
$resetData = $stmt->fetch();

if (!$resetData) {
    die("Invalid or expired token.");
}

$current_time = date("Y-m-d H:i:s");
if ($resetData['used'] || $resetData['expires_at'] < $current_time) {
    die("Token is expired or already used.");
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE employee SET password = ? WHERE user_id = ?");
            $stmt->execute([$password_hash, $resetData['user_id']]);

            $stmt = $pdo->prepare("UPDATE password_resets SET used = TRUE WHERE id = ?");
            $stmt->execute([$resetData['id']]);

            $pdo->commit();

            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">
    <?php include('../index/head.php'); ?>

    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 38px;
            color: #666;
            user-select: none;
        }

        .position-relative {
            position: relative;
        }
    </style>
</head>

<body>
    <?php include('../index/navbar.php'); ?>
    <?php include('../index/script.php'); ?>
    <div class="container mt-5" style="max-width: 480px;">
        <h2>Reset Password</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Password has been reset successfully.',
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.href = 'login.php';
                });
            </script>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="post" action="">
                <div class="mb-3 position-relative">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6" class="form-control">
                    <span class="password-toggle" onclick="togglePassword('new_password', this)">👁️</span>
                </div>
                <div class="mb-3 position-relative">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6" class="form-control">
                    <span class="password-toggle" onclick="togglePassword('confirm_password', this)">👁️</span>
                </div>
                <button type="submit" class="btn btn-danger w-100">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include('../index/footer.php'); ?>

    <script>
        function togglePassword(fieldId, el) {
            const input = document.getElementById(fieldId);
            if (input.type === "password") {
                input.type = "text";
                el.textContent = "🙈";
            } else {
                input.type = "password";
                el.textContent = "👁️";
            }
        }
    </script>
</body>

</html>