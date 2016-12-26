<?php

require "db/DBConnect.php";
include "model/User_interestModel.php";
include "controller/User_interestController.php";
include "supporter/User_interestSupporter.php";

$req = $_POST["req"];
if ($req == "get_user_interests") {
    $user_id = $_POST["user_id"];
    echo json_encode(getUserInterests($user_id));
} else {
    header("location:badpage.html");
}
?>
