<?php include('../backend/track_session.php'); ?>
<?php require_once('../backend/condb.php'); ?>

<nav class="navbar navbar-light bg-light border-bottom" id="top-navbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-info" id="toggleSidebar">☰</button>
        <a href="../pages/index.php" class="text-decoration-none fw-bold">🏠 Home</a>
        <a href="../pages/index.php" class="text-decoration-none fw-bold">📞 Contact</a>
    </div>

    <?php
    $profileImage = '../Pic/png-transparent-default-avatar.png'; // default
    $usernameDisplay = 'Guest';

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $userdata = $stmt->fetch();

            if ($userdata) {
                $usernameDisplay = htmlspecialchars($userdata['username']);
                if (!empty($userdata['profile_image'])) {
                    $uploadPath = '../uploads/' . $userdata['profile_image'];
                    if (file_exists($uploadPath)) {
                        $profileImage = $uploadPath;
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }

    ?>

    <span class="navbar-brand mb-0 h1 d-flex align-items-center gap-2">
        <a href="../pages/user-settings.php">
            <img src="<?php echo $profileImage; ?>"
                alt="avatar"
                width="40"
                height="40"
                class="rounded-circle border border-warning"
                style="object-fit: cover; display: block;">
        </a>
        <span>Welcome | <?php echo $usernameDisplay; ?></span>
    </span>
</nav>