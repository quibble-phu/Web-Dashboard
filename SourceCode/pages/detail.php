<?php include('../backend/track_session.php'); ?>
<?php
require_once '../backend/condb.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../pages/login.php");
}

$maintenance_id = $_GET['id'] ?? null;
$event_start = $_GET['start'] ?? null; // ดึงวันเริ่มงานมาด้วยจาก calendar

if (!$maintenance_id) {
    echo "ไม่พบรหัสงานซ่อม";
    exit;
}

// ตรวจสอบว่า start นี้มีอยู่ในตาราง work จริงหรือไม่
$stmtCheck = $pdo->prepare("SELECT * FROM work WHERE maintenance_id = ? AND assign_date = ?");
$stmtCheck->execute([$maintenance_id, $event_start]);
$workRow = $stmtCheck->fetch();

// ถ้าเจอข้อมูลใน work แสดงตามจริง
if ($workRow) {
    $stmt = $pdo->prepare("
        SELECT 
            mj.maintenance_id,
            mj.description,
            mj.machine_id,
            m.machine_name,
            w.assign_date,
            w.start_date,
            w.end_date,
            CONCAT(e.first_name, ' ', e.last_name) AS technician
        FROM maintenance_job mj
        LEFT JOIN work w ON mj.maintenance_id = w.maintenance_id AND w.assign_date = ?
        LEFT JOIN employee e ON w.user_id = e.user_id
        LEFT JOIN machine m ON mj.machine_id = m.machine_id
        WHERE mj.maintenance_id = ?
    ");
    $stmt->execute([$event_start, $maintenance_id]);
    $data = $stmt->fetch();
} else {
    // งานจาก period ที่ยังไม่มีใน work
    $stmt = $pdo->prepare("
        SELECT 
            mj.maintenance_id,
            mj.description,
            mj.machine_id,
            m.machine_name
        FROM maintenance_job mj
        LEFT JOIN machine m ON mj.machine_id = m.machine_id
        WHERE mj.maintenance_id = ?
    ");
    $stmt->execute([$maintenance_id]);
    $data = $stmt->fetch();

    // set ค่าอื่น ๆ เป็น null หรือ false เพื่อบอกว่าเป็น future
    $data['assign_date'] = null;
    $data['start_date'] = null;
    $data['end_date'] = null;
    $data['technician'] = null;
}


// Query รายการอะไหล่
$stmt_parts = $pdo->prepare("
    SELECT p.part_id, p.name, p.model, p.brand, p.cost , p.supplier, mp.amount
    FROM mn_part mp
    JOIN part p ON mp.part_id = p.part_id
    WHERE mp.maintenance_id = ?
");
$stmt_parts->execute([$maintenance_id]);
$parts = $stmt_parts->fetchAll();
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <?php include('../index/head.php'); ?>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">
    <title>Detail</title>
</head>

<body>
    <?php include('../index/menu.php'); ?>
    <div id="content">
        <?php include('../index/navbar-main.php'); ?>




        <div class="container mt-4">
            <div class="card mt-4 ms-3 me-3">
                <div class="card-header bg-success text-white ">
                    <h2>📋 Detail</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $isFutureGenerated = !$data['assign_date'] && !$data['start_date'] && !$data['end_date'];
                        ?>


                        <?php if (!$isFutureGenerated): ?>
                            <div class="col-md-6">
                                <p><strong>🆔 รหัสงานซ่อม:</strong> <?= htmlspecialchars($data['maintenance_id']) ?></p>
                                <p><strong>📄 รายละเอียด:</strong> <?= htmlspecialchars($data['description']) ?></p>
                                <p><strong>🏭 รหัสเครื่องจักร:</strong> <?= htmlspecialchars($data['machine_id']) ?> - <?= htmlspecialchars($data['machine_name']) ?></p>
                                <p><strong>👷‍♂️ ช่างที่รับผิดชอบ:</strong> <?= $data['technician'] ?: '-' ?></p>
                            </div>
                            <div class="col-md-6">

                                <p><strong>🗓️ วันที่มอบหมาย:</strong> <?= $data['assign_date'] ?: '-' ?></p>
                                <p><strong>🕒 วันที่เริ่ม:</strong> <?= $data['start_date'] ?: '-' ?></p>
                                <p><strong>✅ วันที่เสร็จ:</strong> <?= $data['end_date'] ?: '-' ?></p>
                                <p><strong>📌 สถานะ:</strong>
                                    <?php
                                    if ($data['end_date']) {
                                        echo "✅ เสร็จแล้ว";
                                    } elseif ($data['start_date']) {
                                        echo "🔵 กำลังดำเนินการ";
                                    } elseif ($data['assign_date']) {
                                        echo "🟡 รอดำเนินการ";
                                    } else {
                                        echo "❓ ไม่ระบุ";
                                    }
                                    ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <p><strong>🆔 รหัสงานซ่อม:</strong> <?= htmlspecialchars($data['maintenance_id']) ?></p>
                                <p><strong>📄 รายละเอียด:</strong> <?= htmlspecialchars($data['description']) ?></p>
                                <p><strong>🏭 รหัสเครื่องจักร:</strong> <?= htmlspecialchars($data['machine_id']) ?> - <?= htmlspecialchars($data['machine_name']) ?></p>
                                <p><strong>👷‍♂️ ช่างที่รับผิดชอบ:</strong> ข้อมูลจะปรากฏเมื่อมีการมอบหมาย</p>
                            </div>
                            <div class="col-md-6">
                                    <p><strong>🗓️ วันที่มอบหมาย:</strong> -</p>
                                    <p><strong>🕒 วันที่เริ่ม:</strong> -</p>
                                    <p><strong>✅ วันที่เสร็จ:</strong> -</p>
                                    <p><strong>📌 สถานะ:</strong> ยังไม่เริ่ม </p>
                            </div>
                        <?php endif; ?>

                    </div>
                    <hr>
                    <h3><strong> <i class="bi bi-box-seam-fill fs-4"></i> Part in used :</strong></h3>
                    <?php if ($parts): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="myTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>รหัสอะไหล่</th>
                                        <th>ชื่ออะไหล่</th>
                                        <th>รุ่น</th>
                                        <th>ยี่ห้อ</th>
                                        <th>Supplier</th>
                                        <th>ราคาต่อชิ้น</th>
                                        <th>จำนวนที่ใช้</th>
                                        <th>ราคารวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($parts as $part): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($part['part_id']) ?></td>
                                            <td><?= htmlspecialchars($part['name']) ?></td>
                                            <td><?= htmlspecialchars($part['model']) ?></td>
                                            <td><?= htmlspecialchars($part['brand']) ?></td>
                                            <td><?= htmlspecialchars($part['supplier']) ?></td>
                                            <td><?= htmlspecialchars($part['cost']) ?></td>
                                            <td><?= htmlspecialchars($part['amount']) ?></td>
                                            <td><?= number_format($part['cost'] * $part['amount']) ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>ไม่มีการใช้อะไหล่</p>
                    <?php endif; ?>


                </div>
            </div>

            <a href="main.php" class="btn btn-secondary mt-3">🔙 กลับ</a>
        </div>

        <?php include('../index/footer.php'); ?>
    </div>

    <script src="../js/main.js"></script>
    <?php include('../index/script.php'); ?>
    <script src="../js/popup.js"></script>
</body>

</html>