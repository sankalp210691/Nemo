<?php

require "../db/DBConnect.php";
include "../req/SpecialFunctions.php";
include "../model/PostModel.php";
include "../controller/PostController.php";
include "../model/Sets_postModel.php";
include "../controller/Sets_postController.php";
include "../model/SetsModel.php";
include "../controller/SetsController.php";
include "../model/UserModel.php";
include "../controller/UserController.php";
include "../model/FriendModel.php";
include "../controller/FriendController.php";
include "../supporter/FriendSupporter.php";
include "../model/FollowModel.php";
include "../controller/FollowController.php";
include "../supporter/PostSupporter.php";
include "../model/TagsModel.php";
include "../controller/TagsController.php";
include "../model/Post_tagsModel.php";
include "../controller/Post_tagsController.php";
include "../model/Friend_postModel.php";
include "../controller/Friend_postController.php";
include "../model/LikesModel.php";
include "../controller/LikesController.php";
include "../model/CommentsModel.php";
include "../controller/CommentsController.php";
include "../model/SharesModel.php";
include "../controller/SharesController.php";

$req = $_GET["req"];
if ($req == "create") {
    echo createPost(json_decode($_GET["post"]));
} else if($req=="fetchImage"){
    echo fetchImage($_GET["url"]);
}else if ($req == "get_world_feed") {
    echo json_encode(getWorldFeed(trim($_GET["user_id"]), trim($_GET["start"]), trim($_GET["limit"])));
}else if($req=="get_friend_feed"){
    echo json_encode(getFriendFeed(trim($_GET["user_id"]), trim($_GET["start"]), trim($_GET["limit"])));
}else if($req=="get_following_feed"){
    echo json_encode(getFollowingFeed(trim($_GET["user_id"]), trim($_GET["start"]), trim($_GET["limit"])));
}else if($req=="get_private_feed"){
    echo json_encode(getPrivateFeed(trim($_GET["user_id"]), trim($_GET["start"]), trim($_GET["limit"])));
}else if ($req == "get_user_feed") {
    echo json_encode(getUserFeed(trim($_GET["user_id"]), trim($_GET["uid"]), trim($_GET["start"]), trim($_GET["limit"])));
} else if ($req == "get_post") {
    echo json_encode(getPost(trim($_GET["id"])));
} else if ($req == "get_post_detail") {
    echo json_encode(getPostDetail(trim($_GET["id"]), trim($_GET["uid"]),null));
} else if ($req == "like") {
    if ($_GET["type"] == "post")
        echo likePost($_GET["id"], $_GET["user_id"]);
    else if ($_GET["type"] == "comment")
        echo likeComment($_GET["id"], $_GET["user_id"]);
}else if ($req == "unlike") {
    if ($_GET["type"] == "post")
        unlikePost($_GET["post_id"], $_GET["like_id"]);
    else if ($_GET["type"] == "comment")
        unlikeComment($_GET["comment_id"], $_GET["like_id"]);
} else if ($req == "get_user_albums") {
    echo json_encode(getUserAlbums($_GET["user_id"], $_GET["start"], $_GET["limit"]));
} else if ($req == "get_album_photos") {
    echo json_encode(getAlbumPhotos($_GET["user_id"], $_GET["album_id"], $_GET["start"], $_GET["limit"]));
} else if($req=="get_user_videos"){
    echo json_encode(getUserVideos($_GET["user_id"], $_GET["start"], $_GET["limit"]));
}else if($req=="get_user_links"){
    echo json_encode(getUserWeblinks($_GET["user_id"], $_GET["start"], $_GET["limit"]));
}else if ($req == "post_comment") {
    echo postComment($_GET["post_id"],$_GET["user_id"],$_GET["comment"],$_GET["type"]);
} else if($req=="share_post"){
    echo sharePost(json_decode($_GET["post"]));
}else {
    header("location:../badpage.html");
}
?>
