<?php
session_start();
$pid = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($pid, ".") != false) {
    header("location:badpage.html");
    return;
}
if (is_numeric($pid) == false) {
    header("location:badpage.html");
    return;
}
if ($pid < 1) {
    header("location:badpage.html");
    return;
}
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    return;
}

$id = $_SESSION["id"];
//Keep the above part same, everywhere

require "db/DBConnect.php";
include "req/SpecialFunctions.php";
include "model/PostModel.php";
include "controller/PostController.php";
include "model/Sets_postModel.php";
include "controller/Sets_postController.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";
include "supporter/FriendSupporter.php";
include "supporter/PostSupporter.php";
include "model/TagsModel.php";
include "controller/TagsController.php";
include "model/Post_tagsModel.php";
include "controller/Post_tagsController.php";
include "model/Friend_postModel.php";
include "controller/Friend_postController.php";
include "model/LikesModel.php";
include "controller/LikesController.php";
include "model/CommentsModel.php";
include "controller/CommentsController.php";
include "model/SharesModel.php";
include "controller/SharesController.php";

$user = new User();
$usrcon = new UserController();
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
$user = $usrcon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage"), null, $persistent_connection);
if ($user == null)
    header("location:logout.php");
if ($user->getSignup_stage() == 0) {
    session_destroy();
    header("location:index.php");
    return;
} else if ($user->getSignup_stage() == 1) {
    header("location:getting_started.php");
    return;
}
$first_name = $user->getFirst_name();
$last_name = $user->getLast_name();
$profile_pic = $user->getProfile_pic();
if ($profile_pic == null || strlen($profile_pic) == 0) {
    $profile_pic = "img/default_profile_pic.jpg";
}
$blur_profile_pic = getBlurPicAddress($profile_pic);
$post_detail = getPostDetail($pid, $id, $persistent_connection);
$post = $post_detail["main_post"];
$set_posts = $post_detail["set_post_array"];
$tag_posts = $post_detail["tag_post_array"];
$set_posts_size = sizeof($set_posts);
$tag_posts_size = sizeof($tag_posts);
$query = "select p.id,p.type,p.src,p.url_content_type from post p where p.id in (select l.post_id from likes l join likes l1 on l.user_id=l1.user_id where l1.post_id=?) order by score desc limit 0,30";
$statement = $persistent_connection->prepare($query);
$statement->bind_param("i", $pid);
$statement->execute();
$statement->bind_result($post_id, $post_type, $post_src, $url_content_type);
$people_liked_posts = array();
$i=0;
while($statement->fetch()){
    if($post_type=="video" || ($post_type=="link" && $url_content_type=="video")){
        $post_src = "users/images/" . md5(video_image($post_src)) . ".jpg";
    }
    $people_liked_posts[$i] = array(
        "id"=>$post_id,
        "type"=>$post_type,
        "src"=>$post_src,
        "url_content_type"=>$url_content_type
    );
    $i++;
}
$people_liked_posts_size = sizeof($people_liked_posts);
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo decorateWithLinks(unrenderHTML($post["title"])) ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">

        <script type="text/javascript" src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/special.js"></script>
        <script type="text/javascript">
            user_id = "<?php echo $id ?>"
            user_name = "<?php echo $first_name . " " . $last_name ?>"
            profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            var categories = <?php echo json_encode($_SESSION["categories"]) ?>;
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })
            //Faking Placeholder
            $('input[placeholder]').placeholder();
            $('textarea[placeholder]').placeholder();
            //Faking Placeholder ends
            $(document).ready(function() {
                $("#tag_area .tag").hover(function() {
                    $(this).addClass("tag_hover")
                }, function() {
                    $(this).removeClass("tag_hover")
                })
                $(".gallery").width($("#set_post").width())
<?php
if ($set_posts_size <= 30 && $set_posts_size > 20) {
    ?>
                    $(".gallery").height($(".gallery").width() * (3 / 10))
<?php } else if ($set_posts_size <= 20 && $set_posts_size > 10) { ?>
                    $(".gallery").height($(".gallery").width() * (2 / 10))
<?php } else if ($set_posts_size <= 10 && $set_posts_size > 0) { ?>
                    $(".gallery").height($(".gallery").width() / 10)
<?php } else { ?>
                    $(".gallery").height(0)
<?php } ?>
                $(".imgn").height($(".gallery").width() / 10)
                $(".imgn").width($(".imgn").height() - 0.4)
<?php
if ($post["postType"] == "photo" || ($post["postType"] == "link" && $post["url_content_type"] == "photo")) {
    $post_width = $post["width"];
    $post_height = $post["height"];
    ?>
                    var img = $("<img src='<?php echo $post["src"] ?>'>")
    <?php
} else if ($post["postType"] == "video" || ($post["postType"] == "link" && $post["url_content_type"] == "video")) {
    $post_width = 400;
    $post_height = 300;
    ?>
                    var img = $("<img src='<?php echo "users/images/" . md5(video_image($post["src"])) . ".jpg"; ?>'>")
<?php } ?>
                var op_board = $("#op_board")
                op_board.Like("<?php echo $pid ?>", "post", "<?php echo $post["activity"]["user_liked"] ?>", null, "dark")
<?php if (($self == 0 && $post["sharable"] == 1) || ($self == 1)) { ?>
                    op_board.Share("<?php echo $pid ?>", "<?php echo $post["postType"] ?>", [img, "<?php echo $photo_width ?>", "<?php echo $photo_width ?>"], "dark", null)
    <?php
}
if (($self == 0 && $post["commentable"] == 1) || ($self == 1)) {
    ?>
                    $("#commentbox").elastic();
                    $("#commentbox").css({
                        "padding-top": "10px",
                        "height": "25px"
                    })
                    $("#commentbox").keyup(function() {
                        if ($.trim($(this).val()).length != 0)
                            $("#postcomment").fadeIn("500");
                        else
                            $("#postcomment").fadeOut("500");
                    })
<?php }
?>
<?php
if ($post["postType"] == "video" || ($post["postType"] == "link" && $post["url_content_type"] == "video")) {
    ?>
                    $("#main_post").height($("#main_post").width() * (9 / 16))
<?php } ?>
            })
        </script>
        <style>
            #filler{
                padding-top:80px;
                margin:0 auto;
                width:95%;
                padding-left:0;
                padding-right:0;   
            }

            .genelement{
                background:white;
                margin-bottom:20px;
                border-radius:2px;
                -webkit-box-shadow:  0 0 13px #666;
                -moz-box-shadow:  0 0 13px #666;
                box-shadow:  0 0 13px #666;
                -o-box-shadow: 0 0 13px #666;
            }

            #col1{
                float:left;
                width:50%;
            }

            #post_arena{
                width:100%;
            }

            #post_title{
                padding:15px 20px;
                background:white;
                border-radius:2px 2px 0 0;
                font-size:30px;
                color:#444;
            }

            #main_post{
                <?php
                if ($post["postType"] == "photo" || ($post["postType"] == "link" && $post["url_content_type"] == "photo")) {
                    ?>
                    display:table;
<?php } ?>
                margin:0;
                padding:0;
                width:100%;
            }
            <?php
            if ($post["postType"] == "photo" || ($post["postType"] == "link" && $post["url_content_type"] == "photo")) {
                ?>
                #main_post img{
                    margin:0 auto;
                    max-width:100%;
                }

<?php } ?>
            #story_area{
                padding:15px 20px;
                border-radius:0 0 2px 2px;
                background:white;
                font-family:"Calibri";
                color:#444;
                word-wrap: break-word;
                word-break: break-all;
            }

            #col2{
                float:left;
                width:48%;
                margin-left:20px;
            }

            #gen_info,#set_post{
                width:100%;
                margin-bottom:20px;
            }

            #user_area,.bhead{
                padding:15px 20px;
            }

            #pcircle,#ppic{
                float:left;
                width:30px;
                height:30px;
                border-radius:50%;
            }

            #pcircle{
                border:3px solid white;
                box-shadow: 0 0 4px #777;
                -o-box-shadow: 0 0 4px #777;
                -webkit-box-shadow: 0 0 4px #777;
                -moz-box-shadow: 0 0 4px #777;
            }

            #tag_area{
                padding-bottom:15px;
                display:table;
            }

            #op_board{
                padding-left:20px;
                padding-bottom:10px;
            }

            .bhead{
                font-size:18px;
                color:#444;
                font-family: "Calibri";
            }
        </style>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <?php
            if ($post["postType"] == "photo" || $post["postType"] == "video" || $post["postType"] == "link") {
                ?>
                <div id="filler">
                    <div id="col1">
                        <div id="post_arena" class="genelement">
                            <div id="post_title"><?php echo decorateWithLinks(unrenderHTML($post["title"])) ?></div>
                            <div id="main_post">
                                <?php
                                if ($post["postType"] == "photo" || ($post["postType"] == "link" && $post["url_content_type"] == "photo")) {
                                    ?>
                                    <center><img id="post_element" src="<?php echo $post["src"] ?>"></center>
                                <?php } else if ($post["postType"] == "video" || ($post["postType"] == "link" && $post["url_content_type"] == "video")) { ?>
                                    <embed src="<?php echo $post["src"] ?>?autoplay=1&rel=0" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash" background="black" width="100%" height="100%"></embed>
    <?php } ?>
                            </div>
                            <div id="story_area"><?php echo decorateWithLinks(unrenderHTML($post["description"])) ?></div>
                        </div>
                        <div id = "set_post" class = "genelement">
                            <div class = "bhead">People who liked this also liked</div>
                            <div id = "spost_gallery" class = 'gallery' style = "position:relative;overflow:hidden">
                                <?php
                                for ($i = 0; $i < $people_liked_posts_size; $i++) {
                                    ?>
                                    <a href="post.php?id=<?php echo $people_liked_posts[$i]["id"] ?>"><div class="imgn" style="background-image: url('<?php echo $people_liked_posts[$i]["src"] ?>')"></div></a>
    <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="col2">
                        <div id="gen_info" class="genelement">
                            <div id="user_area">
                                <div id="pcircle"><img id="ppic" src="<?php echo getBlurPicAddress($post["profile_pic"]) ?>"></div>
                                <a style="float:left;font-weight:bold;font-size:20px;margin-left:10px;" href="profile.php?id=<?php echo $post["user_id"] ?>"><?php echo $post["first_name"] . " " . $post["last_name"] ?></a>
                                <span style="float:right;color:#777;font-family:Calibri;font-size:15px"><?php echo formattedDate($post["date"]) . " | " . $post["time"] ?></span>
                            </div><br>
                            <div id="tag_area">
                                <?php
                                $no_of_tags = sizeof($post["tags"]);
                                for ($i = 0; $i < $no_of_tags; $i++) {
                                    ?>
                                    <a href="tag.php?id=<?php echo $post["tags"][$i]["id"] ?>">
                                        <div class='tag cp'>
                                            <input type='hidden' value='<?php echo $post["tags"][$i]["id"] ?>'>
                                            <span class='val'><?php echo $post["tags"][$i]["name"] ?></span>
                                        </div>
                                    </a>
    <?php } ?>
                            </div>
                            <div id='op_board'></div>
    <?php if (($self == 0 && $post["commentable"] == 1) || ($self == 1)) { ?>
                                <textarea id="commentbox" placeholder="Comment" style="resize:none;padding-top:10px;height:25px;width:100%;margin:20px;margin-top:0;width:calc(100% - 3.25em);"></textarea>
                                <input id="postcomment" type="button" class="bbutton" value="Post" style="width:80px;display:none;margin-left:20px;margin-top:-10px;margin-bottom:10px;">
                                <?php
                            }
                            ?>
                        </div>
                        <div id = "set_post" class = "genelement">
                            <div class = "bhead">Other posts from this set</div>
                            <div id = "spost_gallery" class = 'gallery' style = "position:relative;overflow:hidden">
                                <?php
                                for ($i = 0; $i < $set_posts_size; $i++) {
                                    ?>
                                    <a href="post.php?id=<?php echo $set_posts[$i]["id"] ?>"><div class="imgn" style="background-image: url('<?php echo $set_posts[$i]["src"] ?>')"></div></a>
    <?php } ?>
                            </div>
                        </div>
                        <div id = "set_post" class = "genelement">
                            <div class = "bhead">Similar tagged posts</div>
                            <div id = "spost_gallery" class = 'gallery' style = "position:relative;overflow:hidden">
                                <?php
                                for ($i = 0; $i < $tag_posts_size; $i++) {
                                    ?>
                                    <a href="post.php?id=<?php echo $tag_posts[$i]["id"] ?>"><div class="imgn" style="background-image: url('<?php echo $tag_posts[$i]["src"] ?>')"></div></a>
    <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
<?php } ?>
        </div>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>    
        <script type="text/javascript" src="js/jquery-ui.js"></script>
    </body>
</html>