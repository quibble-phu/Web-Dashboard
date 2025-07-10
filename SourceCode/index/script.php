<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- adminlte -->



<!-- Bootstrap 5 -->
<script src="../assets/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<!-- DataTables -->
<script src="../../plugins/datatables-new/datatables.min.js"></script>

<script src="../../plugins/sweetalert2.all.min.js"></script>
<script src="../../plugins/fullcalendar-6.1.18/package/index.global.min.js"></script>








<!-- DataTables Init -->
<script>
  $(document).ready(function() {
    $('#myTable').DataTable({
      responsive: true,
      processing: true,
      lengthChange: true,
      autoWidth: false,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],

      dom: '<"row mb-2"<"col-md-12 text-start"B>>' + // ปุ่ม export ซ้ายบนสุด
        '<"row mb-2"<"col-md-6"l><"col-md-6 text-end"f>>' + // length ซ้าย / search ขวา
        'rt' + // ตาราง
        '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>', // จำนวนแสดงผล ซ้าย / เปลี่ยนหน้า ขวา

      buttons: [{
          extend: 'copy',
          text: '<i class="fas fa-copy"></i> Copy',
          className: 'btn btn-seccondary'
        },
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success'
        },
        {
          extend: 'pdf',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          className: 'btn btn-danger'
        },
        {
          extend: 'print',
          text: '<i class="fas fa-print"></i> Print',
          className: 'btn btn-info'
        },
        {
          extend: 'colvis',
          text: '<i class="fas fa-columns"></i> Sort',
          className: 'btn btn-warning'
        }
      ],
      language: {
        search: "ค้นหา:",
        searchPlaceholder: "🔍 Search...",
        lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
        info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
        paginate: {
          first: "หน้าแรก",
          last: "หน้าสุดท้าย",
          next: "ถัดไป",
          previous: "ย้อนกลับ"
        },
        zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
        infoEmpty: "ไม่มีข้อมูลแสดง",
        infoFiltered: "(กรองจาก _MAX_ รายการทั้งหมด)"
      }
    });
  });
</script>