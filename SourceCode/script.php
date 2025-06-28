<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- adminlte -->
<script src="/WEB_PM/AdminLTE-4.0.0-rc1/plugins/jquery/jquery.min.js"></script>
<script src="/WEB_PM/AdminLTE-4.0.0-rc1/dist/js/adminlte.min.js"></script>


<!-- Bootstrap 5 -->
<script src="assets/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<!-- DataTables -->
<script src="../plugins/datatables-new/datatables.min.js"></script>






<!-- DataTables Init -->
<script>
  $('#myTable').DataTable({
  responsive: true,
  processing: true,
  lengthChange: true,
  autoWidth: false,
  pageLength: 10,
  lengthMenu: [5, 10, 25, 50, 100],
  buttons: [
    {
      extend: 'copy',
      text: '<i class="fas fa-copy"></i> Copy',
      className: 'btn btn-dark'
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
      text: '<i class="fas fa-columns"></i> Select Column',
      className: 'btn btn-warning'
    }
  ],
  language: {
    search: "ค้นหา:",
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
}).buttons().container().appendTo('#myTable_wrapper .col-md-6:eq(0)');

</script>
