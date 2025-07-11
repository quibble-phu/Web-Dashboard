<?php include('../backend/track_session.php');
include '../backend/condb.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$sql = "SELECT mj.*, m.machine_name 
        FROM maintenance_job mj
        LEFT JOIN machine m ON mj.machine_id = m.machine_id
        ORDER BY mj.maintenance_id";
$stmt = $pdo->query($sql);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php include('../index/head.php'); ?>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">
    <title>Maintenance Job Table</title>
</head>

<body>


        <div class="container mt-4">
            <div class="card mt-4 ms-3 me-3">

                <div class="card-header bg-primary text-white">
                    <h3>📋 รายการแผนซ่อมจากตาราง maintenance_job</h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>🆔 Maintenance ID</th>
                                <th>รายละเอียด</th>
                                <th>เครื่องจักร</th>
                                <th>ช่วงเวลา (Period)</th>
                                <th>ประเภท</th>
                                <th>🔍 รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><?= htmlspecialchars($job['maintenance_id']) ?></td>
                                    <td><?= htmlspecialchars($job['description']) ?></td>
                                    <td><?= htmlspecialchars($job['machine_id']) ?> - <?= htmlspecialchars($job['machine_name']) ?></td>
                                    <td><?= htmlspecialchars($job['period']) ?></td>
                                    <td>
                                        <?= $job['inhouse_outsource'] === 'inhouse' ? '🟢 In-house' : '🔴 Outsource' ?>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?= $job['maintenance_id'] ?>" class="btn btn-sm btn-info">
                                            🔍 ดู
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="main.php" class="btn btn-secondary mt-3">🔙 กลับ</a>

        </div>

   
    <?php include('../index/script.php'); ?>
</body>

</html>