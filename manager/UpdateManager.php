<?php
	require "../db/DBConnect.php";
	include "../supporter/UpdateSupporter.php";

	$req = $_GET["req"];
	if ($req == "get_updates") {
	    echo json_encode(getUpdates($_GET["user_id"],$_GET["type"]));
	} else {
	    header("location:../badpage.html");
	}
?>
