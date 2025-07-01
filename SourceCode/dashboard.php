<?php
session_start();
require "condb.php";

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

if (!isset($_SESSION['user_id'])) {
    header("location: login-signup.php?action=login");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('head.php'); ?>
    <link href="style.css" rel="stylesheet">
    <link href="colormode.css" rel="stylesheet">
    <style>
        #myTable_wrapper .dt-buttons .btn {
            margin-right: 0em;
        }

        #myTable {
            font-size: 0.9rem;
        }

        #myTable thead th {
            text-align: center !important;
        }

        #myTable tbody td {
            text-align: left;
        }

        #myTable_filter input {
            border: 2px solid #007bff;
            /* ขอบสีฟ้า */
            border-radius: 5px;
            /* มุมโค้ง */
            padding: 5px 10px;
            /* ระยะห่างในช่อง */
            width: 250px;
            /* กว้าง */
            transition: box-shadow 0.3s ease;
        }

        #myTable_filter input:focus {
            outline: none;
            box-shadow: 0 0 8px #007bff;
            /* เวลาคลิกจะมีเงา */
        }

        #myTable_filter {
            position: relative;
        }

        #myTable_filter input {
            padding-left: 30px;
        }

        #myTable_filter::before {
            content: "\f002";
            /* Unicode ของไอคอนแว่นขยายใน FontAwesome */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            pointer-events: none;
        }

        .lengthMenu {
            margin-right: 10px;

        }
    </style>

</head>

<body>
    <?php include('menu.php'); ?>

    <div id="content">
        <?php include('navbar-main.php'); ?>

        <div class="container-fluid p-4">
            <!-- ฟอร์มกรองวันที่ -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="fw-bold lh-1 text-body-emphasis" style="color: white !important;"><i class="bi bi-table fs-3"></i> Select a date to show</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">
                                <h4><strong>From :</strong></h4>
                            </label>
                            <input type="date" id="date_from" name="date_from" class="form-control" required value="<?= $_GET['date_from'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">
                                <h4><strong>To :</strong></h4>
                            </label>
                            <input type="date" id="date_to" name="date_to" class="form-control" required value="<?= $_GET['date_to'] ?? '' ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <h6> <i class="bi bi-database-fill-up "></i> <strong>Query</strong></h6>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- จบฟอร์ม -->

            <div class="col-12">
                <?php
                $i = 1;

                if (isset($_GET['date_from']) && isset($_GET['date_to'])) {
                    $from = $_GET['date_from'];
                    $to = $_GET['date_to'];

                    try {
                        $stmt = $pdo->prepare("SELECT * FROM test WHERE DATE(datetime_record) BETWEEN :from AND :to");
                        $stmt->bindParam(':from', $from);
                        $stmt->bindParam(':to', $to);
                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo "<h4 class='mb-3'><strong>ข้อมูลระหว่าง $from ถึง $to</strong></h4>";

                        if ($results) {
                            echo "<p class='text-success'>พบข้อมูลทั้งหมด: <strong>" . count($results) . "</strong> รายการ</p>";

                            echo "<div class='table-responsive'>";
                            echo "<table id='myTable' class='table table-striped table-bordered align-middle'>";
                            echo "<thead class='table-dark'>
                                    <tr>
                                        <th>Row</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Detail</th>
                                        <th>Date</th>
                                    </tr>
                                  </thead><tbody>";

                            foreach ($results as $row) {
                                echo "<tr>
                                        <td>" . $i++ . "</td>
                                        <td>" . htmlspecialchars($row["Name"]) . "</td>
                                        <td>" . htmlspecialchars($row["code"]) . "</td>
                                        <td>" . htmlspecialchars($row["detail"]) . "</td>
                                        <td>" . htmlspecialchars($row["datetime_record"]) . "</td>
                                      </tr>";
                            }

                            echo "</tbody></table>";
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-warning mt-3'>ไม่พบข้อมูลในช่วงวันที่ที่เลือก</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
                    }
                }
                ?>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>

    <script src="main.js"></script>
    <script src="popup.js"></script>
    <?php include('script.php'); ?>
</body>

</html>