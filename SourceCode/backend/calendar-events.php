<?php include('../backend/track_session.php'); ?>
<?php
require_once 'condb.php';


if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ใช้ข้อมูลช่วงวันที่ที่ FullCalendar ส่งมา
$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;

$sql = "SELECT maintenance_job.maintenance_id, maintenance_job.description, work.assign_date, work.start_date, work.end_date
        FROM maintenance_job
        LEFT JOIN work ON maintenance_job.maintenance_id = work.maintenance_id
        WHERE (work.start_date BETWEEN ? AND ?) 
           OR (work.assign_date BETWEEN ? AND ?) 
           OR (work.end_date BETWEEN ? AND ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$start, $end, $start, $end, $start, $end]);

$events = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // หาสถานะจากวันที่
    if ($row['start_date'] && $row['end_date']) {
        $status = 'done';
        $color = '#10b981'; // เขียว
    } elseif ($row['start_date']) {
        $status = 'in_progress';
        $color = '#3b82f6'; // น้ำเงิน
    } else {
        $status = 'pending';
        $color = '#facc15'; // เหลือง
    }

    $events[] = [
        'id' => $row['maintenance_id'],
        'title' => $row['description'],
        'start' => $row['start_date'] ?: $row['assign_date'],
        'end' => $row['end_date'] ? date('Y-m-d', strtotime($row['end_date'] . ' +1 day')) : null,
        'allDay' => true,
        'color' => $color,
        'status' => $status,
    ];
}


header('Content-Type: application/json');
echo json_encode($events);
