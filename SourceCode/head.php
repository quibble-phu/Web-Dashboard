<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'condb.php';

if (isset($_SESSION['user_id'])) {
    $session_id = session_id();
    $user_id = $_SESSION['user_id'];
    $now = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
    INSERT INTO user_sessions(user_id, session_id, last_activity, created_at)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE last_activity = VALUES(last_activity)
    ");
    $stmt->execute([$user_id, $session_id, $now, $now]);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM SHOP</title>

<link rel="stylesheet" href="../plugins/datatables-new/datatables.min.css" />
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
<script src="assets/js/color-modes.js"></script>
<!-- Fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

<!-- Icon Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../bootstrap-icons-1.13.1/bootstrap-icons.css">




</head>
<body>
    
</body>
</html>


