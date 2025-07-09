<?php
require_once '../backend/condb.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM employee WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !in_array($user['role'], ['admin', 'co-admin'])) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

$stmt = $pdo->prepare("
    SELECT us.session_id, us.user_id, us.last_activity, us.created_at, us.ip_address, us.user_agent, u.username, u.role
    FROM user_sessions us
    JOIN employee u ON u.user_id = us.user_id
    ORDER BY us.last_activity DESC
");
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sessions as $session) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($session['username']) . "</td>";
    echo "<td>" . htmlspecialchars($session['role']) . "</td>";
    echo "<td>" . htmlspecialchars($session['session_id']) . "</td>";
    echo "<td>" . htmlspecialchars($session['ip_address']) . "</td>";
    echo "<td style='max-width: 200px; overflow: auto; font-size: 12px;'>" . htmlspecialchars($session['user_agent']) . "</td>";
    echo "<td>" . htmlspecialchars($session['created_at']) . "</td>";
    echo "<td>" . htmlspecialchars($session['last_activity']) . "</td>";
    echo "<td>";
    if ($session['session_id'] !== session_id()) {
        echo "<button class='btn btn-sm btn-danger' onclick=\"killSession('" . $session['session_id'] . "')\">Kill</button>";
    } else {
        echo "<span class='text-muted'>Your Session</span>";
    }
    echo "</td>";
    echo "</tr>";
}
?>
