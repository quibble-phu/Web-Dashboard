<?php include('../backend/track_session.php'); ?>

<!-- Sidebar -->
<div id="sidebar" class="bg-dark text-white position-fixed h-100">
    <div class="sidebar-header">
        <i class="bi bi-tools fs-2"></i>
        <span class="sidebar-text" style="color: #ffc100;">PM UNIT</span>
    </div>

    <div class="sidebar-search px-3 py-2">
        <input type="text" class="form-control form-control-sm" id="sidebarSearch" placeholder="🔍 Search..." />
    </div>

    <div class="sidebar-nav flex-grow-1 d-flex flex-column">
        <a href="../pages/main.php"><i class="bi bi-diagram-3 fs-4"></i> <span class="sidebar-text">Welcome</span></a>
        <a href="../pages/dashboard.php"><i class="bi bi-graph-up-arrow fs-4"></i> <span class="sidebar-text">Dashboard</span></a>
        <a href="#"><i class="fas fa-exclamation-triangle fs-4"></i> <span class="sidebar-text">Work Issue</span></a>

        <!-- Admin Panel -->
        <?php
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $userdata = $stmt->fetch();
            } catch (PDOException $e) {
                echo "DB Error: " . $e->getMessage();
            }
        }

        ?>

        <?php if (!in_array($userdata['role'], ['admin', 'co-admin'])) { ?>
            <a href="#" id="notadmin"><i class="bi bi-lock-fill fs-4"></i> <span class="sidebar-text">Admin Panel</span></a>
        <?php } else { ?>
            <div class="admin-panel-group">
                <a class="d-flex justify-content-between align-items-center px-3 py-2 text-white text-decoration-none"
                    data-bs-toggle="collapse" href="#adminMenu" role="button" aria-expanded="false" aria-controls="adminMenu">
                    <span>
                        <i class="bi bi-unlock-fill fs-4 ms-1 "></i>
                        <span class="sidebar-text ms-2">Admin Panel</span>
                    </span>
                    <i class="bi bi-caret-down-fill transition-arrow"></i>
                </a>
                <div class="collapse ms-1" id="adminMenu">
                    <a href="../pages/admin-panel.php" class="d-block text-white py-1">
                        <i class="bi bi-laptop fs-6 ms-3"></i><span class="sidebar-text ms-2">Admin Dashboard</span>
                    </a>
                    <a href="../pages/admin-user-log.php" class="d-block text-white py-1">
                        <i class="bi bi-people-fill fs-6 ms-3"></i><span class="sidebar-text ms-2">User Logs</span>
                    </a>
                </div>
            </div>
        <?php } ?>

        <a href="#"><i class="fas fa-history fs-4"></i> <span class="sidebar-text">History</span></a>
        <a href="../pages/user-settings.php"><i class="bi bi-person-fill-gear fs-4"></i> <span class="sidebar-text">User Settings</span></a>

        <a href="#" id="logoutBtn" class="text-danger mt-auto">
            <i class="fas fa-sign-out-alt"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You will be logged out.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, logout!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //  ดีเลย์ 0.5 วินาที 
                        Swal.fire({
                            title: 'Logging out...',
                            timer: 500,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        setTimeout(() => {
                            window.location.href = '../backend/logout.php';
                        }, 500);
                    }
                });
            });
        }
    });
</script>


<!-- JavaScript: Dropdown arrow rotation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');

        // Collapse watch
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(toggle => {
            const caret = toggle.querySelector('.bi-caret-down-fill');
            const targetId = toggle.getAttribute('href');
            const collapseElement = document.querySelector(targetId);

            collapseElement.addEventListener('shown.bs.collapse', () => {
                caret.classList.add('rotate-180');
            });

            collapseElement.addEventListener('hidden.bs.collapse', () => {
                caret.classList.remove('rotate-180');
            });

            if (collapseElement.classList.contains('show')) {
                caret.classList.add('rotate-180');
            }
        });

        // Close all collapses when sidebar is collapsed
        const observer = new MutationObserver(() => {
            if (sidebar.classList.contains('collapsed')) {
                document.querySelectorAll('#sidebar .collapse.show').forEach(drop => {
                    const bsCollapse = bootstrap.Collapse.getInstance(drop);
                    if (bsCollapse) bsCollapse.hide();
                });
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    });
</script>