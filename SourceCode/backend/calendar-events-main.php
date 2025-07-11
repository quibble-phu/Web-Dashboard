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

// ดึงข้อมูล maintenance_job + รอบล่าสุดจาก work (พร้อม assign_date ล่าสุด)
$sql = "SELECT mj.maintenance_id, mj.description, mj.period, mj.inhouse_outsource, 
               w.end_date, w2.assign_date
        FROM maintenance_job mj
        LEFT JOIN (
            SELECT maintenance_id, MAX(end_date) AS end_date
            FROM work
            GROUP BY maintenance_id
        ) w ON mj.maintenance_id = w.maintenance_id
        LEFT JOIN (
            SELECT maintenance_id, MIN(assign_date) AS assign_date
            FROM work
            GROUP BY maintenance_id
        ) w2 ON mj.maintenance_id = w2.maintenance_id";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$events = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $maintenanceId = $row['maintenance_id'];
    $description = $row['description'];
    $periodStr = $row['period'];
    $lastEndDateStr = $row['end_date'];
    $assignDateStr = $row['assign_date'];
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
        }
    } else {
        $period = new DateInterval("P1M");
    }

    // ---------- เงื่อนไข 1: มี end_date ⇒ วนรอบถัดไป ----------
    if ($lastEndDateStr) {
        $nextStartDate = new DateTime($lastEndDateStr);
        $nextStartDate->modify('+1 day');

        $round = 0;
        while ($round < 3) {
            $nextEndDate = clone $nextStartDate;
            $nextEndDate->add($period);
            $nextEndDate->modify('-1 day');

            if ($nextStartDate > $endDateFilter) break;

            if ($nextEndDate >= $startDateFilter) {
                $color = $source === 'inhouse' ? '#10b981' : '#f97316';
                $result = $source === 'inhouse' ? '🟢 inhouse' : '🔴 outsource';
                $events[] = [
                    'id' => $maintenanceId,
                    'title' => $description,
                    'result' => $result,
                    'start' => $nextStartDate->format('Y-m-d'),
                    'end' => $nextStartDate->format('Y-m-d'),
                    'event_start' => $nextStartDate->format('Y-m-d'),

                    'allDay' => true,
                    'color' => $color
                ];
            }


            $nextStartDate = clone $nextEndDate;
            $nextStartDate->modify('+1 day');
            $round++;
        }
    }

    // ---------- เงื่อนไข 2: ไม่มี end_date แต่มี assign_date ----------
    elseif ($assignDateStr) {
        $assignDate = new DateTime($assignDateStr);
        if ($assignDate >= $startDateFilter && $assignDate <= $endDateFilter) {
            $color = $source === 'inhouse' ? '#10b981' : '#f97316';
            $result = $source === 'inhouse' ? '🟢 inhouse' : '🔴 outsource';
            $events[] = [
                'id' => $maintenanceId,
                'title' => $description . ' (เริ่มรอบแรก)',
                'result' => $result,
                'start' => $assignDate->format('Y-m-d'),
                'end' => $assignDate->format('Y-m-d'),
               
                'allDay' => true,
                'color' => $color
            ];
        }
    }
}
header('Content-Type: application/json');
echo json_encode($events);
