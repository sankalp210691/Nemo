<?php
session_start();
if (!isset($_GET["id"])) {
    header("location:badpage.html");
    return;
}
$uid = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($uid, ".") != false) {
    header("location:badpage.html");
    return;
}
if (is_numeric($uid) == false) {
    header("location:badpage.html");
    return;
}
if ($uid < 1) {
    header("location:badpage.html");
    return;
}
if (!isset($_SESSION['id'])) {
    header("location:pubPr.php?id=$uid");
    return;
}
$id = $_SESSION["id"];
//Keep the above part same, everywhere
include "req/SpecialFunctions.php";
require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "supporter/SetsSupporter.php";
include "supporter/FriendSupporter.php";

$usrcon = new UserController();
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
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
$blur_profile_pic = substr($profile_pic, 0, strrpos($profile_pic, "/")) . "/blur_" . substr($profile_pic, strrpos($profile_pic, "/") + 1);

$pusercon = new UserController();
$puser = $pusercon->getByPrimaryKey($uid, array("first_name", "last_name"), null, $persistent_connection);
$pfirst_name = $puser->getFirst_name();
$plast_name = $puser->getLast_name();

if ($uid == $id)
    $self = 1;
else
    $self = 0;

$tab = "created";
if (isset($_GET["tab"])) {
    $tab = trim($_GET["tab"]);
    if ($tab != "created" && $tab != "following") {
        $tab = "created";
    }
}
$sets = getUserSets($uid, $id, $tab, $persistent_connection);
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Rancho' rel='stylesheet' type='text/css'>
        <script src="js/jquery-latest.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script src="js/special.js"></script>
        <title>Sets - <?php echo $first_name . " " . $last_name ?></title>
        <style>
            #set_menu_div{
                display:table;
                width:100%;
                width:calc(100% - 5em);
                background:white;
                padding:20px 40px;
                border-bottom:1px solid #ccc;
                padding-top:70px;
            }

            .set{
                float:left;
                display:table;
                height:180px;
                width:320px; 
                margin:10px;
                border-radius:2px;
            }

            .set:hover{
                box-shadow: 0 0 9px #222;
                -o-box-shadow: 0 0 9px #222;
                -webkit-box-shadow: 0 0 9px #222;
                -moz-box-shadow: 0 0 9px #222;
            }

            .sgal{
                float:left;
                width:50%;
                height:100%;
                background-color:#e2e2e2;
                background-size: cover;
                background-repeat: no-repeat;
            }

            .sinfo{
                float:left;
                background:white;
                padding:10px;
                width:50%;
                width:calc(50% - 1.25em);
                width:expression(50% - 1.25em);
                height:100%;
            }

            .stitle{
                font-family: "Calibri";
                font-size:20px;
                color:#444;
            }

            .setdesc{
                color:#444;
            }

            .spreview{
                clear:both;
            }

            .pr{
                width:40px;
                height:40px;
                margin-top:10px;
                margin-right:5px;
                background-color:#e2e2e2;
                background-size: cover;
                background-repeat: no-repeat;
                float:left;
            }

            .pr:last-child{
                margin-right:0;
            }
        </style>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            var categories = <?php echo json_encode($_SESSION["categories"]) ?>;
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })

            function follow(id) {
                var follow_button = $("#set" + id).find("input[type='button']")
                var loader = $("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-right:20px;margin-top:20px;'>")
                $.ajax({
                    url: "manager/SetsManager.php",
                    cache: false,
                    type: "get",
                    data: "req=follow_set&user_id=<?php echo $id ?>&set_id=" + id,
                    beforeSend: function() {
                        follow_button.parent().prepend(loader)
                        follow_button.hide()
                    },
                    success: function(follow_id) {
                        loader.remove()
                        if (follow_id == -1) {
                            alertBox("Some error occured. Please try again later.")
                            follow_button.show()
                            return
                        }
                        var unfollow_button = $("<input type='button' class='wbutton' value='Unfollow' onclick='unfollow(" + follow_id + "," + id + ")' style='float:right;width:80px;height:30px;margin-top:5px;'>")
                        follow_button.parent().prepend(unfollow_button)
                        follow_button.remove()
                    }, error: function(e, f) {
                        loader.remove()
                        follow_button.show()
                        alertBox("Some error occured. Please try again later.")
                    }
                })
            }

            function unfollow(follow_id, set_id) {
                var unfollow_button = $("#set" + set_id).find("input[type='button']")
                var loader = $("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin-right:20px;margin-top:20px;'>")
                $.ajax({
                    url: "manager/SetsManager.php",
                    cache: false,
                    type: "get",
                    data: "req=unfollow_set&follow_id=" + follow_id,
                    beforeSend: function() {
                        unfollow_button.parent().prepend(loader)
                        unfollow_button.hide()
                    },
                    success: function(done) {
                        loader.remove()
                        if (done == -1) {
                            alertBox("Some error occured. Please try again later.")
                            unfollow_button.show()
                            return
                        }
                        var follow_button = $("<input type='button' class='gbutton' value='Follow' onclick='follow(" + set_id + ")' style='float:right;width:80px;height:30px;margin-top:5px;'>")
                        unfollow_button.parent().prepend(follow_button)
                        unfollow_button.remove()
                    }, error: function(e, f) {
                        loader.remove()
                        unfollow_button.show()
                        alertBox("Some error occured. Please try again later.")
                    }
                })
            }

            $(document).ready(function() {
                $(".rating").each(function() {
                    $(this).addRatingWidget();
                })
            })
        </script>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="set_menu_div">
                <ul class="hori_menu" id='set_menu'>
                    <?php
                    if ($self == 1) {
                        if ($tab == "created") {
                            ?>
                            <li class='ctb'>My sets</li>
                        <?php } else { ?>
                            <li><a href='setsgallery.php?id=<?php echo $uid ?>'>My sets</a></li>
                            <?php
                        }
                    } else {
                        if ($tab == "created") {
                            ?>
                            <li class='ctb'><?php echo $pfirst_name ?>'s sets</li>
                        <?php } else { ?>
                            <li><a href='setsgallery.php?id=<?php echo $uid ?>'><?php echo $pfirst_name ?>'s sets</a></li>
                            <?php
                        }
                    }
                    if ($tab == "following") {
                        ?>
                        <li class='ctb'>Following</li>
                    <?php } else { ?>
                        <li><a href='setsgallery.php?id=<?php echo $uid ?>&tab=following'>Following</a></li>
                    <?php } ?>
                </ul>
            </div>
            <div id="sets_area" style="margin-top:30px;padding-left:40px;">
                <?php
                $setarray_length = sizeof($sets);
                for ($i = 0; $i < $setarray_length; $i++) {
                    ?>
                    <div class="set gen_hover_shadow" id="s<?php echo $sets[$i]["id"] ?>">
                        <?php
                        if (($psize = sizeof($sets[$i]["display_pics"])) != 0) {
                            ?>
                            <div style="background-image:url('<?php echo $sets[$i]["display_pics"][0]["src"] ?>')" class='sgal'></div>
                        <?php }else{ ?>
                            <div style="background:#f7f7f7" class='sgal'></div>
                        <?php } ?>
                        <div class='sinfo'>
                            <p class='stitle'><a class="black_link" href="set.php?id=<?php echo $sets[$i]["id"] ?>"><?php echo $sets[$i]["name"] ?></a></p>
                            <br>
                            <p class='setdesc'><?php echo $sets[$i]["description"] ?></p>
                            <div class='ratingdiv fl' id='rating<?php echo $sets[$i]["id"] ?>'><input type='hidden' value='<?php echo $sets[$i]["rating"] ?>'></div>
                            <?php
                            if ($psize != 0) {
                                ?>
                                <div class="spreview">
                                    <?php
                                    for ($j = 0; $j < $psize; $j++) {
                                        ?>
                                        <div class='pr' style="background-image:url('<?php echo $sets[$i]["display_pics"][$j]["src"] ?>')"></div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div style="clear:both;">
                                <input type='button' class='gbutton' value='Follow' style='width:80px;margin-top:10px;'>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
//                $sets_size = sizeof($sets);
//                for ($i = 0; $i < $sets_size; $i++) {
                ?>
    <!--<div id="set<?php // echo $sets[$i]["id"]   ?>" class="set">-->
                <!--<div class="set_cvr">-->
                <?php
//                            if ($sets[$i]["display_pics"][0]["type"] == "photo") {
//                                $src = $sets[$i]["display_pics"][0]["src"];
//                            } else if ($sets[$i]["display_pics"][0]["type"] == "video") {
//                                $src = $src = "users/images/" . md5(video_image($sets[$i]["display_pics"][0]["src"])) . ".jpg";
//                            }
                ?>
                    <!--<a href='set.php?id=<?php // echo $sets[$i]["id"]   ?>'><img src="<?php // echo $sets[$i]["display_pics"][0]["src"]   ?>"></a>-->
                <!--</div>-->
                <!--<div class="set_desc">-->
                <!--<div class="set_name">-->
                <?php // echo $sets[$i]["name"] ?>
                <!--</div>-->
                <?php
//                            if ($self == 1 && $tab == "created") {
//                            } else {
//                                if ($sets[$i]["follow_id"] == "-1") {
                ?>
                        <!--<input type="button" class="gbutton" value="Follow" onclick="follow(<?php // echo $sets[$i]["id"]  ?>)" style="float:right;width:80px;height:30px;margin-top:-10px;">-->
                <?php
//                                } else {
                ?>
                        <!--<input type='button' class='wbutton' value='Unfollow' onclick='unfollow("<?php // echo $sets[$i]["follow_id"]  ?>", "<?php // echo $sets[$i]["id"]  ?>")' style='float:right;width:80px;height:30px;margin-top:-10px;'>-->
                <?php
//                                }
//                            }
                ?>
                <!--                            <div class="set_stat">
                                                <img src="img/view.png">&nbsp;<?php // echo $sets[$i]["views"]   ?>
                                                <div class="rating" style="display:table;margin-left:45px;margin-top:-10px;" data-rating="<?php // echo $sets[$i]["rating"]   ?>"></div>
                                            </div>
                                        </div>
                                    </div>-->
                <?php
//                }
                ?>
            </div>
        </div>
    </body>
</html>
