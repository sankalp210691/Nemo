<?php

require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";

session_start();
$id = $_SESSION["id"];

$user = new User();
$usercon = new UserController();
$user = $usercon->getByPrimaryKey($id, array("online"), null,null);
if ($user != null) {
    $user->setOnline(0);
    $usercon->update($user,null);
}
session_destroy();
header("location: index.php");
?>
