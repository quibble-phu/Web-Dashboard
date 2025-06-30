<!-- Sidebar -->
<div id="sidebar" class="bg-dark text-white position-fixed h-100">
    <div class="sidebar-header">
        <i class="bi bi-tools fs-2"></i>
        <span class="sidebar-text" style="color: #ffc100 ;">PM UNIT</span>

    </div>
    <!-- Search -->
    <div class="sidebar-search px-3 py-2">
        <input type="text" class="form-control form-control-sm" id="sidebarSearch" placeholder="🔍 Search..." />
    </div>

    <div class="sidebar-nav flex-grow-1 d-flex flex-column">
        <a href="main.php"><i class="bi bi-diagram-3 fs-4"></i></i> <span class="sidebar-text">Welcome</span></a>
        <a href="dashboard.php"><i class="bi bi-display fs-4"></i> <span class="sidebar-text">Dashboard</span></a>
        <a href="#"><i class="fas fa-exclamation-triangle fs-4"></i> <span class="sidebar-text">Work Issue</span></a>
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
        <?php if (!in_array($userdata['role'], ['admin', 'co-admin']))  { ?>
            <a href="#" id="notadmin"><i class="bi bi-lock-fill fs-4"></i> <span class="sidebar-text">Admin Panel</span></a>
           

        <?php } else { ?>
             <a href="admin-panel.php"><i class="bi bi-unlock-fill fs-4"></i> <span class="sidebar-text">Admin Panel</span></a>
        <?php } ?>

        <a href="#"><i class="fas fa-history fs-4"></i> <span class="sidebar-text">History</span></a>
        <a href="user-settings.php"><i class="bi bi-person-fill-gear fs-4"></i> <span class="sidebar-text">User Settings</span></a>

        <a href="#" id="logoutBtn" class="text-danger mt-auto">
            <i class="fas fa-sign-out-alt"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>
</div>