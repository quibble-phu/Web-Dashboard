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

$showAlert = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("location: login-signup.php");
}
try {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $userdata = $stmt->fetch();
} catch (PDOException $e) {
    echo "Registration Fail" . $e->getmessage();
}
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
    <style>
        .transition-arrow {
            transition: transform 0.3s ease;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>

</head>

<body>
    <!-- Sidebar -->
    <?php include('menu.php'); ?>
    <!-- Sidebar end-->

    <!-- Main Content -->
    <div id="content">

        <!-- navbar main -->
        <?php include('navbar-main.php'); ?>
        <!-- navbar main end -->



        <!-- content -->
        <?php
        $threshold = date('Y-m-d H:i:s', strtotime('-5 minutes'));

        $stmt = $pdo->prepare("
    SELECT users.username
    FROM user_sessions
    JOIN users ON users.id = user_sessions.user_id
    WHERE user_sessions.last_activity >= ?
");
        $stmt->execute([$threshold]);
        $online_users = $stmt->fetchAll();
        ?>

        <div class="card mt-4 ms-3 me-3">
            <div class="card-header bg-success text-white ">
                <i class="bi bi-broadcast-pin fs-4"></i><span class="fs-4 ms-1"><strong>Online Users</strong></span>
            </div>
            <div class="card-body">
                <?php foreach ($online_users as $user): ?>
                    <span class="badge bg-primary me-1"><?= htmlspecialchars($user['username']) ?></span>
                <?php endforeach; ?>
                <?php if (empty($online_users)): ?>
                    <p class="text-muted">No user online</p>
                <?php endif; ?>
            </div>
        </div>


        <button id="btnClearSessions" class="btn btn-warning ms-3 mt-3">Kill InActive Session</button>




        <div class="card mt-4 ms-3 me-3">
            <div class="card-header bg-primary text-white ">
                <i class="bi bi-incognito fs-4"></i><span class="fs-4 ms-1"><strong>Users Component</strong> </span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="myTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#id</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Hashed_Password</th>
                            <th>Role</th>
                            <th>Config</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $pdo->prepare("SELECT * FROM users ORDER BY id ASC");
                            $stmt->execute();
                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($users as $user) {
                                echo "<tr id='row-{$user['id']}'>";
                                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['password']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td>
                                <a href='admin-edit.php?id={$user['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                <button class='btn btn-sm btn-danger' onclick='confirmDeleteAJAX({$user['id']})'>Delete</button>
                            </td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>

                </table>
            </div>




        </div>
        <!-- content end -->

        <!-- foooter -->
        <?php
        include('footer.php');
        ?>

        <?php include('script.php'); ?>


    </div>
    <!-- Main content end -->



    <!-- on off menu -->
    <script src="main.js"></script>
    <script>
        function confirmDeleteAJAX(userId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ส่งคำสั่งลบไปยัง PHP ด้วย fetch
                    fetch('admin-delete.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + userId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('ลบสำเร็จ', data.message, 'success');
                                document.getElementById('row-' + userId).remove(); // ลบแถวออกจากตาราง
                            } else {
                                Swal.fire('ผิดพลาด', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                        });
                }
            });
        }
    </script>
    <script>
        document.getElementById('btnClearSessions').addEventListener('click', function() {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบ session ที่ไม่ active เกิน 30 นาทีหรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('cleanup_sessions.php')
                        .then(async response => {
                            const text = await response.text();
                            if (response.ok) {
                                Swal.fire('สำเร็จ', text, 'success');
                            } else {
                                Swal.fire('ผิดพลาด', text, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('ผิดพลาด', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                        });
                }
            });
        });
    </script>



</body>

</html>