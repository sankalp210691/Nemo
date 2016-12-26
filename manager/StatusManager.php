<?php

require "../db/DBConnect.php";
include "../model/StatusModel.php";
include "../controller/StatusController.php";

$req = $_POST["req"];
if ($req == "post_status") {
    $user_id = trim($_POST["user_id"]);
    $status_text = trim($_POST["status"]);

    if (strlen($user_id) == 0 || strlen($status_text) == 0) {
        echo "Invalid user id or status text";
    } else {
        $status = new Status();
        $statuscon = new StatusController();

        $status->setUser_id($user_id);
        $status->setStatus_text($status_text);
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $status->setDate($date);
        $status->setTime($time);
        $status_id = $statuscon->insert($status,null);
        echo "Status ID -> ".$status_id;
    }
} else {
    header("location:badpage.html");
}
?>
