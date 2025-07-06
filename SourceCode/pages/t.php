<?php



$new_plain_password = '746600'; // รหัสผ่านใหม่ที่ต้องการ

// hash รหัสผ่านใหม่ด้วย password_hash
$new_hashed_password = password_hash($new_plain_password, PASSWORD_DEFAULT);

echo $new_hashed_password

?>
