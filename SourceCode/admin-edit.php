<?php
session_start();
require "condb.php";

if (!isset($_SESSION['user_id'])) {
    header("location: login-signup.php");
    exit;
}

// ดึงข้อมูลผู้ใช้ที่ login อยู่
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userdata = $stmt->fetch();

if (!in_array($userdata['role'], ['admin', 'co-admin'])) {
    header("location: main.php");
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('head.php'); ?>
    <link href="style.css" rel="stylesheet">
    <link href="colormode.css" rel="stylesheet">
    <?php include('script.php'); ?>
</head>

<body>
    <?php include('menu.php'); ?>
    <div id="content">
        <?php include('navbar-main.php'); ?>

        




        <?php
        // ดึงข้อมูลผู้ใช้ที่จะแก้ไข
        if (!isset($_GET['id'])) {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something Wrong',
                text: 'Not found user'
            }).then(() => {
                window.location.href = 'admin-panel.php';
            });
        </script>";
            exit;
        }

        $user_id_to_edit = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id_to_edit]);
        $user_to_edit = $stmt->fetch();

        if (!$user_to_edit) {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something Wrong',
                text: 'Not found user'
            }).then(() => {
                window.location.href = 'admin-panel.php';
            });
        </script>";
            exit;
        }

        // co-admin แก้ admin ไม่ได้
        if ($userdata['role'] === 'co-admin' && $user_to_edit['role'] === 'admin') {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์',
                text: 'คุณไม่มีสิทธิ์แก้ไขผู้ดูแลระบบ'
            }).then(() => {
                window.location.href = 'admin-panel.php';
            });
        </script>";
            exit;
        }

        // อัปเดตข้อมูลเมื่อส่ง form
        if (isset($_POST['update'])) {
            $id = $_POST['id'];
            $newUsername = $_POST['username'];
            $newEmail = $_POST['email'];
            $newRole = $_POST['role'];

            // co-admin ห้ามแก้เป็น admin
            if ($userdata['role'] === 'co-admin' && $newRole === 'admin') {
                echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์',
                text: 'คุณไม่สามารถอัปเดตเป็น admin ได้'
            }).then(() => {
                window.location.href = 'admin-panel.php';
            });
        </script>";
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([$newUsername, $newEmail, $newRole, $id]);
            
            echo "<script>
                Swal.fire({
                icon: 'success',
                title: 'อัปเดตสำเร็จ',
                text: 'ข้อมูลผู้ใช้ได้รับการอัปเดตเรียบร้อยแล้ว',
                timer: 2500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'admin-panel.php';
            });
        </script>";
            exit;
        }
        ?>


        <div class="container-fluid p-4">
            <h3 class="mb-4">แก้ไขข้อมูลผู้ใช้</h3>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="id" value="<?= $user_to_edit['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_to_edit['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_to_edit['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= $user_to_edit['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="co-admin" <?= $user_to_edit['role'] === 'co-admin' ? 'selected' : '' ?>>Co-Admin</option>
                        <?php if ($userdata['role'] === 'admin'): ?>
                            <option value="admin" <?= $user_to_edit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                 <button type="submit" name="update" id="confirm" style="display:none;" ></button>            
                <button type="button"  class="btn btn-success" onclick="confirmUpdate()" >Save</button>
                <a href="admin-panel.php" class="btn btn-danger">Cancel</a>
            </form>
        </div>
        <?php include('footer.php'); ?>
    </div>
    <script src="main.js"></script>
    <script>
        function confirmUpdate() {
            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: "คุณต้องการบันทึกการเปลี่ยนแปลงหรือไม่?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirm').click();
                }
            });
        }
    </script>


</body>

</html>