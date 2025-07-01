<?php
session_start();
require 'condb.php';

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


if (!isset($_SESSION['user_id'])) {
    header('Location: login-signup.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

if (!in_array($currentUser['role'], ['admin', 'co-admin'])) {
    echo "Permission denied.";
    exit;
}

// ดึง sessions ทั้งหมด
$stmt = $pdo->prepare("
    SELECT us.session_id, us.user_id, us.last_activity, us.created_at, u.username, u.role
    FROM user_sessions us
    JOIN users u ON u.id = us.user_id
    ORDER BY us.last_activity DESC
");
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <?php include('head.php'); ?>
    <link href="style.css" rel="stylesheet">
    <link href="colormode.css" rel="stylesheet">
    <style>

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
        $stmt = $pdo->prepare("
            SELECT us.session_id, us.user_id, us.last_activity, us.created_at, u.username, u.role
            FROM user_sessions us
            JOIN users u ON us.user_id = u.id
            ORDER BY us.last_activity DESC
        ");
        $stmt->execute();
        $sessions = $stmt->fetchAll();

        ?>

        <div class="card mt-4 ms-3 me-3">
            <div class="card-header bg-success text-white ">
                <i class="bi bi-broadcast-pin fs-4"></i><span class="fs-4 ms-1"><strong>Online Users</strong></span>
            </div>
            <div class="card-body">
                <?php foreach ($online_users as $user): ?>
                    <span class="badge bg-primary me-1 "><?= htmlspecialchars($user['username']) ?></span>
                <?php endforeach; ?>
                <?php if (empty($online_users)): ?>
                    <p class="text-muted">No user online</p>
                <?php endif; ?>
            </div>
        </div>


        <button id="btnClearSessions" class="btn btn-warning ms-3 mt-3">Kill InActive Session</button>


        <div class="card mt-4 ms-3 me-3">
            <div class="card-header bg-info text-white ">
                <i class="bi bi-broadcast-pin fs-4"></i><span class="fs-4 ms-1"><strong>🧾 Active Sessions</strong></span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped" id="myTable">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Session ID</th>
                            <th>Created At</th>
                            <th>Last Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td><?= htmlspecialchars($session['username']) ?></td>
                                <td><?= htmlspecialchars($session['role']) ?></td>
                                <td><?= htmlspecialchars($session['session_id']) ?></td>
                                <td><?= htmlspecialchars($session['created_at']) ?></td>
                                <td><?= htmlspecialchars($session['last_activity']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="killSession('<?= $session['session_id'] ?>')">
                                        Kill
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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

    <script>
        function killSession(sessionId) {
            Swal.fire({
                title: 'ลบ Session นี้?',
                text: 'คุณแน่ใจหรือไม่ที่จะลบ session นี้ออกจากระบบ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_session.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'session_id=' + encodeURIComponent(sessionId)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('ลบสำเร็จ', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('ผิดพลาด', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>





</body>

</html>