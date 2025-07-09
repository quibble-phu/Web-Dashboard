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
            $newPassword = trim($_POST['password']);
            $minlength = 6;
            // เข้ารหัสรหัสผ่าน
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $newFirstname  = trim($_POST['fname']);
            $newLastname  = trim($_POST['lname']);
            $newTeam = $_POST['team'];
            $newPosition = $_POST['position'];
            $newRole = $_POST['role'];


            $errors = [];

            // ตรวจสอบแต่ละฟิลด์
            if (empty($newUsername)) {
                $errors[] = "กรุณากรอก Username";
            }
            if (empty($newEmail)) {
                $errors[] = "กรุณากรอก Email";
            } else if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "รูปแบบ Email ไม่ถูกต้อง";
            }
            // check pass
            if (!empty($newPassword)) {

                if (strlen($newPassword) < $minlength) {
                    $errors[] = "Password ต้องมีอย่างน้อย $minlength ตัวอักษร";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            } else {
                // ใช้ password เดิมจากฐานข้อมูล
                $hashedPassword = $user_to_edit['password'];
            }

            if (empty($newFirstname)) {
                $errors[] = "กรุณากรอก First Name";
            }
            if (empty($newLastname)) {
                $errors[] = "กรุณากรอก Last Name";
            }


            // ตรวจ username ซ้ำ
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE username = ? AND user_id != ?");
            $stmt->execute([$newUsername, $id]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Username ซ้ำ กรุณาเลือกชื่อใหม่";
            }

            // ตรวจ email ซ้ำ
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM employee WHERE email = ? AND user_id != ?");
            $stmt->execute([$newEmail, $id]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Email นี้ถูกใช้แล้ว กรุณาใช้อีเมลอื่น";
            }

            // ตรวจสิทธิ์ co-admin ไม่ให้เปลี่ยนเป็น admin
            if ($userdata['role'] === 'co-admin' && $newRole === 'admin') {
                $errors[] = "คุณไม่มีสิทธิ์เปลี่ยนผู้ใช้เป็น Admin";
            }

            // ถ้ามี error แสดงทั้งหมด
            if (!empty($errors)) {
                $errorMessage = implode("<br>", $errors);
                echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'พบข้อผิดพลาด',
            html: '$errorMessage'
        }).then(() => {
            window.history.back();
        });
    </script>";
                exit;
            }


            $stmt = $pdo->prepare("UPDATE employee SET username = ?, email = ?, password = ?
            , first_name = ?, last_name = ?, team = ?, position = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$newUsername, $newEmail, $hashedPassword,  $newFirstname, $newLastname, $newTeam, $newPosition, $newRole, $id]);
            echo "<script>
                Swal.fire({
                icon: 'success',
                title: 'อัปเดตสำเร็จ',
                text: 'ข้อมูลผู้ใช้ได้รับการอัปเดตเรียบร้อยแล้ว',
                timer: 1000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '../pages/admin-panel.php';
            });
        </script>";
            exit;
        }
        ?>


        <div class="container-fluid p-4">
            <h3 class="mb-4">Edit <?= htmlspecialchars($user_to_edit['username']) ?> Info</h3>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="user_id" value="<?= $user_to_edit['user_id'] ?>">

                <div class="row">
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_to_edit['username']) ?>">
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_to_edit['email']) ?>" placeholder="example@gmail.com">
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="ใส่รหัสใหม่ (ถ้าต้องการเปลี่ยน) min 6 Character"
                                autocomplete="off">
                            <button class="btn" type="button" id="adminedit"
                                style="position: absolute; top: calc(50% - 11px); right: 10px; border: none; background: transparent; padding: 0; cursor: pointer; height: 24px; width: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                            </button>
                        </div>
                    </div>




                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($user_to_edit['first_name']) ?>">
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($user_to_edit['last_name']) ?>">
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Team</label>
                        <select name="team" class="form-select" required>
                            <option value="PM" <?= $user_to_edit['team'] === 'PM' ? 'selected' : '' ?>>PM</option>
                            <option value="PD1" <?= $user_to_edit['team'] === 'PD1' ? 'selected' : '' ?>>PD1</option>
                            <option value="PD2" <?= $user_to_edit['team'] === 'PD2' ? 'selected' : '' ?>>PD2</option>
                        </select>
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select" required>
                            <option value="Staff" <?= $user_to_edit['position'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="MGR." <?= $user_to_edit['position'] === 'MGR.' ? 'selected' : '' ?>>MGR.</option>
                            <option value="DGM." <?= $user_to_edit['position'] === 'DGM.' ? 'selected' : '' ?>>DGM.</option>
                            <option value="GM." <?= $user_to_edit['position'] === 'GM.' ? 'selected' : '' ?>>GM.</option>
                            <option value="SSV." <?= $user_to_edit['position'] === 'SSV.' ? 'selected' : '' ?>>SSV.</option>
                            <option value="SV." <?= $user_to_edit['position'] === 'SV.' ? 'selected' : '' ?>>SV.</option>
                        </select>
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="user" <?= $user_to_edit['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="operator" <?= $user_to_edit['role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                            <option value="engineer" <?= $user_to_edit['role'] === 'engineer' ? 'selected' : '' ?>>Engineer</option>
                            <option value="co-admin" <?= $user_to_edit['role'] === 'co-admin' ? 'selected' : '' ?>>Co-Admin</option>
                            <?php if ($userdata['role'] === 'admin'): ?>
                                <option value="admin" <?= $user_to_edit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <?php endif; ?>

                        </select>
                    </div>
                </div>
                <button type="submit" name="update" id="confirm" style="display:none;"></button>
                <button type="button" class="btn btn-success" onclick="confirmUpdate()">Save</button>
                <a href="../pages/admin-panel.php" class="btn btn-danger">Cancel</a>
            </form>
        </div>

        <?php include('../index/footer.php'); ?>
    </div>
    <!-- showpass -->
    <script>
        const adminedit = document.getElementById("adminedit");
        if (adminedit) {
            adminedit.addEventListener("click", function() {
                const passwordInput = document.getElementById("password");
                const eyeIcon = document.getElementById("eyeIcon1");

                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    eyeIcon.classList.remove("bi-eye-slash");
                    eyeIcon.classList.add("bi-eye");
                } else {
                    passwordInput.type = "password";
                    eyeIcon.classList.remove("bi-eye");
                    eyeIcon.classList.add("bi-eye-slash");
                }
            });
        }
    </script>

    <script src="../js/main.js"></script>
    <!-- sweetalert2 -->
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