<?php

require "../db/DBConnect.php";
include "../model/SetsModel.php";
include "../controller/SetsController.php";
include "../model/Sets_categoryModel.php";
include "../controller/Sets_categoryController.php";
include "../supporter/SetsSupporter.php";

$req = $_GET["req"];
if ($req == "create") {
    echo createSet(trim($_GET["name"]),trim($_GET["desc"]), 2, trim($_GET["cid"]), trim($_GET["user_id"]));
} else if ($req == "get_sets") {
    echo json_encode(getSets(trim($_GET["user_id"]), trim($_GET["get_preview"])));
} else if ($req == "follow_set") {
    include "../model/Set_followerModel.php";
    include "../controller/Set_followerController.php";
    include "../supporter/Set_followerSupporter.php";

    echo followSet($_GET["user_id"], $_GET["set_id"]);
} else if ($req == "unfollow_set") {
    include "../model/Set_followerModel.php";
    include "../controller/Set_followerController.php";
    include "../supporter/Set_followerSupporter.php";
    
    echo unfollowSet($_GET["follow_id"]);
} else if($req=="edit"){
    editSet($_GET["id"],$_GET["name"]);
}else if($req=="get_set_categories"){
    echo json_encode(getSetCategories($_GET["set_id"], null));
}else {
    header("location:../badpage.html");
}
?>
