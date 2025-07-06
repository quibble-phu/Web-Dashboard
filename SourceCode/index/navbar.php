<?php
include('../backend/track_session.php');

$userdata = null; // เริ่มต้นเป็น null
if (isset($_SESSION['user_id'])) {
    require_once('../backend/condb.php'); // ดึงไฟล์เชื่อมต่อฐานข้อมูล
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM employee WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $userdata = $stmt->fetch();
    } catch (PDOException $e) {
        // กรณีดึงข้อมูลไม่ได้
        $userdata = null;
    }
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
                <li><a href="../pages/index.php" class="nav-link px-2 text-white">Home</a></li>
                <li><a href="../pages/main.php" class="nav-link px-2 text-warning">Features</a></li>
                <li><a href="#" class="nav-link px-2 text-white">Dashboard</a></li>
                <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
                <li><a href="#" class="nav-link px-2 text-white">Contact</a></li>
            </ul>
            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search" id="search">
                <input type="search" class="form-control form-control-dark text-bg-light" placeholder="Search..." aria-label="Search">
            </form>

            <div class="text-end d-flex align-items-center gap-2">
                <?php if (isset($userdata)) : ?>
                    <?php
                    $profileImage = !empty($userdata['profile_image'])
                        ? '../uploads/' . $userdata['profile_image']
                        : '../Pic/png-transparent-default-avatar.png';
                    ?>
                    <a href="../pages/user-settings.php">
                        <img src="<?php echo $profileImage; ?>"
                            alt="avatar"
                            width="40"
                            height="40"
                            class="rounded-circle border border-warning"
                            style="object-fit: cover; display: block;">
                    </a>
                    <span class="navbar-brand mb-0 h1 me-4">Welcome | <?php echo htmlspecialchars($userdata['username']) ?></span>
                    <button class="btn btn-danger" onclick="logoutConfirm()">Logout</button>
                <?php else : ?>
                    <a href="../pages/login.php" class="btn btn-warning me-2">Login</a>
                <?php endif; ?>




            </div>
        </div>
    </div>
    <script>
        function logoutConfirm() {
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
        }
    </script>
</header>