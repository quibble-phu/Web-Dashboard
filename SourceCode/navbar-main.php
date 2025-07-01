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


<nav class="navbar navbar-light bg-light border-bottom" id="top-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-info" id="toggleSidebar">☰</button>
        <a href="index.php" class="text-decoration-none fw-bold">🏠 Home</a>
        <a href="index.php" class="text-decoration-none fw-bold">📞 Contact</a>

    </div>
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





    <span class="navbar-brand mb-0 h1 d-flex align-items-center gap-2">
        <a href="user-settings.php">
            <img src="<?php echo $profileImage; ?>"
                alt="avatar"
                width="40"
                height="40"
                class="rounded-circle border border-warning"
                style="object-fit: cover; display: block;">
        </a>
        <span>Welcome | <?php echo $userdata['username'] ?></span>
    </span>
</nav>