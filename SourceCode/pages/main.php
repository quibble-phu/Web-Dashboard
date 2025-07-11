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
            visibility: hidden;
            pointer-events: none;
            position: absolute;
            width: 100%;
            left: 0;
            top: 0;
        }

        .view-container.active {
            opacity: 1;
            transform: scale(1);
            visibility: visible;
            pointer-events: auto;
            position: relative;
        }

        #content {
            position: relative;
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

        /* Responsive calendar container */
        .cal {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 10px;
            min-height: 400px;
            height: calc(100vh - 250px);
            /* ปรับให้พอดีกับจอ ลบ header/navbar ประมาณนี้ */
            overflow-y: auto;
        }


        #calendar-legend {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin: 10px auto 20px auto;
            padding: 0 10px;
            max-width: 1200px;
            box-sizing: border-box;
        }

        .legend-left span {
            display: inline-block;
            margin-bottom: 5px;
        }

        .legend-right {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
        }

        /* สำหรับมือถือ */
        @media (max-width: 768px) {
            .cal {
                height: calc(100vh - 300px);
            }

            #calendar-legend {
                flex-direction: column;
                align-items: flex-start;
            }

            .legend-right {
                width: 100%;
                justify-content: flex-start;
                margin-top: 10px;
            }

            .legend-right select {
                width: 100%;
            }
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

        <div class="text-center my-3">
            <button class="btn btn-secondary" onclick="switchView('table')">📋 Table</button>
            <button class="btn btn-secondary" onclick="switchView('calendar-main')">🗓️ All Calendar</button>
            <button class="btn btn-secondary" onclick="switchView('calendar-work')">👷 My Work Calendar</button>
        </div>

        <!-- ตารางทั้งหมด (งาน in-house และ outsource) -->
        <div id="tableView" class="view-container">
            <?php include('../backend/maintenance_job_table.php'); ?>
        </div>

        <!-- ปฏิทินหลัก: แสดง maintenance job ทั้งหมด -->
        <!-- ใน <body> -->
        <div id="calendarMainView" class="view-container active">
            <div id="calendar-legend" class="mb-3">
                <div class="legend-left">
                    <span style="background-color: #10b981; padding: 5px 10px; border-radius: 4px;">🟢 In-house</span>
                    <span style="background-color: #f87171; padding: 5px 10px; border-radius: 4px;">🔴 Outsource</span>
                </div>
            </div>
            <div id="calendar-main" class="cal"></div>
        </div>

        <!-- end main -->

        <<!-- ปฏิทินงานที่ assign แล้ว -->
            <div id="calendarWorkView" class="view-container">
                <!-- Legend แสดงสถานะ -->
                <div id="calendar-legend">
                    <div class="legend-left">
                        <span style="background-color: #facc15; padding: 5px 10px; border-radius: 4px; margin-right: 10px;">🟡 Pending</span>
                        <span style="background-color: #3b82f6; padding: 5px 10px; border-radius: 4px; margin-right: 10px;">🔵 In-progess</span>
                        <span style="background-color: #10b981; padding: 5px 10px; border-radius: 4px;">✅ Sucess</span>
                    </div>
                    <div class="legend-right">
                        <label for="statusFilter" class="me-2">กรองตามสถานะ: </label>
                        <select id="statusFilterWork" class="form-select d-inline-block w-auto">
                            <option value="">ทั้งหมด</option>
                            <option value="pending">🟡 รอดำเนินการ</option>
                            <option value="in_progress">🔵 กำลังดำเนินการ</option>
                            <option value="done">✅ เสร็จแล้ว</option>
                        </select>
                    </div>
                </div>
                <!-- FullCalendar จะแสดงตรงนี้ -->
                <div id="calendar-work" class="cal"></div>
            </div>
            <!-- end work -->



            <?php include('../index/footer.php'); ?>
    </div>

    <!-- Scripts -->
    <script src="../js/main.js"></script>
    <?php include('../index/script.php'); ?>
    <script src="../js/popup.js"></script>

    <script>
        let calendarMain, calendarWork;

        document.addEventListener('DOMContentLoaded', function() {

            function handleUnauthorized(response) {
                if (response.status === 403) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'หมดเวลาใช้งาน',
                        text: 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง',
                        confirmButtonText: 'เข้าสู่ระบบ',
                    }).then(() => {
                        window.location.href = '../pages/login.php';
                    });
                    return true;
                }
                return false;
            }

            async function fetchCalendarEvents(url, params) {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(params)
                    });

                    if (handleUnauthorized(response)) return [];

                    if (!response.ok) {
                        throw new Error('โหลดข้อมูลไม่สำเร็จ');
                    }

                    return await response.json();
                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถโหลดข้อมูลได้ ลองอีกครั้งภายหลัง' || error.message  ,
                        confirmButtonText: 'Back',
                    }).then(() => {
                        window.location.href = '../pages/dashboard.php';
                    });
                    return [];
                }
            }

            // calendar-main
            const calendarMainEl = document.getElementById('calendar-main');
            calendarMain = new FullCalendar.Calendar(calendarMainEl, {
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
                events: async function(fetchInfo, successCallback, failureCallback) {
                    const events = await fetchCalendarEvents('../backend/calendar-events-main.php', {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr
                       
                    });
                    successCallback(events);
                },
                eventClick: handleEventClick
            });

            // calendar-work
            const calendarWorkEl = document.getElementById('calendar-work');
            calendarWork = new FullCalendar.Calendar(calendarWorkEl, {
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
                events: async function(fetchInfo, successCallback, failureCallback) {
                    const status = document.getElementById('statusFilterWork').value;
                    const events = await fetchCalendarEvents('../backend/calendar-events.php', {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                    
                        status: status
                    });
                    successCallback(events);
                },
                eventClick: handleEventClick
            });

            // render calendar ถ้าอยู่ในหน้า
            if (document.getElementById('calendarMainView').classList.contains('active')) {
                calendarMain.render();
            }
            if (document.getElementById('calendarWorkView').classList.contains('active')) {
                calendarWork.render();
            }

            // กรองสถานะ
            const statusFilterWork = document.getElementById('statusFilterWork');
            if (statusFilterWork) {
                statusFilterWork.addEventListener('change', function() {
                    calendarWork.refetchEvents();
                });
            }
        });

        function switchView(view) {
            const views = {
                table: document.getElementById('tableView'),
                'calendar-main': document.getElementById('calendarMainView'),
                'calendar-work': document.getElementById('calendarWorkView')
            };

            const buttons = {
                table: document.querySelector("button[onclick=\"switchView('table')\"]"),
                'calendar-main': document.querySelector("button[onclick=\"switchView('calendar-main')\"]"),
                'calendar-work': document.querySelector("button[onclick=\"switchView('calendar-work')\"]")
            };

            Object.values(views).forEach(viewEl => viewEl.classList.remove('active'));
            Object.values(buttons).forEach(btn => btn.classList.remove('active-btn'));

            setTimeout(() => {
                views[view].classList.add('active');
                buttons[view].classList.add('active-btn');

                if (view === 'calendar-main') calendarMain.render();
                else if (view === 'calendar-work') calendarWork.render();
            }, 100);
        }

        function handleEventClick(info) {
            Swal.fire({
                title: '📌 รายละเอียดแผนซ่อม',
                html: `
            <b>🆔 Maintenance ID:</b> ${info.event.id}<br>
            <b>📄 รายละเอียด:</b> ${info.event.title}<br>
            <b>📍 Status:</b> ${info.event.extendedProps.result || '❓ Unknow'}<br>
           <br><a href="detail.php?id=${info.event.id}&start=${info.event.startStr}" class="btn btn-primary mt-2">🔍 ดูรายละเอียด</a>

        `,
        // <b>🗓️ วันที่:</b> ${info.event.startStr}<br>
        //${info.event.endStr ? `<b>📅 สิ้นสุด:</b> ${info.event.endStr}` : ''}
                icon: 'info',
                confirmButtonText: 'ปิด'
            });
        }
    </script>

</body>

</html>