<?php
session_start();
$tid = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($tid, ".") != false) {
    header("location:badpage.html");
    return;
}
if (is_numeric($tid) == false) {
    header("location:badpage.html");
    return;
}
if ($tid < 1) {
    header("location:badpage.html");
    return;
}
if (!isset($_SESSION['id'])) {
    $online = 0;
} else {
    $online = 1;
    $id = $_SESSION["id"];
}

require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/TagsModel.php";
include "controller/TagsController.php";
include "supporter/TagsSupporter.php";
include "model/Tag_followerModel.php";
include "controller/Tag_followerController.php";
include "req/SpecialFunctions.php";

$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();

if ($online == 1) {
    $usrcon = new UserController();
    $user = $usrcon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage"), null, $persistent_connection);
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

    $tag_follower = new Tag_follower();
    $tag_followercon = new Tag_followerController();
    $tag_follower->setUser_id($id);
    $tag_follower->setTag_id($tid);
    $tag_followers = $tag_followercon->findByAll($tag_follower, array("id"), null, $persistent_connection);
    if (sizeof($tag_followers) == 0) {
        $following = false;
    } else {
        $following = true;
        $follow_id = $tag_followers[0]->getId();
    }
}
$tagcon = new TagsController();
$tag = $tagcon->getByPrimaryKey($tid, array("*"), null, $persistent_connection);
$tag_name = $tag->getName();
$tag_score = $tag->getScore();

$popular_posts = getTagPosts($tid, "popular", "all", 0, 20, $persistent_connection);
$popular_posts_size = sizeof($popular_posts);

$popular_photos = getTagPosts($tid, "popular", "photo", 0, 3, $persistent_connection);
$popular_photos_size = sizeof($popular_photos);

$popular_videos = getTagPosts($tid, "popular", "video", 0, 3, $persistent_connection);
$popular_videos_size = sizeof($popular_videos);

$recent_posts = getTagPosts($tid, "recent", "all", 0, 5, $persistent_connection);
$recent_posts_size = sizeof($recent_posts);

$most_liked_posts = getTagPosts($tid, "most_liked", "all", 0, 4, $persistent_connection);
$most_liked_posts_size = sizeof($most_liked_posts);

$most_shared_posts = getTagPosts($tid, "most_shared", "all", 0, 4, $persistent_connection);

$most_commented_posts = getTagPosts($tid, "most_commented", "all", 0, 4, $persistent_connection);

$top_users = getTopTagUsers($tid, 0, 5, $persistent_connection);
$top_users_size = sizeof($top_users);

$top_sets = getTopTagSets($tid, 0, 5, $persistent_connection);

$assoc_tags = getAssociatedTags($tid, 0, 10, $persistent_connection);
$assoc_tags_size = sizeof($assoc_tags);

$db_connection->mysqli_connect_close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/tag.css" rel="stylesheet">
        <script src="js/jquery-latest.js"></script>
        <script src="js/special.js"></script>
        <title><?php echo $tag_name ?></title>
        <script type="text/javascript">
<?php if ($online == 1) { ?>
                user_id = "<?php echo $id ?>"
                user_name = "<?php echo $first_name . " " . $last_name ?>"
                profile_pic = "<?php echo $profile_pic ?>"
                var blur_profile_pic = $("<img>")
                var categories = <?php echo json_encode($_SESSION["categories"]) ?>;
                blur_profile_pic.attr({
                    "src": "<?php echo $blur_profile_pic ?>"
                })

                function follow(e) {
                    $.ajax({
                        url: "manager/TagManager.php",
                        type: "get",
                        data: "req=follow_tag&user_id=" + user_id + "&tag_id=<?php echo $tid ?>",
                        beforeSend: function() {
                            $(e).replaceWith("<img id='floader' src='img/ajax_loader_horizontal.gif'>");
                        }, success: function(follow_id) {
                            if (follow_id != null) {
                                $("#floader").replaceWith("<input data-follow-id='" + follow_id + "' type='button' class='wbutton follow' value='Unfollow' onclick='unfollow(this)'>");
                            } else {
                                alertBox();
                                $("#floader").replaceWith("<input type='button' class='gbutton follow' value='Follow' onclick='follow(this)'>");
                            }
                        }, error: function(e, f) {
                            alertBox();
                            $("#floader").replaceWith("<input type='button' class='gbutton follow' value='Follow' onclick='follow(this)'>");
                        }
                    })
                }

                function unfollow(e) {
                    var follow_id = $(e).attr("data-follow-id");
                    $.ajax({
                        url: "manager/TagManager.php",
                        type: "get",
                        data: "req=unfollow_tag&follow_id=" + follow_id,
                        beforeSend: function() {
                            $(e).replaceWith("<img id='floader' src='img/ajax_loader_horizontal.gif'>");
                        }, success: function(html) {
                            if (html == 1) {
                                $("#floader").replaceWith("<input type='button' class='gbutton follow' value='Follow' onclick='follow(this)'>");
                            } else {
                                alertBox();
                                $("#floader").replaceWith("<input data-follow-id='" + follow_id + "' type='button' class='wbutton follow' value='Unfollow' onclick='unfollow(this)'>");
                            }
                        }, error: function(e, f) {
                            alertBox();
                            $("#floader").replaceWith("<input data-follow-id='" + follow_id + "' type='button' class='wbutton follow' value='Unfollow' onclick='unfollow(this)'>");
                        }
                    })
                }
<?php } ?>
            //Faking Placeholder
            $('input[placeholder]').placeholder();
            $('textarea[placeholder]').placeholder();
            //Faking Placeholder ends

            var msp = <?php echo json_encode($most_shared_posts) ?>;
            var mcp = <?php echo json_encode($most_commented_posts) ?>;
            var tset = <?php echo json_encode($top_sets) ?>;

            $(document).ready(function() {
                $("#feat_div").width(0.95 * screen.width)
                $(".fcol").width(($("#featarea").width() - 5) / 2)
                $("#hposter").height(($(".fcol").height() / 2) - 4)
                $("#sposter1").width(($("#hposter").width() / 2) - 2)
                $("#sposter2").width(($("#hposter").width() / 2) - 2)
                $(".oimg").width((4 / 3) * $(".oimg").height())
                $(".pslide").height((9 / 16) * $(".pslide").width())
                $(".smallslide").height((9 / 16) * $(".smallslide").width())
                $("#vph_slide").height((3 / 4) * $(".smallslide").width())
                $(".index").click(function() {
                    if ($(this).hasClass("cin")) {
                    } else {
                        var index = $(this).attr("id").substring(1);
                        var curindex = $("div.cin").attr("id").substring(1)
                        $(".index").removeClass("cini")
                        $(this).addClass("cini")
                        $("#p" + curindex).fadeOut("500", "linear", function() {
                            $("#p" + index).fadeIn("500")
                        })
                    }
                })
                $("#assoctag .tag").hover(function() {
                    $(this).addClass("tag_hover")
                }, function() {
                    $(this).removeClass("tag_hover")
                })
                $("#featarea .poster").hover(function() {
                    $(this).children(".feat").show("slide", {direction: "down"}, 200)
                }, function() {
                    $(this).children(".feat").hide("slide", {direction: "down"}, 200)
                })
            })
        </script>
    </head>
    <body>
        <?php
        if ($online == 1) {
            include "head_menu.html";
            include "dock.html";
        } else {
            include "signup_prompt.html";
        }
        ?>
        <div id="container">
            <?php
            if ($online == 1) {
                ?>
                <div id="filler">
                    <?php
                } else if ($online == 0) {
                    ?>
                    <div id="filler" style="padding-top:120px;">    
                    <?php } ?>
                    <div id="feat_div" class="dshadow">
                        <div id="nmarea">
                            <ul class="linear_list">
                                <li style="float:left;">
                                    <p id="tag_name"><?php echo $tag_name ?></p>
                                </li>
                                <li>
                                    <?php
                                    if ($online == 1) {
                                        if ($following == false) {
                                            ?>
                                            <input type="button" class="gbutton follow" value="Follow" onclick="follow(this)">
                                            <?php
                                        } else if ($following == true) {
                                            ?>
                                            <input data-follow-id="<?php echo $follow_id ?>" type="button" class="wbutton follow" value="Unfollow" onclick="unfollow(this)">
                                            <?php
                                        }
                                    }
                                    ?>
                                </li>
                                <li style="margin-top:25px;">
                                    <?php echo $tag->getPosts() ?> posts
                                </li>
                                <li style="margin-top:25px;">
                                    <?php echo $tag->getFollowers() ?> followers
                                </li>
                            </ul>
                            <?php if ($popular_posts_size == 0) { ?>
                                <center style="clear:both;margin-bottom:20px;font-size:20px">This tag has no posts at the moment</center>
                            <?php } ?>
                        </div>
                        <?php if ($popular_posts_size != 0) { ?>
                            <div id="tarea">
                                <div id="featarea">
                                    <div class="fcol">
                                        <div style="background-image:url('<?php echo $popular_posts[0]["src"] ?>')" id="bposter" class="poster">
                                            <div class='feat'>
                                                <p class='ftitle'><?php echo $popular_posts[0]["title"] ?></p>
                                                <?php
                                                $desc = $popular_posts[0]["description"];
                                                if (strlen($desc) > 50)
                                                    $desc = substr(0, 47) . "...";
                                                ?>
                                                <p class='finfo'><?php echo $desc ?></p>
                                                <center><a href="post.php?id=<?php echo $popular_posts[0]["id"] ?>" style="text-decoration:none;"><input type='button' class='gbutton'style='width:80px;margin-top:10px;' value="View post"></a></center>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fcol">
                                        <?php
                                        if ($popular_posts_size > 1) {
                                            ?>
                                            <div style="background-image:url('<?php echo $popular_posts[1]["src"] ?>')" id="hposter" class="poster">
                                                <div class='feat'>
                                                    <p class='ftitle'><?php echo $popular_posts[1]["title"] ?></p>
                                                    <?php
                                                    $desc = $popular_posts[1]["description"];
                                                    if (strlen($desc) > 50)
                                                        $desc = substr(0, 47) . "...";
                                                    ?>
                                                    <p class='finfo'><?php echo $desc ?></p>
                                                    <center><a href="post.php?id=<?php echo $popular_posts[1]["id"] ?>" style="text-decoration:none;"><input type='button' class='gbutton'style='width:80px;margin-top:10px;' value="View post"></a></center>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php
                                        if ($popular_posts_size > 2) {
                                            ?>
                                            <div style='width:100%;height:50%;margin-top:2px;'>
                                                <div style="background-image:url('<?php echo $popular_posts[2]["src"] ?>')" id="sposter1" class="poster">
                                                    <div class='feat'>
                                                        <p class="ftitle ftitles"><?php echo $popular_posts[2]["title"] ?></p>
                                                        <center><a href="post.php?id=<?php echo $popular_posts[2]["id"] ?>" style="text-decoration:none;"><input type='button' class='gbutton'style='width:80px;margin-top:10px;' value="View post"></a></center>
                                                    </div>
                                                </div>
                                                <?php
                                                if ($popular_posts_size > 3) {
                                                    ?>
                                                    <div style="background-image:url('<?php echo $popular_posts[3]["src"] ?>')" id="sposter2" class="poster">
                                                        <div class='feat'>
                                                            <p class="ftitle ftitles"><?php echo $popular_posts[3]["title"] ?></p>
                                                            <center><a href="post.php?id=<?php echo $popular_posts[3]["id"] ?>" style="text-decoration:none;"><input type='button' class='gbutton'style='width:80px;margin-top:10px;' value="View post"></a></center>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div id="otharea">
                                    <?php
                                    $s = $popular_posts_size;
                                    if ($s > 7)
                                        $s = 7;
                                    for ($i = 4; $i < $s; $i++) {
                                        ?>
                                        <div class="othpost">
                                            <div class="oimg" style="background-image:url('<?php echo $popular_posts[$i]["src"] ?>')"></div>
                                            <div>
                                                <div class="otitle"><a href="post.php?id=<?php echo $popular_posts[$i]["id"] ?>"><?php echo $popular_posts[$i]["title"] ?></a></div>
                                                <?php
                                                $desc = $popular_posts[$i]["description"];
                                                if (strlen($desc) > 60)
                                                    $desc = substr(0, 57) . "...";
                                                ?>
                                                <div class="onews"><?php echo $desc ?></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div id="menu_bar">
                                <ul class="hori_menu">
                                    <li class="ctb">All posts</li>
                                    <li>Photos</li>
                                    <li>Videos</li>
                                    <li>Web Links</li>
                                    <li>Places</li>
                                    <li>Panorama</li>
                                </ul>
                            </div>
                            <div id="arena">
                                <div id="larena">
                                    <?php
                                    if ($popular_posts_size > 6) {
                                        ?>
                                        <div class="header">
                                            <p>Popular posts from <b>All Categories</b></p>
                                        </div>
                                        <div id="parea">
                                            <div id="pcararea">
                                                <center style='width:100%;margin-top:30px;'>
                                                    <?php
                                                    $s = $popular_posts_size;
                                                    if ($s > 12)
                                                        $s = 12;
                                                    for ($i = 7; $i < $s; $i++) {
                                                        ?>
                                                        <div class="pframe cin" id='p<?php $i - 7 ?>'>
                                                            <div class="pslide" style="background-image:url('<?php echo $popular_posts[$i]["src"] ?>')"></div>
                                                            <div class='pnews'>
                                                                <h1 class='ptitle'><a href='post.php?id=<?php echo $popular_posts[$i]["id"] ?>' class='black_link'><?php echo $popular_posts[$i]["title"] ?></a></h1>
                                                                <?php
                                                                $desc = $popular_posts[1]["description"];
                                                                if (strlen($desc) > 60)
                                                                    $desc = substr(0, 57) . "...";
                                                                ?>
                                                                <div class='pinfo'><?php echo $desc ?></div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class='frame_index'>
                                                        <?php
                                                        $s = $popular_posts_size;
                                                        if ($s > 12)
                                                            $s = 12;
                                                        if ($s > 7) {
                                                            ?>
                                                            <div class = 'index cini' id = 'i<?php $i - 7 ?>'></div>
                                                            <?php
                                                        }
                                                        for ($i = 8; $i < $s; $i++) {
                                                            ?>
                                                            <div class='index' id='i<?php $i - 7 ?>'></div>
                                                        <?php } ?>
                                                    </div>
                                                </center>
                                            </div>
                                            <?php
                                        }
                                        if ($popular_posts_size > 11) {
                                            ?>
                                            <div id="plistarea">
                                                <?php
                                                $s = $popular_posts_size;
                                                if ($s > 16)
                                                    $s = 16;
                                                for ($i = 12; $i < $s; $i++) {
                                                    ?>
                                                    <div id='pl<?php echo $i - 12 ?>' class='pl'>
                                                        <div class="plsq" style="background-image:url('<?php echo $popular_posts[$i]["src"] ?>')"></div>
                                                        <div class="plname"><a class="black_link" href="post.php?id=<?php echo $popular_posts[$i]["id"] ?>"><?php echo $popular_posts[$i]["title"] ?></a></div>
                                                    </div> 
                                                <?php } ?>
                                                <div class='loader'>Load more posts</div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    if ($most_liked_posts_size > 0 || $most_commented_posts_size > 0 || $most_shared_posts_size > 0) {
                                        ?>
                                        <div class="header">
                                            <ul class='linear_list' id='mostposts'>
                                                <li class='ctl'>Most Liked</li>
                                                <li>Most Shared</li>
                                                <li>Most commented</li>
                                            </ul>
                                        </div>
                                        <div id='mostpostsarea'>
                                            <?php
                                            for ($i = 0; $i < $most_liked_posts_size; $i++) {
                                                ?>
                                                <div id='pl<?php echo $i ?>' class='pl'>
                                                    <div class="plsq" style="background-image:url('<?php echo $most_liked_posts[$i]["src"] ?>')"></div>
                                                    <div class="plname"><a class="black_link" href="post.php?id=<?php echo $most_liked_posts[$i]["id"] ?>"><?php echo $most_liked_posts[$i]["title"] ?></a></div>
                                                </div> 
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div id="marena">
                                    <div class="header">
                                        <ul class='linear_list'>
                                            <li class='ctl'>Top Users</li>
                                            <li>Top Sets</li>
                                        </ul>
                                    </div>
                                    <div id='tus'>
                                        <?php
                                        for ($i = 0; $i < $top_users_size; $i++) {
                                            ?>
                                            <div id='tu<?php echo $i ?>' class='tu'>
                                                <div class="pcircle">
                                                    <img src='<?php echo $top_users[$i]["profile_pic"] ?>' class="ppic">
                                                </div>
                                                <div class="tname"><a class="black_link" href="profile.php?id=<?php echo $top_users[$i]["id"] ?>"><?php echo $top_users[$i]["name"] ?></a></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php
                                    if ($popular_photos_size > 0) {
                                        ?>
                                        <div class='header'>
                                            <p>Popular photos</p>
                                        </div>
                                        <div id='pph'>
                                            <div id='pph_slide' class='smallslide' style="background-image:url('<?php echo $popular_photos[0]["src"] ?>')"></div>
                                            <div id='pph_list' class='ssliderlist'>
                                                <?php
                                                for ($i = 0; $i < $popular_photos_size; $i++) {
                                                    if ($i == 0) {
                                                        ?>
                                                        <div id='phl<?php echo $i ?>' class='sslidertype csslider'>
                                                        <?php } else { ?>
                                                            <div id='phl<?php echo $i ?>' class='sslidertype'>    
                                                            <?php } ?>
                                                            <div class="icon">
                                                                <img src='img/photo_hover.png' style='height:20px;'>
                                                            </div>
                                                            <div class="rpname ssl"><?php echo $popular_photos[$i]["title"] ?></div>
                                                        </div>
                                                    <?php } ?>
                                                    <div class='loader'>Load more posts</div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="header">
                                            <!--have to add rating widget and all-->
                                            <p>Most Rated Sets</p>
                                        </div>
                                        <div id='rpo'>
                                            <div id='rp0' class='rp'>
                                                <div class="rpsq" style="background-image:url('users/images/7369d237fdc761a3a186b133a4596075.jpg')"></div>
                                                <div class="rpname"><a class="black_link" href="profile.php?id=1">Google does it again. This time with sharks</a></div>
                                            </div> 
                                            <div id='rp0' class='rp'>
                                                <div class="rpsq" style="background-image:url('users/images/9b63e61dee0c157fae5969d45d607384.jpg')"></div>
                                                <div class="rpname"><a class="black_link" href="profile.php?id=1">Google does it again. This time with sharks</a></div>
                                            </div>
                                            <div id='rp0' class='rp'>
                                                <div class="rpsq" style="background-image:url('users/images/85eaaf4160ba6e27cb8c8b94053845c2.jpg')"></div>
                                                <div class="rpname"><a class="black_link" href="profile.php?id=1">Google does it again. This time with sharks</a></div>
                                            </div>
                                            <div id='rp0' class='rp'>
                                                <div class="rpsq" style="background-image:url('users/images/3ee245b5e23aeca8300e0aa6f3a81fbc.jpg')"></div>
                                                <div class="rpname"><a class="black_link" href="profile.php?id=1">Google does it again. This time with sharks</a></div>
                                            </div>
                                            <div id='rp0' class='rp'>
                                                <div class="rpsq" style="background-image:url('users/images/de84dffb5c22a012824fe81b383565e0.jpg')"></div>
                                                <div class="rpname"><a class="black_link" href="profile.php?id=1">Google does it again. This time with sharks</a></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rarena">
                                        <div class="header">
                                            <p>Recent Posts</p>
                                        </div>
                                        <div id='rpo'>
                                            <?php
                                            for ($i = 0; $i < $recent_posts_size; $i++) {
                                                ?>
                                                <div id='rp<?php echo $i ?>' class='rp'>
                                                    <div class="rpsq" style="background-image:url('<?php echo $recent_posts[$i]["src"] ?>')"></div>
                                                    <div class="rpname"><a class="black_link" href="post.php?id=<?php echo $recent_posts[$i]["id"] ?>"><?php echo $recent_posts[$i]["title"] ?></a></div>
                                                </div> 
                                            <?php } ?>
                                        </div>
                                        <div class="header">
                                            <p>Tags <b><?php echo $tag_name ?></b> is most associated with</p>
                                        </div>
                                        <div id='assoctag'>
                                            <?php
                                            for ($i = 0; $i < $assoc_tags_size; $i++) {
                                                ?>
                                                <a href="tag.php?id=<?php echo $assoc_tags[$i]["id"] ?>">
                                                    <div class='tag cp'>
                                                        <input type='hidden' value='<?php echo $assoc_tags[$i]["id"] ?>'>
                                                        <span class='val'><?php echo $assoc_tags[$i]["name"] ?></span>
                                                    </div>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <?php
                                        if ($popular_videos_size > 0) {
                                            ?>
                                            <div class="header">
                                                <p>Popular Videos</p>
                                            </div>
                                            <div id='vph'>
                                                <div id='vph_slide' class='smallslide' style="background-image:url('<?php echo $popular_videos[0]["src"] ?>')"></div>
                                                <div id='vph_list' class='ssliderlist'>
                                                    <?php
                                                    for ($i = 0; $i < $popular_videos_size; $i++) {
                                                        if ($i == 0) {
                                                            ?>
                                                            <div id='vhl0' class='sslidertype csslider'>
                                                            <?php } else { ?>
                                                                <div id='vhl0' class='sslidertype'>
                                                                <?php } ?>
                                                                <div class="icon">
                                                                    <img src='img/video_hover.png' style='width:20px;'>
                                                                </div>
                                                                <div class="rpname ssl">This is exactly what I was talking about</div>
                                                            </div> 
                                                        <?php } ?>
                                                        <div class='loader'>Load more posts</div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    </body>
                    <script type="text/javascript" src="js/jquery.masonry.min.js"></script>    
                    <script type="text/javascript" src="js/jquery-ui.js"></script>
                    </html>
