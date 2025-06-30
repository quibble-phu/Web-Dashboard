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
                รายชื่อผู้ใช้ทั้งหมด
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
                            $i = 1;

                            foreach ($users as $user) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['password']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td>
                                <a href='admin-edit.php?id={$user['id']}' class='btn btn-sm btn-warning'>แก้ไข</a>
                                <a href='delete-user.php?id={$user['id']}' class='btn btn-sm btn-danger' >ลบ</a>

                              </td>";
                                echo "</tr>";
                                $i++;
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</td></tr>";
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

</body>

</html>