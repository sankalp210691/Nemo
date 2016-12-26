<?php

require "db/DBConnect.php";
include "model/Email_tokenModel.php";
include "controller/Email_tokenController.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/User_stageModel.php";
include "controller/User_stageController.php";
include "model/StageModel.php";
include "controller/StageController.php";

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    $email_token = new Email_token();
    $email_tokencon = new Email_tokenController();

    $email_token->setToken($token);
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $email_tokens = $email_tokencon->findByAll($email_token, array("*"), null, $persistent_connection);
    if (sizeof($email_tokens) == 0) {
        $db_connection->mysqli_connect_close();
        echo "<h1>Invalid token</h1>";
    } else {
        $usercon = new UserController();

        $user = $usercon->getByPrimaryKey($email_tokens[0]->getUser_id(), array("id", "signup_stage"), null, $persistent_connection);
        $user->setSignup_stage(1);
        $usercon->update($user, $persistent_connection);

        $email_tokencon->delete($email_tokens[0]->getId(), $persistent_connection);
       
        header("location:index.php");
    }
} else {
    header("location:badpage.html");
}
?>
