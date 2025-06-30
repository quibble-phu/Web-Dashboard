<?php
session_start();
require "condb.php";
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
        <div class="card mt-4 ">
            <div class="card-header bg-primary text-white">
               <i class="bi bi-incognito fs-4"></i> <span class="fs-4"><strong>All Users</strong> </span> 
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
                                $canDelete = false;

                                // เงื่อนไขการลบ
                                if ($userdata['role'] === 'admin' && $user['id'] != $userdata['id']) {
                                    $canDelete = true;
                                } elseif ($userdata['role'] === 'co-admin' && $user['role'] === 'user') {
                                    $canDelete = true;
                                }

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['password']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td>
                                <a href='admin-edit.php?id={$user['id']}' class='btn btn-sm btn-warning'>Edit</a> ";

                                if ($canDelete) {
                                    echo "<button class='btn btn-sm btn-danger' onclick='confirmDelete({$user['id']})'>Delete</button>";
                                } else {
                                    echo "<button class='btn btn-sm btn-danger' onclick='showDeleteError()'>Delete</button>";
                                }

                                echo "</td></tr>";
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
        function confirmDelete(userId) {
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
                    window.location.href = `admin-delete.php?id=${userId}`;
                }
            });
        }

        function showDeleteError() {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถลบได้',
                text: 'คุณไม่มีสิทธิ์ในการลบผู้ใช้นี้'
            });
        }
    </script>


</body>

</html>