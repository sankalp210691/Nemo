<?php
session_start();
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
    header("location:index.php");
    return;
}
$id = $_SESSION["id"];
$_SESSION["id"] = $id;
//Keep the above part same, everywhere
//include "SpecialFunctions.php";
require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";

$usrcon = new UserController();
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
$user = $usrcon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic","signup_stage"), null, $persistent_connection);
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

if ($uid != $id) {
    $pusercon = new UserController();
    $puser = $pusercon->getByPrimaryKey($uid, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
    $pfirst_name = $puser->getFirst_name();
    $plast_name = $puser->getLast_name();
    $pprofile_pic = $puser->getProfile_pic();
    if ($pprofile_pic == null || strlen($pprofile_pic) == 0) {
        $pprofile_pic = "img/default_profile_pic.jpg";
    }
    $pblur_profile_pic = substr($pprofile_pic, 0, strrpos($pprofile_pic, "/")) . "/blur_" . substr($pprofile_pic, strrpos($pprofile_pic, "/") + 1);
}

$db_connection->mysqli_connect_close();
$self = 0;
if ($uid == $id)
    $self = 1;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <script src="js/jquery-latest.js"></script>
        <script src="js/special.js"></script>
        <?php
        if ($uid != $id) {
            ?>
            <title>Friends - <?php echo $pfirst_name . " " . $plast_name ?></title>
            <?php
        } else {
            ?>
            <title>Friends - <?php echo $first_name . " " . $last_name ?></title>
            <?php
        }
        ?>
        <style>
            #inline_menu_div{
                position:fixed;
                display:table;
                background:white;
                width:100%;
                padding-top:70px;
                border-bottom:1px solid #ccc;

                box-shadow: 0 2px 2px  #eee;
                -o-box-shadow: 0 2px 2px  #eee;
                -webkit-box-shadow: 0 2px 2px  #eee;
                -moz-box-shadow: 0 2px 2px  #eee;
            }

            #inline_menu{
                float:left;
                font-size:30px;
                color:#999;
                padding-left:40px;
            }

            #inline_menu li{
                margin-right:20px;
            }

            #inline_menu li:hover{
                color:black;
                cursor:pointer;
            }

            .cur_tab{
                border-bottom:3px solid #007dff;
                color:black;
            }

            #search_header{
                float:right;
                margin-right:50px;
                margin-bottom:15px;
            }

            .friend_tile{
                height:120px;
                width:360px;
                background:white;
                border:1px solid #ccc;
                border-radius: 3px;
                float:left;
                margin:10px;
                margin-left:0;
                margin-right:20px;
            }

            .friend_tile:hover{
                box-shadow: 0 0 10px #aaa;
                -o-box-shadow: 0 0 10px #aaa;
                -webkit-box-shadow: 0 0 10px #aaa;
                -moz-box-shadow: 0 0 10px #aaa;
            }

            .friend_tile:active{
                box-shadow: 0 0 1px #ccc;
                -o-box-shadow: 0 0 1px #ccc;
                -webkit-box-shadow: 0 0 1px #ccc;
                -moz-box-shadow: 0 0 1px #ccc;
            }

            .ft_pic{
                height:120px;
            }

            .fstat{
                font-family: Arial;
                font-size:12px;
                color:#999;
                font-weight: bold;
                text-shadow: inset 2px 2px 2px #222;
            }

            .fstat:hover{
                color:#007dff;
                cursor:pointer;
            }

            .fin{
                width:30px;
                height:30px;
                border-radius:2px;
                margin-right:5px;
                margin-top:4px;
            }

            .fstatus{
                display:table;
                font-size:20px;
                padding:5px;
                width: 80px;
                margin-left:2px;
                margin-top:5px;
                -webkit-box-shadow: 0 0 1px #444;
                -moz-box-shadow:  0 0 1px #444;
                box-shadow:   0 0 1px #444;
                -o-box-shadow:  0 0 1px #444;
            }

            .fstatus:hover{
                cursor:pointer;
                -webkit-box-shadow: 0 0 1px #000;
                -moz-box-shadow:  0 0 1px #000;
                box-shadow:   0 0 1px #000;
                -o-box-shadow:  0 0 1px #000;
            }

            #control_panel{
                position:fixed;
                width:27%;
                height:100%;
                border-right:1px solid #ccc;
                margin-left:-40px;
                margin-top:-10px;
                background:white;
                -webkit-box-shadow: 10px 0 10px -5px #ccc;
                -moz-box-shadow:  10px 0 10px -5px #ccc;
                box-shadow:   10px 0 10px -5px #ccc;
                -o-box-shadow:  10px 0 10px -5px #ccc;
            }

            #friend_panel{
                margin-left:26%;
                width:76%;
                display:table;
                height:100%;
            }

            #top_tab{
                display:table;
                width:100%;
                height:20px;
            }

            #top_tab li{
                display:table-cell;
                padding:12px;
                text-align:center;
                border:1px solid #ccc;
                border-right:0;
                border-radius:2px;
                background:#f9f9f9;
                font-size:20px;
                cursor:pointer;
            }

            #top_tab .ctb{
                background:white;
                border-bottom:0;
            }

            .group_label{
                color:#444;
                font-weight:bold;
            }

            .square{
                display:table;
                overflow:hidden;
                border:1px solid #ccc;
                border-radius:3px;
                margin:5px;
                margin-left:0;
                margin-right:10px;
                cursor:pointer;
            }

            .square img{
                width:100%;
                height:100%;
                cursor:pointer;
            }

            .square_user_name{
                width:100%;
                color:#444;
                font-weight:bold;
                background:white;
                padding-bottom:7px;
                padding-top:7px;
            }

            .group_option{
                display:table;
                width:100%;
                padding-top:5px;
                padding-bottom:5px;
                border-bottom:1px solid #ccc;
                height:40px;
            }

            .group_option:hover{
                color:white;
                background:#007dff;
                cursor:pointer;
            }

            .group_name_div{
                vertical-align:center;
                margin-left:20px;
                font-size:25px;
            }
        </style>
        <script src="js/sigma.js"></script>
        <?php if ($self == 1) { ?>
            <script src="js/friends.js"></script>
        <?php } ?>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            var cf_list = [], group_list = []
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })
            var friends, orignal_list

            function start() {
                $("#fa").children("div").css({
                    "background": "black",
                    "width": screen.width,
                    "margin-left": "-40px"
                })
                if (friends == null)
                    getFriends("", user_id, "0", "9999999999", 0)
                else
                    init()
            }

            function init() {
                // Instanciate sigma.js and customize it :
                var sigInst = sigma.init(document.getElementById('gd')).drawingProperties({
                    defaultLabelColor: '#fff'
                })


                var nodes = [], i
                sigInst.addNode(user_name, {
                    'x': Math.random(),
                    'y': Math.random(),
                    'size': 20,
                    'color': '#14b30e'
                })
                for (i = 0; i < friends.length; i++) {
                    sigInst.addNode(friends[i].name, {
                        'x': Math.random(),
                        'y': Math.random(),
                        'size': 10,
                        'color': '#007dff'
                    })
                    sigInst.addEdge(i, user_name, friends[i].name)
                }

                var greyColor = '#666';
                sigInst.bind('overnodes', function(event) {
                    var nodes = event.content;
                    var neighbors = {};
                    sigInst.iterEdges(function(e) {
                        if (nodes.indexOf(e.source) < 0 && nodes.indexOf(e.target) < 0) {
                            if (!e.attr['grey']) {
                                e.attr['true_color'] = e.color;
                                e.color = greyColor;
                                e.attr['grey'] = 1;
                            }
                        } else {
                            e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
                            e.attr['grey'] = 0;

                            neighbors[e.source] = 1;
                            neighbors[e.target] = 1;
                        }
                    }).iterNodes(function(n) {
                        if (!neighbors[n.id]) {
                            if (!n.attr['grey']) {
                                n.attr['true_color'] = n.color;
                                n.color = greyColor;
                                n.attr['grey'] = 1;
                            }
                        } else {
                            n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
                            n.attr['grey'] = 0;
                        }
                    }).draw(2, 2, 2);
                }).bind('outnodes', function() {
                    sigInst.iterEdges(function(e) {
                        e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
                        e.attr['grey'] = 0;
                    }).iterNodes(function(n) {
                        n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
                        n.attr['grey'] = 0;
                    }).draw(2, 2, 2);
                })
                sigInst.draw()
            }
        </script>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="inline_menu_div">
                <ul id="inline_menu" class="linear_list">
                    <li class="cur_tab" id="cl">Contact List</li>
                    <?php if ($self == 1) { ?>
                        <li id="mg">Manage Groups</li>
                    <?php } ?>
                    <li id="sg">Social Graph</li>
                </ul>
                <div id="search_header" >
                    <input type="search" placeholder="Search Friends" style="width:250px;height:35px;" id="search_friend">
                </div>
            </div>
            <div id="fa" style="padding-left:40px;padding-top:130px;width:100%;"></div>
            <script>
                var fa = $("#fa")
                fa.width("95%")
                function getFriends(e, user_id, start, limit, show) {
                    var center = $("<center id='post_loader'>")
                    $.ajax({
                        url: "manager/FriendManager.php",
                        cache: false,
                        type: "GET",
                        dataType: "json",
                        data: "req=get_friends&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                        beforeSend: function() {
                            if (show == 1) {
                                var feed_loader = $("<img style='width:50px;margin-top:80px'>")
                                feed_loader.attr("src", "img/massive_ajax_loader.gif")
                                center.html(feed_loader)
                                e.append(center)
                            }
                        },
                        success: function(data) {
                            friends = data
                            if (show == 1) {
                                center.remove()
                                var i, data_length = data.length
                                for (i = 0; i < data_length; i++) {
                                    var tile = $("<div uid='" + data[i].uid + "'>")
                                    tile.addClass("friend_tile")
                                    e.append(tile)

                                    var pic = $("<img class='fl'>")
                                    pic.attr("src", data[i].profile_pic)
                                    pic.addClass("ft_pic")
                                    tile.html(pic)

                                    var finfo = $("<div class='fl ml1' style='margin-top:5px;font-size:25px;color:#777;'>")
                                    finfo.html("<a href='profile.php?id=" + data[i].uid + "' class='black_link'>" + data[i].name + "</a>")

                                    finfo.append("<br>")
                                    var nset = $("<span class='fstat'>")
                                    nset.html(data[i].set_count + " sets")
                                    finfo.append(nset)
                                    finfo.append("&nbsp;")
                                    if (data[i].uid != user_id) {
                                        var mfrnd = $("<span class='fstat'>")
                                        var mutual_friend_count = data[i].mutual_friend_count
                                        if (mutual_friend_count != 0) {
                                            mfrnd.html(mutual_friend_count + " mutual friends")
                                            finfo.append(mfrnd)
                                            mfrnd.click(function(e) {
                                                scriptLoader("mfLib", "mfLib", [[user_id, $(this).parent().parent().attr("uid")]], 0)
                                                e.stopPropagation()
                                            })
                                        }
                                    }

<?php
if ($self == 1) {
    ?>
                                        var fstatus = $("<div>")
                                        fstatus.menuButton({
                                            source: ["Friends", "Unfriend", "Block"],
                                            sourceImage: ["img/friendreq.png", "img/friendreq.png", "img/friendreq.png"],
                                            width: "90px"
                                        })
                                        finfo.append(fstatus)
<?php } ?>

                                    tile.append(finfo)
                                    nset.click(function(e) {
                                        scriptLoader("setsLib", "setsLib", [[$(this).parent().parent().attr("uid"), $(this).parent().children("a").html()]], 0)
                                        e.stopPropagation()
                                    })


                                    tile.click(function() {
                                        document.location.href = "profile.php?id=" + $(this).attr("uid")
                                    })
                                }
                                orignal_list = e.html()
                            } else {
                                init()
                            }
                        }, error: function(jqXHR, textStatus, errorThrown) {
                            alertBox("Some error occured. Please try again later.")
                        }
                    })
                }
                $(document).ready(function() {
                    $("#inline_menu_div").css("min-width", screen.width)
                    getFriends(fa, user_id, 0, 20, 1)
                    $("#search_friend").smartSearch({
                        dataSourceURL: "manager/FriendManager.php",
                        dataSourceParameters: "req=search&uid=<?php echo $uid ?>&user_id=<?php echo $id ?>&format=friend_list",
                        autoComplete: false,
                        callback: function(data) {
                            if (data == -1) {
                                fa.html(orignal_list)
                            } else if (data.length == 0) {
                                fa.html("")
                            } else if (data.length > 0) {
                                fa.html("")
                                var i, data_length = data.length
                                for (i = 0; i < data_length; i++) {
                                    var tile = $("<div uid='" + data[i].uid + "'>")
                                    tile.addClass("friend_tile")
                                    fa.append(tile)

                                    var pic = $("<img class='fl'>")
                                    pic.attr("src", data[i].profile_pic)
                                    pic.addClass("ft_pic")
                                    tile.html(pic)

                                    var finfo = $("<div class='fl ml1' style='margin-top:5px;font-size:25px;color:#777;'>")
                                    finfo.html("<a href='profile.php?id=" + data[i].uid + "' class='black_link'>" + data[i].name + "</a>")

                                    finfo.append("<br>")
                                    var nset = $("<span class='fstat'>")
                                    nset.html(data[i].set_count + " sets")
                                    finfo.append(nset)
                                    finfo.append("&nbsp;")
                                    if (data[i].uid != user_id) {
                                        var mfrnd = $("<span class='fstat'>")
                                        var mutual_friend_count = data[i].mutual_friend_count
                                        if (mutual_friend_count != 0) {
                                            mfrnd.html(mutual_friend_count + " mutual friends")
                                            finfo.append(mfrnd)
                                            mfrnd.click(function(e) {
                                                scriptLoader("mfLib", "mfLib", [[user_id, $(this).parent().parent().attr("uid")]], 0)
                                                e.stopPropagation()
                                            })
                                        }
                                    }

<?php
if ($self == 1) {
    ?>
                                        var fstatus = $("<div>")
                                        fstatus.menuButton({
                                            source: ["Friends", "Unfriend", "Block"],
                                            sourceImage: ["img/friendreq.png", "img/friendreq.png", "img/friendreq.png"],
                                            width: "90px"
                                        })
                                        finfo.append(fstatus)
<?php } ?>

                                    tile.append(finfo)
                                    nset.click(function(e) {
                                        scriptLoader("setsLib", "setsLib", [[$(this).parent().parent().attr("uid"), $(this).parent().children("a").html()]], 0)
                                        e.stopPropagation()
                                    })


                                    tile.click(function() {
                                        document.location.href = "profile.php?id=" + $(this).attr("uid")
                                    })
                                }
                            }
                        }
                    })

                    $("#inline_menu li").click(function() {
                        var li = $(this)
                        if (li.hasClass("cur_tab")) {
                        } else {
                            var liid = li.attr("id")
                            if (liid == "cl") {
                                fa.html(orignal_list)
                                $(".cur_tab").removeClass("cur_tab")
                                li.addClass("cur_tab")
                            } else if (liid == "sg") {
                                var graph_div = $("<div id='gd' style='width:100%;'>")
                                graph_div.height(0.7 * screen.height)
                                fa.html(graph_div)
                                $(".cur_tab").removeClass("cur_tab")
                                li.addClass("cur_tab")
                                //                                scriptLoader("sigma", "", [[]], 1, "")
                                //                                init()
                                start()
                            }
<?php if ($self == 1) { ?>
                                else if (liid == "mg") {
                                    $(".cur_tab").removeClass("cur_tab")
                                    li.addClass("cur_tab")
                                    manageGroupPage(fa)
                                }
<?php } ?>
                        }
                    })
                }
                )
            </script>
        </div>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <script src="js/jquery-ui.js"></script>
    </body>
</html>