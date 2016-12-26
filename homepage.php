<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("location:index.php");
}
$id = $_SESSION["id"];
////Keep the above part same, everywhere

require "db/DBConnect.php";
include "req/SpecialFunctions.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/PostModel.php";
include "controller/PostController.php";
include "supporter/PostSupporter.php";

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
$quickposts = getQuickPosts($id,0,20,$persistent_connection);
$quickposts_size = sizeof($quickposts);
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>NEMO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <link href="css/easydropdown.css" rel="stylesheet">
        <link href="css/perfect-scrollbar.css" rel="stylesheet">

        <script type="text/javascript" src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/special.js"></script>
        <script type="text/javascript" src="js/jquery.mousewheel.js"></script>
        <script type="text/javascript" src="js/perfect-scrollbar.js"></script>

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
            function getFeed(e, user_id, start, limit,type) {
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_"+type+"_feed&user_id=" + user_id + "&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='width:50px;margin-top:80px'>")
                        feed_loader.attr("src", "img/massive_ajax_loader.gif")
                        center.html(feed_loader)
                        if(start==0)
                            e.html(center)
                        else
                            e.append(center)
                    },
                    success: function(data) {
                        center.remove()
                        var data_length = data.length, i
                        for (i = 0; i < data_length; i++) {
                            var post = new PostTile(data[i])
                            post.arrangeTile(e, 4, "append",null)
                        }
                    },
                    error: function(e, f) {
                        alertBox("Some error occured. Please try again later.")
                    }
                })
            }
            $(document).ready(function() {
                $("#quickpost").perfectScrollbar({suppressScrollX: true});
                $("#home_menu li").click(function(){
                    if($(this).hasClass("ctb")){}else{
                        $("#home_menu li").removeClass("ctb");
                        $(this).addClass("ctb");
                        var id = $(this).attr("id");
                        if(id=="wd"){
                            getFeed($("#wall"),user_id,0,20,"world");
                        }else if(id=="fr"){
                            getFeed($("#wall"),user_id,0,20,"friend");
                        }else if(id=="fl"){
                            getFeed($("#wall"),user_id,0,20,"following");
                        }else if(id=="pf"){
                            getFeed($("#wall"),user_id,0,20,"private");
                        }
                    }
                })
            })
        </script>
        <style>
            #wall{
                margin-top:40px;
                padding-top:10px;
                padding-bottom:30px;
                margin:0 auto;
                width:95%;
                padding-left:0;
                padding-right:0;    
            }

            #wall_menu{
                display:table;
                padding:10px 40px;
                background:white;
                border-radius:3px;
                border-bottom:1px solid #ccc;
                width:100%;

                box-shadow: 0 0 3px #ccc;
                -o-box-shadow: 0 0 3px #ccc;
                -webkit-box-shadow: 0 0 3px #ccc;
                -moz-box-shadow: 0 0 3px #ccc;
            }

            #qp_div{
                float:right;
                position:fixed;
                top:0;
                right:0;
                margin-top:55px;
                background:white;
                height:100%;
                height:calc(100% - 3.475em);
                height:expression(100% - 3.475em);
                width:15%;

                -webkit-box-shadow:  -3px 0 3px #999;
                -moz-box-shadow:   -3px 0 3px #999;
                box-shadow:   -3px 0 3px #999;
                -o-box-shadow:  -3px 0 3px #999;
            }

            #qp_div h3{
                color:#444;
                margin:10px;
            }

            #quickpost{
                height:100%;
                width:100%;
                position:relative;
                overflow: hidden;
            }

            .qimg{
                float:left;
                margin-right:10px;
                height:30px;
                width:30px;
                position: relative;
                background:#f7f7f7;
                background-size:cover;
                background-repeat: no-repeat;
                background-position: top;
                -webkit-box-shadow: inset 0 0 30px black;
                -moz-box-shadow: inset 0 0 30px black;
                box-shadow: inset 0 0 30px black;
            }

            .qtitle{
                font-family:"Calibri";
                font-size:15px;
            }

            .qp{
                padding:10px;
                width:100%;
                width:calc(100% - 1.245em);
                width:expression(100% - 1.245em);
                border-top:1px solid #ccc;
                border-bottom:1px solid #ccc;
                overflow:hidden;
                background:white;
                cursor:pointer;

                word-wrap: break-word;
                word-break: break-all;

                transition: all 0.3s ease-out;
                -webkit-transition: all 0.3s ease-out;
                -moz-transition: all 0.3s ease-out;
                -ms-transition: all 0.3s ease-out;
                -o-transition: all 0.3s ease-out;
            }

            .qp:hover{
                transform: scale(1.1);
                -webkit-transform: scale(1.1);
                -moz-transform: scale(1.1);
                -ms-transform: scale(1.1);
                -o-transform: scale(1.1);
            }
        </style>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="wall_container" style="float:left;width:85%;padding-top:55px">
                <div id="wall_menu">
                    <ul id="home_menu" class="hori_menu" style="width:50%">
                        <li id="wd" class="ctb">World</li>
                        <li id="fr">Friends</li>
                        <li id="fl">Following</li>
                        <li id="pf">Private Feed</li>
                    </ul>
                </div>
                <div id="wall">
                    <script>
                        var wall = $("#wall")
                        getFeed($("#wall"), user_id, 0, 36,"world")
                    </script>
                </div>
            </div>
            <div id="qp_div">
                    <h3>Quickposts</h3>
                <div id='quickpost'>
                    <?php
                    for ($i = 0; $i < $quickposts_size; $i++) {
                        ?>
                        <a href='post.php?id=<?php echo $quickposts[$i]["id"] ?>'>
                            <div class='qp'>
                                <?php 
                                    if($quickposts[$i]["type"]=="photo" || ($quickposts[$i]["type"]=="link" && $quickposts[$i]["url_content_type"]=="photo")){
                                        $src=$quickposts[$i]["src"];
                                    }else if($quickposts[$i]["type"]=="video" || ($quickposts[$i]["type"]=="link" && $quickposts[$i]["url_content_type"]=="video")){
                                        $src = "users/images/" . md5(video_image($quickposts[$i]["src"])) . ".jpg";
                                    }
                                ?>
                                <div class="qimg" style="background-image:url('<?php echo $src ?>')"></div>
                                <div>
                                    <div class="qtitle"><a class='black_link' href="post.php?id=<?php echo $quickposts[$i]["id"] ?>"><?php echo $quickposts[$i]["title"] ?></a></div>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jQueryRotate.js"></script>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>
    </body>
</html>