<?php

require "../db/DBConnect.php";
include "../model/TagsModel.php";
include "../controller/TagsController.php";
include "../supporter/TagsSupporter.php";
include "../model/Tag_followerModel.php";
include "../controller/Tag_followerController.php";

$req = $_GET["req"];
if ($req == "get_tags_by_key") {
    echo json_encode(getTagsByKey(trim($_GET["key"])));
} else if ($req == "follow_tag") {
    echo followTag($_GET["user_id"], $_GET["tag_id"], null);
} else if ($req == "unfollow_tag") {
    echo unfollowTag($_GET["follow_id"], null);
} else if ($req == "convert_tag") {
    echo json_encode(convertTag($_GET["text"]));
} else {
    header("location:../badpage.html");
}
?>
