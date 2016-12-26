<?php

$jpeg_data = file_get_contents('php://input');
$filename = md5(date("HisYd").rand(0,999999)).".jpg";
$complete_address = "../users/images/".$filename;
$result = file_put_contents($complete_address, $jpeg_data);
echo "users/images/".$filename;
?>