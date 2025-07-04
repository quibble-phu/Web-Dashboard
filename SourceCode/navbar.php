<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
?>

<header class="p-3 text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <i class="bi bi-gear fs-2 me-2"></i>
                <span class="fs-4 fw-bold" style="color: #ffc100">PM UNIT |</span>
            </a>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <li><a href="index.php" class="nav-link px-2 text-white">Home</a></li>
                <li><a href="main.php" class="nav-link px-2 text-warning">Features</a></li>
                <li><a href="#" class="nav-link px-2 text-white">Dashboard</a></li>
                <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
                <li><a href="#" class="nav-link px-2 text-white">Contact</a></li>
            </ul>
            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search" id="search">
                <input type="search" class="form-control form-control-dark text-bg-light" placeholder="Search..." aria-label="Search">
            </form>
            <div class="text-end d-flex align-items-center gap-2">
                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <a href="login-signup.php?action=login" class="btn btn-outline-light me-2" id="navbarLogin">Login</a>
                    <a href="login-signup.php?action=signup" class="btn btn-warning" id="navbarSignup">Sign-up</a>
                <?php } else { ?>
                    <?php

                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                    }
                    try {

                        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $userdata = $stmt->fetch();
                    } catch (PDOException $e) {
                        echo "Registration Fail" . $e->getmessage();
                    }

                    ?>
                    <?php
                    $profileImage = !empty($userdata['profile_image'])
                        ? 'uploads/' . $userdata['profile_image']
                        : 'Pic/png-transparent-default-avatar.png';
                    ?>
                    <a href="user-settings.php">
                        <img src="<?php echo $profileImage; ?>"
                            alt="avatar"
                            width="40"
                            height="40"
                            class="rounded-circle border border-warning"
                            style="object-fit: cover; display: block;">
                    </a>
                    <span class="navbar-brand mb-0 h1 me-4">Welcome | <?php echo $userdata['username'] ?></span>
                    <button class="btn btn-danger" onclick="logoutConfirm()">Logout</button>
                <?php } ?>



            </div>
        </div>
    </div>
</header>