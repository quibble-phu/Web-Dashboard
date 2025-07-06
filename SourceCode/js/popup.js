
document.getElementById('notadmin').addEventListener('click', function (e) {
    e.preventDefault();
  Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: 'เฉพาะผู้ดูแลระบบ (admin) เท่านั้น',
                    confirmButtonText: 'กลับหน้าหลัก'
                }).then(() => {
                    window.location.href = 'main.php';
                });
      

});