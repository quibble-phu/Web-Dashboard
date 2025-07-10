<?php include('../backend/track_session.php'); ?>
<?php
include 'condb.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;

if (!$start || !$end) {
    echo json_encode([]);
    exit;
}

$startDateFilter = new DateTime($start);
$endDateFilter = new DateTime($end);

// ดึงข้อมูล maintenance_job + รอบล่าสุดจาก work
$sql = "SELECT mj.maintenance_id, mj.description, mj.period, mj.inhouse_outsource, w.end_date 
        FROM maintenance_job mj
        LEFT JOIN (
            SELECT maintenance_id, MAX(end_date) AS end_date
            FROM work
            GROUP BY maintenance_id
        ) w ON mj.maintenance_id = w.maintenance_id";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$events = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $maintenanceId = $row['maintenance_id'];
    $description = $row['description'];
    $periodStr = $row['period']; // เช่น '1M', '7D'
    $lastEndDateStr = $row['end_date'];
    $source = $row['inhouse_outsource'];

    // แปลง period เป็น DateInterval
    if (preg_match('/^(\d+)([DMY])$/', $periodStr, $matches)) {
        $num = (int)$matches[1];
        $unit = $matches[2];

        switch ($unit) {
            case 'D':
                $period = new DateInterval("P{$num}D");
                break;
            case 'M':
                $period = new DateInterval("P{$num}M");
                break;
            case 'Y':
                $period = new DateInterval("P{$num}Y");
                break;
            default:
                $period = new DateInterval("P1M");
                break;
        }
    } else {
        $period = new DateInterval("P1M");
    }

    // เริ่มจาก end_date ล่าสุด หรือจากช่วง filter
    $nextStartDate = $lastEndDateStr ? new DateTime($lastEndDateStr) : clone $startDateFilter;
    if ($lastEndDateStr) $nextStartDate->modify('+1 day');

    // วนสร้างรอบใหม่จนเกิน endDateFilter (ล่วงหน้า 3 รอบพอ)
    $round = 0;
    while ($round < 3) {
        $nextEndDate = clone $nextStartDate;
        $nextEndDate->add($period);
        $nextEndDate->modify('-1 day');

        // ถ้าเลยช่วงที่เราจะแสดง ก็หยุด
        if ($nextStartDate > $endDateFilter) break;

        // แสดงเฉพาะรอบที่อยู่ในช่วง filter
        if ($nextEndDate >= $startDateFilter) {
            $color = $source === 'inhouse' ? '#10b981' : '#f97316'; 
            $events[] = [
                'id' => $maintenanceId,
                'title' => $description,
                //'start' => $nextStartDate->format('Y-m-d'),
                //'end' => $nextEndDate->format('Y-m-d'),
                'start' => $nextStartDate->format('Y-m-d'),
                'end' => $nextStartDate->format('Y-m-d'), 

                'allDay' => true,
                'color' => $color
            ];
        }

        // เตรียมรอบถัดไป
        $nextStartDate = clone $nextEndDate;
        $nextStartDate->modify('+1 day');
        $round++;
    }
}

header('Content-Type: application/json');
echo json_encode($events);
?>