<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("location:index.php");
}
$id = $_SESSION['id'];
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
include "req/SpecialFunctions.php";

$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();

$user = new User();
$usercon = new UserController();

$user = $usercon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage"), null, $persistent_connection);
$first_name = $user->getFirst_name();
$last_name = $user->getLast_name();
$profile_pic = $user->getProfile_pic();
$blur_profile_pic = getBlurPicAddress($profile_pic);
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
            <link href="css/easydropdown.css" rel="stylesheet">
            <link href="css/perfect-scrollbar.css" rel="stylesheet">
            <link href="css/getting_started.css" rel="stylesheet">
            <script type="text/javascript" src="js/jquery-latest.js"></script>
            <script type="text/javascript" src="js/special.js"></script>
            <script type="text/javascript">
                var user_id = "<?php echo $id ?>"
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
                    $("#intro").animate({
                        "margin-left": ($(window).width() - $("#intro span img").width()) / 2,
                        "opacity": 1
                    }, 15, function() {
                        window.setTimeout("a()", 10)
                    })

                    $("#i1o").click(function() {
                        $("#intro1").hide("fade", function() {
                            $(this).remove()
                            $("#intro2").show("fade")
                            $("#search").focus()
                        })
                    })

                    $("#i2o").click(function() {
                        $("#intro2").hide("fade", function() {
                            $(this).remove()
                            $("#intro3").show("fade")
                        })
                    })
                    var activesearch = 0, currentsearch;
                    $("#search").smartSearch({
                        dataSourceURL: "searchManager.php",
                        dataSourceParameters: "req=gs&type=ap",
                        textParameter: "q",
                        callback: function(data) {
                            currentsearch = $.trim($("#search").val())
                            if (activesearch == 0) {
                                $("#sdiv").animate({
                                    "margin-top": 0}, {
                                    duration: 700,
                                    complete: function() {
                                        $("#resultdiv,#menudiv").fadeIn("700", function() {
                                            $("#container").css("background", "#f6f6f6")
                                        })
                                        var data_length = data.length, i
                                        for (i = 0; i < data_length; i++) {
                                            var post = new PostTile(data[i], [0, 0, 1, 0])
                                            post.arrangeTile($("#postarena"), 4, "append", "incCount")
                                        }
                                    }
                                })
                                $("#sdiv center h1").fadeOut("500", "linear", function() {
                                    $(this).remove()
                                })
                                activesearch = 1;
                            } else {
                                $("#postarena").remove()
                                $("#post_menu li").removeClass("ctb")
                                $("#ap").addClass("ctb")
                                var postarena = $("<div id='postarena'>")
                                $("#resultdiv").append(postarena)
                                postarena.width($("#resultdiv").width() - 20)
                                var data_length = data.length, i
                                for (i = 0; i < data_length; i++) {
                                    var post = new PostTile(data[i], [0, 0, 1, 0])
                                    post.arrangeTile($("#postarena"), 4, "append", "incCount")
                                }
                            }
                        }
                    });

                    var localbuffer = []
                    localbuffer["ap"] = []
                    localbuffer["php"] = []
                    localbuffer["vdp"] = []
                    localbuffer["wlp"] = []
                    localbuffer["pnp"] = []
                    localbuffer["pop"] = []
                    localbuffer["evp"] = []
                    localbuffer["app"] = []
                    $("#post_menu li").click(function() {
                        if ($(this).hasClass("ctb") || $(this).hasClass("s")) {
                        } else {
                            var newid = $(this).attr("id");
                            $("#post_menu li").removeClass("ctb")
                            $(this).addClass("ctb")
                            if (currentsearch in localbuffer[newid]) {
                                $("#postarena").remove()
                                var postarena = $("<div id='postarena'>")
                                $("#resultdiv").append(postarena)
                                postarena.width($("#resultdiv").width() - 20)
                                var data_length = localbuffer[newid][currentsearch].length, i
                                for (i = 0; i < data_length; i++) {
                                    var post = new PostTile(localbuffer[i], [0, 0, 1, 0])
                                    post.arrangeTile($("#postarena"), 4, "append", "incCount")
                                }
                            } else {
                                $.ajax({
                                    url: "searchManager.php",
                                    dataType: "json",
                                    type: "get",
                                    data: "req=gs&type=" + newid + "&sugg=0&q=" + currentsearch,
                                    success: function(data) {
                                        localbuffer[newid][currentsearch] = data;
                                        $("#postarena").remove();
                                        var postarena = $("<div id='postarena'>");
                                        $("#resultdiv").append(postarena);
                                        postarena.width($("#resultdiv").width() - 20);
                                        var data_length = data.length, i;
                                        for (i = 0; i < data_length; i++) {
                                            var post = new PostTile(data[i], [0, 0, 1, 0])
                                            post.arrangeTile($("#postarena"), 4, "append", "incCount")
                                        }
                                    }, error: function(e, f) {
                                        alert(JSON.stringify(e) + " " + JSON.stringify(f))
                                        alertBox()
                                    }
                                })
                            }
                        }
                    })
                });

                var scnt = 0;
                function incCount() {
                    scnt++;
                    $("#s" + (6 - scnt)).css("background", "#007dff");
                    if (scnt == 5) {
                        $.ajax({
                            url: "manager/UserManager.php",
                            type: "post",
                            data: "req=ss1over&user_id=" + user_id,
                            success: function() {
                                location.reload()
                            }, error: function(e, f) {
                                alertBox()
                            }
                        })
                    }
                }

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
                                                Photo
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
                                            <div><img src="img/link_post.jpg"></div>
                                            <p>
                                                Web Links
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/poll_post.jpg"></div>
                                            <p>
                                                Poll
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/places_post.jpg"></div>
                                            <p>
                                                Places
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/event_post.jpg"></div>
                                            <p>
                                                Events
                                            </p>
                                        </div>
                                        <div class="pt uni_shadow_lightest">
                                            <div><img src="img/app_post.jpg"></div>
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
                            <div id="sdiv">
                                <center style="width:100%;">
                                    <h1>Search</h1><br>
                                    <input type="search" id="search" placeholder="Search what you find interesting" x-webkit-speech>
                                </center>
                                <br>
                                <div id="menudiv">
                                    <ul id="post_menu" class="hori_menu">
                                        <li class="ctb" id="ap">All posts</li>
                                        <li id="php">Photos</li>
                                        <li id="vdp">Videos</li>
                                        <li id="wlp">Web Links</li>
                                        <li id="plp">Places</li>
                                        <li id="pnp">Panorama</li>
                                        <li id="pop">Poll</li>
                                        <li id="evp">Events</li>
                                        <li id="app">Apps</li>
                                        <li class='s'><div id='s1' class='circle'></div></li>
                                        <li class='s'><div id='s2' class='circle' style='margin-right:10px;'></div></li>
                                        <li class='s'><div id='s3' class='circle' style='margin-right:10px;'></div></li>
                                        <li class='s'><div id='s4' class='circle' style='margin-right:10px;'></div></li>
                                        <li class='s'><div id='s5' class='circle' style='margin-right:10px;'></div></li>
                                    </ul>
                                </div>
                            </div>
                            <div id="resultdiv">
                                <div id="postarena"></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </body>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>
    </html>
    <?php
}
?>