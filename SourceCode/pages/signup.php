<?php
session_start();
include('../backend/track_session.php');
require_once('../backend/condb.php');

if (!isset($_SESSION['user_id'])) {
    header("location: ../pages/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userdata = $stmt->fetch();

if (!in_array($userdata['role'], ['admin', 'co-admin'])) {
    header("location: ../pages/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Signup</title>
    <?php include('../index/head.php'); ?>
    <style>
        body {
            background: #f5f6fa;
        }

        .signup-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 4rem 3rem;
            /* เดิมคือ 3rem 2rem */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }


        @media (max-width: 767.98px) {
            .form-grid .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include('../index/navbar.php'); ?>

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="row w-100">
            <div class="col-12 col-sm-10 col-md-10 col-lg-8 mx-auto signup-card">
                <form action="../backend/signup_db.php" method="POST" class="w-100">
                    <h2 class="mb-4 text-center fw-bold">
                        <span style="color: black;">SIGN</span>
                        <span style="color:rgb(30, 118, 25);">UP</span>
                    </h2>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success'];
                                                            unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error'];
                                                        unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="row g-3 form-grid">
                        <div class="col-md-6">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="col-md-6">
                            <input type="password" name="password" class="form-control" placeholder="Password (min 6 characters)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="password" name="confirmpassword" class="form-control" placeholder="Confirm Password" required>
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="firstname" class="form-control" placeholder="Firstname" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="lastname" class="form-control" placeholder="Lastname" required>
                        </div>

                        <div class="col-md-6">
                            <select name="team" class="form-select" required>
                                <option value="">-- Select Team --</option>
                                <option value="PM">PM</option>
                                <option value="PD1">PD1</option>
                                <option value="PD2">PD2</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="position" class="form-select" required>
                                <option value="">-- Select Position --</option>
                                <option value="Staff">Staff</option>
                                <option value="MGR.">MGR.</option>
                                <option value="DGM.">DGM.</option>
                                <option value="GM.">GM.</option>
                                <option value="SSV.">SSV.</option>
                                <option value="SV.">SV.</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <select name="role" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                <option value="user">User</option>
                                <option value="operator">Operator</option>
                                <option value="engineer">Engineer</option>
                                <option value="co-admin">Co-Admin</option>
                                <?php if ($userdata['role'] === 'admin'): ?>
                                    <option value="admin">Admin</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <button class="btn btn-success w-100 mt-4" name="register">Submit</button>
                </form>
                <a href="../pages/admin-panel.php" class="btn btn-primary w-25 mt-3" >Back</a>
            </div>
        </div> 
    </div>

    <?php include('../index/footer.php'); ?>
    <?php include('../index/script.php'); ?>
</body>

</html>