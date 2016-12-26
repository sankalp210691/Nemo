<?php

require "../db/DBConnect.php";
include "../model/FriendModel.php";
include "../controller/FriendController.php";
include "../supporter/FriendSupporter.php";
include "../model/FollowModel.php";
include "../controller/FollowController.php";
include "../model/GroupsModel.php";
include "../controller/GroupsController.php";
include "../model/Groups_friendModel.php";
include "../controller/Groups_friendController.php";
include "../model/UserModel.php";
include "../controller/UserController.php";
include "../req/SpecialFunctions.php";

$req = $_GET["req"];
if ($req == "get_friends") {
    echo json_encode(getFriends(trim($_GET["user_id"]), trim($_GET["start"]), trim($_GET["limit"])));
} else if ($req == "add_friend") {
    echo addFriend($_GET["id"], $_GET["uid"]);
} else if ($req == "cancel_req") {
    echo cancelRequest($_GET["id"]);
} else if ($req == "unfriend") {
    echo unfriend($_GET["fid"], $_GET["uid"], $_GET["fuid"]);
} else if ($req == "accept") {
    echo decideRequest($req, $_GET["id"]);
} else if ($req == "reject") {
    echo decideRequest($req, $_GET["id"]);
} else if ($req == "follow") {
    echo follow($_GET["uid"],$_GET["fuid"]);
} else if ($req == "unfollow") {
    echo unfollow($_GET["fid"],$_GET["uid"],$_GET["fuid"]);
}else if ($req == "get_requests") {
    echo json_encode(getRequests($_GET["user_id"]));
} else if ($req == "cancel") {
    echo cancelRequest($_GET["id"]);
} else if ($req == "get_mutual_friends") {
    echo json_encode(getMutualFriends($_GET["id1"], $_GET["id2"]));
} else if ($req == "search") {
    echo json_encode(searchFriend($_GET["uid"], $_GET["user_id"], $_GET["format"], $_GET["text"]));
} else if ($req == "create_group") {
    echo createGroup($_GET["user_id"], $_GET["group_name"], $_GET["group_type"], $_GET["is_block"], $_GET["is_private_sharing"], $_GET["is_suggest"], $_GET["list"]);
} else if ($req == "get_user_groups") {
    echo json_encode(getUserGroups($_GET["user_id"]));
} else if ($req == "get_group_details") {
    echo json_encode(getGroupDetails($_GET["id"]));
} else if ($req == "update_group") {
    echo updateGroup($_GET["group_id"], $_GET["group_name"], $_GET["user_id"], $_GET["group_type"], $_GET["is_block"], $_GET["is_private_sharing"], $_GET["is_suggest"], $_GET["list"]);
} else {
    header("location:../badpage.html");
}
?>
