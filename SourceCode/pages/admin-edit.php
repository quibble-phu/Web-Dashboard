<?php include('../backend/track_session.php'); ?>
<?php

if (!isset($_SESSION['user_id'])) {
    header("location: ../pages/login.php");
    exit;
}


// ดึงข้อมูลผู้ใช้ที่ login อยู่
$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userdata = $stmt->fetch();

if (!in_array($userdata['role'], ['admin', 'co-admin'])) {
    header("location: ../pages/main.php");
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('../index/head.php'); ?>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">
    <?php include('../index/script.php'); ?>
</head>

<body>
    <?php include('../index/menu.php'); ?>
    <div id="content">
        <?php include('../index/navbar-main.php'); ?>






        <?php
        // ดึงข้อมูลผู้ใช้ที่จะแก้ไข
        if (!isset($_GET['user_id'])) {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something Wrong',
                text: 'Not found user'
            }).then(() => {
                window.location.href = '../pages/admin-panel.php';
            });
        </script>";
            exit;
        }

        $user_id_to_edit = $_GET['user_id'];
        $stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
        $stmt->execute([$user_id_to_edit]);
        $user_to_edit = $stmt->fetch();

        if (!$user_to_edit) {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Something Wrong',
                text: 'Not found user'
            }).then(() => {
                window.location.href = '../pages/admin-panel.php';
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
                window.location.href = '../pages/admin-panel.php';
            });
        </script>";
            exit;
        }

        // อัปเดตข้อมูลเมื่อส่ง form
        if (isset($_POST['update'])) {
            // รับค่าจากฟอร์ม
            $id = $_POST['user_id'];
            $newUsername = trim($_POST['username']);
            $newEmail = trim($_POST['email']);
            $newRole = $_POST['role'];

            // เช็คฟอร์แมต email
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Email ไม่ถูกต้อง',
            text: 'กรุณากรอกอีเมลให้ถูกต้อง'
        }).then(() => {
            window.history.back();
        });
    </script>";
                exit;
            }

            // เช็ค username ซ้ำ (ยกเว้นตัวเอง)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE username = ? AND user_id != ?");
            $stmt->execute([$newUsername, $id]);
            if ($stmt->fetchColumn() > 0) {
                echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Username ซ้ำ',
            text: 'มีผู้ใช้ชื่อเดียวกันแล้ว กรุณาเปลี่ยนชื่อใหม่'
        }).then(() => {
            window.history.back();
        });
    </script>";
                exit;
            }

            // เช็ค email ซ้ำ (ยกเว้นตัวเอง)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE email = ? AND user_id != ?");
            $stmt->execute([$newEmail, $id]);
            if ($stmt->fetchColumn() > 0) {
                echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Email ซ้ำ',
            text: 'มีอีเมลนี้ในระบบแล้ว กรุณาใช้ email อื่น'
        }).then(() => {
            window.history.back();
        });
    </script>";
                exit;
            }

           


            // co-admin ห้ามแก้เป็น admin
            if ($userdata['role'] === 'co-admin' && $newRole === 'admin') {
                echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์',
                text: 'คุณไม่สามารถอัปเดตเป็น admin ได้'
            }).then(() => {
                window.location.href = '../pages/admin-panel.php';
            });
        </script>";
                exit;
            }

            $stmt = $pdo->prepare("UPDATE employee SET username = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$newUsername, $newEmail, $newRole, $id]);

            echo "<script>
                Swal.fire({
                icon: 'success',
                title: 'อัปเดตสำเร็จ',
                text: 'ข้อมูลผู้ใช้ได้รับการอัปเดตเรียบร้อยแล้ว',
                timer: 2500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '../pages/admin-panel.php';
            });
        </script>";
            exit;
        }
        ?>


        <div class="container-fluid p-4">
            <h3 class="mb-4">แก้ไขข้อมูลผู้ใช้</h3>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="user_id" value="<?= $user_to_edit['user_id'] ?>">


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
                        <option value="operator" <?= $user_to_edit['role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                        <option value="engineer" <?= $user_to_edit['role'] === 'engineer' ? 'selected' : '' ?>>Engineer</option>
                        <option value="co-admin" <?= $user_to_edit['role'] === 'co-admin' ? 'selected' : '' ?>>Co-Admin</option>
                        <?php if ($userdata['role'] === 'admin'): ?>
                            <option value="admin" <?= $user_to_edit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <?php endif; ?>
                        
                    </select>
                </div>
                <button type="submit" name="update" id="confirm" style="display:none;"></button>
                <button type="button" class="btn btn-success" onclick="confirmUpdate()">Save</button>
                <a href="../pages/admin-panel.php" class="btn btn-danger">Cancel</a>
            </form>
        </div>
        <?php include('../index/footer.php'); ?>
    </div>
    <script src="../js/main.js"></script>
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