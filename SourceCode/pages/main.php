<?php include('../backend/track_session.php'); ?>
<?php
if (!isset($_SESSION['user_id'])) {
    header("location: ../pages/login.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('../index/head.php'); ?>
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/colormode.css" rel="stylesheet">



    <style>
        .view-container {
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            transform: scale(0.97);
            display: none;
        }

        .view-container.active {
            display: block;
            opacity: 1;
            transform: scale(1);
        }

        button {
            margin: 10px 5px;
            padding: 8px 16px;
            font-size: 1rem;
            cursor: pointer;
        }

        .active-btn {
            background-color: rgb(255, 166, 0);
            color: white;
            border-radius: 4px;
            border-color: rgb(255, 166, 0);
        }

        #calendar {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 10px;
            height: 700px;
        }

        .fc-toolbar-title {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .fc-button {
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
        }

        .fc-button:hover {
            background-color: #0056b3;
        }

        #calendar-legend {
            margin-left: 230px;
            
        }
    </style>

</head>

<body>
    <!-- Sidebar -->
    <?php include('../index/menu.php'); ?>
    <!-- Sidebar end -->

    <!-- Main Content -->
    <div id="content">
        <!-- Navbar -->
        <?php include('../index/navbar-main.php'); ?>
        <!-- Navbar end -->
        <h2 class="text-center mt-4">🛠️ Maintenance Plan</h2>
        <button class="btn btn-secondary" onclick="switchView('table')">📋 Table</button>
        <button class="btn btn-secondary" onclick="switchView('calendar')">📅 Calendar</button>

        <div id="tableView" class="view-container active">
            <?php include('plan-table.php'); ?>
        </div>

        <div id="calendarView" class="view-container">
            <!-- Legend แสดงสถานะ -->
            <div id="calendar-legend" style="margin-bottom: 10px;">
                <span style="background-color: #facc15; padding: 5px 10px; border-radius: 4px; margin-right: 10px;">🟡 รอดำเนินการ</span>
                <span style="background-color: #3b82f6; padding: 5px 10px; border-radius: 4px; margin-right: 10px;">🔵 กำลังดำเนินการ</span>
                <span style="background-color: #10b981; padding: 5px 10px; border-radius: 4px;">✅ เสร็จแล้ว</span>
            </div>

            <!-- FullCalendar จะแสดงตรงนี้ -->
            <div id="calendar"></div>
        </div>




    <?php include('../index/footer.php'); ?>
    </div>

    <!-- Scripts -->
    <script src="../js/main.js"></script>
    <?php include('../index/script.php'); ?>
    <script src="../js/popup.js"></script>

    <script>
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'th',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: 'วันนี้',
                    month: 'เดือน',
                    week: 'สัปดาห์',
                    day: 'วัน',
                    list: 'รายการ'
                },
                events: {
                    url: '../backend/calendar-events.php',
                    method: 'POST',
                    failure: function() {
                        Swal.fire('❌ ล้มเหลว', 'ไม่สามารถโหลดข้อมูลจากเซิร์ฟเวอร์ได้', 'error');
                    }
                },
                eventClick: function(info) {
                    Swal.fire({
                        title: '📌 รายละเอียดแผนซ่อม',
                        html: `
                            <b>🆔 Maintenance ID:</b> ${info.event.id}<br>
                            <b>📄 รายละเอียด:</b> ${info.event.title}<br>
                            <b>🗓️ วันที่เริ่ม:</b> ${info.event.startStr}<br>
                            ${info.event.endStr ? `<b>🗓️ วันที่สิ้นสุด:</b> ${info.event.endStr}` : ''}
                            <a href="detail.php?id=${info.event.id}" class="btn btn-primary mt-2">🔍 ดูรายละเอียด</a>
                        `,
                        icon: 'info',
                        confirmButtonText: 'ปิด'

                    });
                }
            });

            // ถ้า calendarView เป็น active ตอนโหลด
            if (document.getElementById('calendarView').classList.contains('active')) {
                calendar.render();
            }
        });

        function switchView(view) {
            const table = document.getElementById('tableView');
            const calendarView = document.getElementById('calendarView');
            const btnTable = document.querySelector("button[onclick=\"switchView('table')\"]");
            const btnCalendar = document.querySelector("button[onclick=\"switchView('calendar')\"]");

            table.classList.remove('active');
            calendarView.classList.remove('active');
            btnTable.classList.remove('active-btn');
            btnCalendar.classList.remove('active-btn');

            setTimeout(() => {
                if (view === 'table') {
                    table.classList.add('active');
                    btnTable.classList.add('active-btn');
                } else {
                    calendarView.classList.add('active');
                    btnCalendar.classList.add('active-btn');
                    if (calendar) calendar.render(); // render ใหม่ตอนแสดง
                }
            }, 100);
        }
    </script>
</body>

</html>