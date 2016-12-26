<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("location:index.php");
}
$id = $_SESSION["id"];
//Keep the above part same, everywhere

require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/User_stageModel.php";
include "controller/User_stageController.php";
include "model/CategoryModel.php";
include "controller/CategoryController.php";
include "model/InterestsModel.php";
include "controller/InterestsController.php";
include "supporter/InterestSupporter.php";

$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();

$user = new User();
$usercon = new UserController();

$user = $usercon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage"), null, $persistent_connection);

$add_contacts = 1;
$choose_interests = 1;

if ($user->getSignup_stage() == 0) {        //signup_stage=0 refers to NOT ACTIVATED
    $db_connection->mysqli_connect_close();
    session_destroy();
    header("location:index.php");
    return;
} else if ($user->getSignup_stage() > 1) {   //signup_stage=1 refers to getting_started page
    $db_connection->mysqli_connect_close();
    header("location:homepage.php");
    return;
} else {
    $user_stage = new User_stage();
    $user_stagecon = new User_stageController();
    $user_stage->setUser_id($id);
    $user_stage->setStatus(0);
    $user_stages = $user_stagecon->findByAll($user_stage, array("*"), "order by id", $persistent_connection);
    $user_stages_size = sizeof($user_stages);
    for ($i = 0; $i < $user_stages_size; $i++) {
        if ($user_stages[$i]->getStage_id() == 1) {
            $choose_interests = 0;
            continue;
        }
        if ($user_stages[$i]->getStage_id() == 2) {
            $add_contacts = 0;
            continue;
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link href="css/special.css" rel="stylesheet">
            <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
            <link href="css/getting_started.css" rel="stylesheet">
            <script type="text/javascript" src="js/jquery-latest.js"></script>
            <script type="text/javascript" src="js/special.js"></script>
            <script type="text/javascript">
                var user_id = "<?php echo $id ?>"
                //Faking Placeholder
                $('input[placeholder]').placeholder();
                $('textarea[placeholder]').placeholder();
                //Faking Placeholder ends
                $(document).ready(function() {
                    $("#intro").animate({
                        "margin-left": ($(window).width() - $("#intro span img").width()) / 2,
                        "opacity": 1
                    }, 1500, function() {
                        window.setTimeout("a()", 1000)
                    })

                    $("#i1o").click(function() {
                        $("#intro1").hide("fade", function() {
                            $(this).remove()
                            $("#intro2").show("fade")
                        })
                    })

                    $("#i2o").click(function() {
                        $("#intro2").hide("fade", function() {
                            $(this).remove()
                            $("#intro3").show("fade")
                        })
                    })
                })

                function a() {
                    $("#intro").fadeOut(1000, function() {
                        $("#intro").remove()
                        $("#working_div").show("fade")
                    })
                }
            </script>
            <title>Getting Started</title>
        </head>
        <body>
            <div id="container">
                <div id="intro">
                    <span><img src="img/getting_started_intro.jpg"></span>
                </div>
                <div id="working_div">
                    <?php
                    if ($choose_interests == 0) {
                        ?>
                        <div id="intro1">
                            <center>
                                <h1>
                                    Build your network around things you like
                                </h1>
                                <div id="post_types" style="margin-top:20px;display:table;margin-left:-20px;">
                                    <div id="mdl">
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/picture_post.jpg"></div>
                                            <p>
                                                Picture
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/video_post.jpg"></div>
                                            <p>
                                                Video
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/panorama_post.jpg"></div>
                                            <p>
                                                Panorama
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/status_post.jpg"></div>
                                            <p>
                                                Poll
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/status_post.jpg"></div>
                                            <p>
                                                Web Link
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/places_post.jpg"></div>
                                            <p>
                                                Places
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/status_post.jpg"></div>
                                            <p>
                                                Events
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/status_post.jpg"></div>
                                            <p>
                                                Apps
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </center>
                            <input id="i1o" type="button" class="bbutton fr" value="Next" style="width:80px;margin-right:15%;margin-top:20px;">
                        </div>
                        <div id="intro2">
                            <h1>Privacy</h1>
                            <div id='i2m' style='display:table;width:100%;'>
                                <div id="i2l">
                                    <table>
                                        <tr>
                                            <td>
                                                <h2>Privacy Hierarchy</h2>
                                                <p>Privacy of your posts is maintained even after others share them. The creator's rules of privacy are respected more than anyone else's. So you always control where your posts go. For more details, visit <a href="privacy-hierarchy.php">Privacy Hierarchy</a></p>
                                            </td>
                                            <td>
                                                <h2>Tagged</h2>
                                                <p>Control the flow of posts to your friends/public when you are tagged in a publicly shared post. On other end, tagging comes after respecting creator's privacy rules (subject to exceptions).</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h2>Universal Deletion</h2>
                                                <p>Think things have got out of hand with any of your posts? Just remove the one you created and any shared copy of the post shall be deleted, no matter who shared it. Note that this is applicable for non-publicly shared posts only. For more details, visit <a href="privacy-faq.php">Privacy - FAQ</a></p>
                                            </td>
                                            <td>
                                                <h2>Sharable</h2>
                                                <p>Decide whether you want others to be able to share your post or not. This option allows you to restrict the post with just the ones you initially shared.</p>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <div class="fl" style="margin-left:80px;font-family:Arial">Choose default privacy settings for posts you create</div>
                                    <div id="default_privacy" class="fl" style="margin-left:10px;margin-top:-5px;"></div>
                                    <script type="text/javascript">
                                        $("#default_privacy").menuButton({
                                            source: ["Friends", "Public"],
                                            sourceImage: ["img/friendreq.png", "img/friendreq.png"],
                                            width: "90px"
                                        })
                                    </script>
                                </div>
                                <div id="i2r">
                                    <img src="img/privacy.jpg" class="fr uni_shadow_lightest" style='border:1px solid #ccc'>
                                </div>
                            </div>
                            <input id='i2o' type="button" class="bbutton fr" value="Next" style="width:80px;margin-right:160px;">
                        </div>
                    <div id="intro3">
                        <center>
                            <h1>Enough! Now start by creating your first set</h1>
                            <br><br><br>
                            <input id="i3o" type="button" class="bbutton" value="Create" style="padding-left:20px;padding-right:20px;">
                            <script type="text/javascript">
                                $("#i3o").click(function(){
                                   scriptLoader('setCreator', 'setCreator', [["reload"]], 1, this)
                                })
                            </script>
                        </center>
                    </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </body>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
    </html>
    <?php
}
?>