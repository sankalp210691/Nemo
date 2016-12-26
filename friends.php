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
//Keep the above part same, everywhere
include "req/SpecialFunctions.php";
require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";
include "model/GroupsModel.php";
include "controller/GroupsController.php";
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
$blur_profile_pic = getBlurPicAddress($profile_pic);

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
$ref = 1;
if (isset($_GET["ref"])) {
    $ref = $_GET["ref"];
    if ($ref != 2 && $ref != 3) {
        $ref = 1;
    }
    if ($ref == 2 && $self == 0) {
        $ref = 1;
    }
}
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

            #inline_menu li a{
                color:  #999;
                text-decoration: none;
            }

            #inline_menu li:hover,#inline_menu li a:hover{
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
            <?php if ($ref == 1) { ?>
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
                    margin-left:10px;
                    color:#999;
                    font-weight: bold;
                    text-shadow: inset 2px 2px 2px #222;
                }

                .fstat:first-of-type{
                    margin-left:0;
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
            <?php } ?>
            <?php if ($ref == 2) { ?>
                .gfriend_block{
                    display:table;
                    padding:10px 30px;
                    width:100%;
                    width:calc(100% - 3em);
                    border-bottom:1px solid #ccc;
                    background:white;
                }

                .circle{
                    float:left;
                    width:40px;
                    height:40px;
                    border-radius:50%;
                    border:3px solid white;
                    -webkit-box-shadow: 0 0 5px #777;
                    -moz-box-shadow:  0 0 5px #777;
                    box-shadow:   0 0 5px #777;
                    -o-box-shadow:  0 0 5px #777;
                }

                .cppic{
                    width:40px;
                    height:40px;
                    border-radius:50%;
                }

                .fname{
                    float:left;
                    margin:13px 20px;
                    font-size:20px;
                }

                .cr{
                    float:right;
                    color:#999;
                    margin:10px;
                    font-size:20px;
                    font-weight:bold;
                }

                .cr:hover{
                    color:#007dff;
                    cursor:pointer;
                }

                #group_settings_form{
                    margin:20px;
                    font-family:Calibri;
                    font-size:15px;
                }

                .slabel{
                    font-weight:bold;
                }

                #friend_div{
                    border-right:1px solid #ccc;
                    float:left;
                    width:70%;
                    height:100%;
                    overflow-x:hidden;
                }

                .groupbox{
                    float:left;
                    background:white;
                    border:1px solid #ccc;
                    -webkit-box-shadow: 0 0 5px #ccc;
                    -moz-box-shadow:  0 0 5px #ccc;
                    box-shadow:   0 0 5px #ccc;
                    -o-box-shadow:  0 0 5px #ccc;
                    padding:10px 20px;
                    width:225px;
                    height:205px;
                    margin:10px 20px;
                    margin-left:0;
                }

                .groupbox h1{
                    font-weight:lighter;
                }

                .groupbox div{
                    margin-top:10px;
                    border-top:1px solid #000;
                }

                #cnb{
                    margin-top:50px;
                    width:130px;
                }

                .grpfrnd{
                    position:relative;
                    float:left;
                    width:100px;
                    height:100px;
                    background-size: cover;
                    background-repeat: no-repeat;
                    margin-right:5px;
                    margin-bottom:5px;
                }
            <?php } ?>
        </style>
        <?php if ($ref == 3) { ?>
            <script src="js/sigma.js"></script>
        <?php } ?>
        <?php if ($self == 1) { ?>
            <script src="js/friends.js"></script>
        <?php } ?>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            var categories = <?php echo json_encode($_SESSION["categories"]) ?>;
            var cf_list = [], group_list = []
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })
            var friends, orignal_list
<?php
if ($ref == 2) {
    ?>
                var cur_stack = [], cur_stack_length = 0


                function addToList(e) {
                    e = $(e)
                    var ele = [e, e.attr("id"), e.find("p").html(), e.find("img").attr("src")]
                    var index = (e.attr("id") % 100) - 1
                    if (cur_stack[index] == null) {
                        cur_stack[index] = ele;
                        cur_stack_length++;
                        e.css({
                            "background": "#007dff",
                            "color": "white"
                        })
                    }
                }

                function showGroup(id, name, piclist) {
                    location.reload();
                }

                function openGroupSettings(group) {
                    var groupAdminBox = new Box("group_admin_box", "30", "64")
                    groupAdminBox.heading = "Group Settings"
                    groupAdminBox.createOverlay(0)
                    var main_body = groupAdminBox.createBox()

                    //                    var friend_div = $("<div id='friend_div'>")
                    //                    main_body.html(friend_div)
                    if (group == null) {
                        //                        createList(null, friend_div)
                    } else {
                        $.ajax({
                            url: "manager/GroupManager.php",
                            type: "get",
                            dataTye: "json",
                            data: "req=get_group_friend&group_id=" + group.id + "&cuser_id=<?php echo $id ?>",
                            beforeSend: function() {
                                //                                friend_div.html("<center><img src='img/ajax_loader_horizontal.gif' style='margin-top:20px;'></center>")
                            },
                            success: function(data) {
                                //                                createList(data, friend_div)
                            }, error: function(e, f) {
                                alertBox()
                            }
                        })
                    }

                    var selected_friends = []
                    function showFriends(friend_div, gfriend_div, group_name, group_type, is_block, is_private_sharing, is_suggest) {
                        var friend_search = $("<input type='text' placeholder='Search friends' style='margin:20px;margin-bottom:0;'>");
                        friend_div.html(friend_search);
                        var friend_area = $("<div style='width:100%;padding:20px;width:calc(100% - 2.5em);height:calc(100% - 8.25em);overflow-y:scroll;'>");
                        friend_div.append(friend_area);
                        var save_div = $("<div style='background:white;width:100%;clear:both;padding:20px 20px;padding-top:0;width:calc(100% - 2.5em);position:absolute;bottom:0;'>")
                        var save = $("<input id='sgroup' type='button' class='bbutton' value='Save' style='width:80px;'>");
                        save_div.html(save);
                        friend_div.append(save_div);
                        var friends_length = friends.length, i, fdiv = [];
                        for (i = 0; i < friends_length; i++) {
                            fdiv[i] = $("<div id='f" + friends[i].id + "' data-background='" + friends[i].profile_pic + "' class='grpfrnd cp' style=\"background-image:url('" + friends[i].profile_pic + "')\">");
                            friend_area.append(fdiv[i]);
                            fdiv[i].html("<div class='grad'></div>");
                            fdiv[i].append("<p style='position:absolute;bottom:5px;left:5px;color:white;font-size:15px;'>" + friends[i].name + "</p>");
                            fdiv[i].click(function() {
                                //add to list
                                selected_friends[selected_friends.length] = $(this).attr("id").substr(1);
                                //
                                var fblock = $("<div id='g" + $(this).attr("id") + "' class='gfriend_block'>")
                                var circle = $("<div class='circle'>")
                                var ppic = $("<img id='p" + $(this).attr("id") + "' src='" + $(this).attr("data-background") + "' class='cppic'>")
                                circle.html(ppic)
                                fblock.html(circle)
                                gfriend_div.append(fblock)
                                var p = $("<p class='fname'>")
                                p.html($(this).children("p").html())
                                fblock.append(p)
                                var cr = $("<span class='cr'>")
                                cr.html("X")
                                fblock.append(cr);
                                $(this).fadeOut("500");
                                $(".cr").click(function() {
                                    var block = $(this).parent(), i;
                                    //delete from list
                                    for (i = 0; i < selected_friends.length; i++) {
                                        if (selected_friends[i] == block.attr("id").substr(2)) {
                                            selected_friends.splice(i, 1);
                                            break;
                                        }
                                    }
                                    //
                                    $("#" + block.attr("id").substr(1)).fadeIn("500");
                                    block.rotate({
                                        angle: 0,
                                        animateTo: 70,
                                    });
                                    block.animate({
                                        "margin-top": "+=100",
                                        "opacity": "0"
                                    }, function() {
                                        block.remove()
                                    })
                                    block.addClass("gen_hover_shadow");
                                })
                            })
                        }
                        save.click(function() {
                            $.ajax({
                                url: "manager/FriendManager.php",
                                type: "get",
                                data: "req=create_group&user_id=" + user_id + "&group_name=" + encodeURIComponent(group_name) + "&group_type=" + group_type + "&is_block=" + is_block + "&is_private_sharing=" + is_private_sharing + "&is_suggest=" + is_suggest + "&list=" + selected_friends,
                                beforeSend: function() {
                                    $("#sgroup").replaceWith("<img id='loader' src='img/ajax_loader_horizontal.gif'>");
                                }, success: function(group_id) {
                                    var picarray = [], selected_friends_size = selected_friends.length;
                                    if (selected_friends_size > 4)
                                        selected_friends_size = 4;
                                    for (i = 0; i < selected_friends_size; i++) {
                                        picarray[i] = $("#pf" + selected_friends[i]).attr("id")
                                    }
                                    showGroup(group_id, group_name, picarray);
                                    groupAdminBox.closeBox();
                                }, error: function(e, f) {
                                    $("#loader").replaceWith("<input id='sgroup' type='button' class='bbutton' value='Save' style='width:80px;'>");
                                    alertBox()
                                }
                            })
                        })
                    }

                    var form = $("<form id='group_settings_form'>")
                    main_body.append(form)
                    //GROUP NAME STARTS                
                    var group_name_label = $("<label for='group_name' class='slabel'>")
                    group_name_label.html("Group Name")
                    var group_name = $("<input type='text' placeholder='Name of this group' id='group_name' style='width:300px;'>")
                    form.html(group_name_label)
                    form.append(group_name)
                    form.append("<br>")
                    form.append("<br>")
                    //GROUP TYPE STARTS
                    var group_type_label = $("<label for='group_type_label' class='slabel'>")
                    group_type_label.html("Group Type")
                    form.append(group_type_label)
                    form.append("<br>")

                    var table = $("<table style='width:300px;'>")
                    form.append(table)
                    var tr1 = $("<tr>")
                    table.html(tr1)

                    var fmtd = $("<td>")
                    var family_label = $("<label for='family_radio'>")
                    family_label.html("Family")
                    var family_radio = $("<input type='radio' id='family_radio' name='group_type' value='friends'>")
                    fmtd.html(family_radio)
                    fmtd.append(family_label)
                    tr1.html(fmtd)

                    var frtd = $("<td>")
                    var friends_label = $("<label for='friends_radio'>")
                    friends_label.html("Friends")
                    var friends_radio = $("<input type='radio' id='friends_radio' name='group_type' value='friends'>")
                    frtd.append(friends_radio)
                    frtd.append(friends_label)
                    tr1.append(frtd)

                    var oftd = $("<td>")
                    var office_label = $("<label for='office_radio'>")
                    office_label.html("Office")
                    var office_radio = $("<input type='radio' id='office_radio' name='group_type' value='office'>")
                    oftd.append(office_radio)
                    oftd.append(office_label)
                    tr1.append(oftd)

                    var tr2 = $("<tr>")
                    table.append(tr2)

                    var sctd = $("<td>")
                    var school_label = $("<label for='school_radio'>")
                    school_label.html("School/University")
                    var school_radio = $("<input type='radio' id='school_radio' name='group_type' value='school'>")
                    sctd.append(school_radio)
                    sctd.append(school_label)
                    tr2.append(sctd)

                    var intd = $("<td>")
                    var interest_label = $("<label for='interest_radio'>")
                    interest_label.html("Interest")
                    var interest_radio = $("<input type='radio' id='interest_radio' name='group_type' value='interest'>")
                    intd.append(interest_radio)
                    intd.append(interest_label)
                    tr2.append(intd)

                    var ottd = $("<td>")
                    var other_label = $("<label for='other_radio'>")
                    other_label.html("Other")
                    var other_radio = $("<input type='radio' id='other_radio' name='group_type' value='other'>")
                    ottd.append(other_radio)
                    ottd.append(other_label)
                    tr2.append(ottd)
                    form.append("<br>")
                    //EXTRA OPTIONS START
                    var block_label = $("<label for='block' class='slabel'>")
                    block_label.html("Block friends in this group")
                    var block = $("<input type='checkbox' id='block' name='block'>")
                    form.append(block)
                    form.append(block_label)
                    form.append("<br>")

                    var sharing_label = $("<label for='sharing' class='slabel'>")
                    sharing_label.html("Don't share private posts unless I tag")
                    var sharing = $("<input type='checkbox' id='sharing' name='sharing'>")
                    form.append(sharing)
                    form.append(sharing_label)
                    form.append("<br>")

                    var suggest_label = $("<label for='suggest' class='slabel'>")
                    suggest_label.html("Suggest friends in this group")
                    var suggest = $("<input type='checkbox' id='suggest' name='suggest' checked>")
                    form.append(suggest)
                    form.append(suggest_label)
                    form.append("<br>")
                    form.append("<br>")

                    var post_frequency_label = $("<label for='range' class='slabel'>")
                    post_frequency_label.html("Post frequency")
                    var range_div = $("<div id='range_div' style='width:300px;margin-top:10px;'>")
                    var range = $("<input type='range' value='50' step='10' min='0' max='100' id='range' name='range' style='width:300px;height:10px;' data-rangeslider>")
                    var rtable = $("<table style='width:300px;margin-top:5px;'>")
                    var rtr = $("<tr>")
                    rtable.html(rtr)
                    var rtdl = $("<td style='width:100px'>")
                    rtr.html(rtdl)
                    rtdl.html("Less")
                    var rtda = $("<td style='width:100px'>")
                    rtr.append(rtda)
                    rtda.html("<center>Automatic</center>")
                    var rtdh = $("<td style='width:100px;text-align:right'>")
                    rtr.append(rtdh)
                    rtdh.html("High")
                    range_div.html(range)
                    form.append(post_frequency_label)
                    form.append("<br>");
                    form.append(range_div);
                    range.rangeslider({polyfill: false});
                    range_div.append(rtable)
                    form.append("<br>");

                    var del = $("<img src='img/delete.png' style='height:20px;margin-left:5px;float:left;border-right:1px solid #ccc'>")
                    var del_button = $("<button class='wbutton' style='width:150px;'>")
                    del_button.html(del)
                    del_button.append("Delete this group")
                    form.append(del_button)
                    form.append("<br><br>");

                    var next = $("<input type='button' class='bbutton' style='width:80px;margin-right:20px;' value='Next'>");
                    next.click(function() {
                        var group_name = $.trim($("#group_name").val());
                        var group_type = $.trim($("input[name='group_type']").val());
                        var is_block = 0, is_private_sharing = 0, is_suggest = 0;
                        if ($("input#block").is(":checked"))
                            is_block = 1;
                        if ($("input#sharing").is(":checked"))
                            is_private_sharing = 1;
                        if ($("input#suggest").is(":checked"))
                            is_suggest = 1;
                        if (group_name.length == 0) {
                            $("#group_name").addClass("errorInput");
                            return;
                        }
                        if (group_type.length == 0) {
                            $("input[name='group_type']").addClass("errorInput");
                            return;
                        }
                        $("#group_name").removeClass("errorInput");
                        $("input[name='group_type']").removeClass("errorInput");
                        $("#group_admin_box").animate({
                            "width": "70%",
                            "margin-left": "15%"
                        }, "1000", function() {
                            var friend_div = $("<div id='friend_div' style='float:left;position:relative;overflow:hidden;height:100%'>");
                            var gfriend_div = $("<div id='gfriend_div' style='width:30%;width:calc(30% - .0625em);float:left;height:100%;overflow-y:auto;overflow-x:hidden;background:#f7f7f7'>")
                            main_body.html(friend_div);
                            main_body.append(gfriend_div);
                            showFriends(friend_div, gfriend_div, group_name, group_type, is_block, is_private_sharing, is_suggest)
                        });
                        $("#group_settings_form").fadeOut("1000");

                    })
                    var cancel = $("<input type='button' class='wbutton' style='width:80px' value='Cancel'>")
                    cancel.click(function() {
                        groupAdminBox.closeBox()
                    })
                    form.append(next)
                    form.append(cancel)
                }
<?php } ?>
<?php
if ($ref == 3) {
    ?>
                function start() {
                    on
                    $("#fa").children("div").css({
                        "background": "black",
                        "width": screen.width,
                        "margin-left": "-40px"
                    })
                    getFriends($("#fa"), "<?php echo $uid ?>", "0", "9999999999")
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
<?php } ?>
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
                    <?php
                    if ($ref == 1) {
                        ?>
                        <li class="cur_tab" id="cl">Contact List</li>
                    <?php } else { ?>
                        <li id="cl"><a href="friends.php?id=<?php echo $uid ?>&ref=1">Contact List</a></li>
                    <?php } ?>
                    <?php if ($self == 1) { ?>
                        <?php
                        if ($ref == 2) {
                            ?>
                            <li class="cur_tab" id="mg">Manage Groups</li>
                        <?php } else { ?>
                            <li id="mg"><a href="friends.php?id=<?php echo $uid ?>&ref=2">Manage Groups</a></li>
                        <?php } ?>
                    <?php } ?>
                    <?php
                    if ($ref == 3) {
                        ?>
                        <li class="cur_tab" id="sg">Social Graph</li>
                    <?php } else { ?>
                        <li id="sg"><a href="friends.php?id=<?php echo $uid ?>&ref=3">Social Graph</a></li>
                    <?php } ?>
                </ul>
                <?php
                if ($ref == 1 || $ref == 3) {
                    ?>
                    <div id="search_header" >
                        <input type="search" placeholder="Search Friends" style="width:250px;height:35px;" id="search_friend">
                    </div>
                <?php } else { ?>
                    <div id="search_header" style="width:1px;height:35px;"></div>
                <?php } ?>
            </div>
            <div id="fa" style="padding-left:40px;padding-top:130px;width:100%;">
                <?php if ($ref == 3) { ?>
                    <div id='gd' style='width:100%;'></div>
                <?php } ?>
                <?php
                if ($ref == 2) {
                    ?>
                    <div id="newgroupbox" class="groupbox">
                        <center><h1>New Group</h1></center>
                        <div>
                            <center><input id="cnb" type="button" class="bbutton" value="Create new group" onclick="openGroupSettings(null)"></center>
                        </div>
                    </div>
                    <?php
                    $groups = getUserGroups($uid, 1);
                    $groups_size = sizeof($groups);
                    for ($i = 0; $i < $groups_size; $i++) {
                        ?>
                        <div id="gr<?php echo $groups[$i]["id"] ?>" class="groupbox">
                            <center><h1><?php echo $groups[$i]["name"] ?></h1></center>
                            <div>
                                <table>
                                    <tr>
                                        <td><?php echo $groups[$i]["profile_pic"][0] ?></td>
                                        <td><?php echo $groups[$i]["profile_pic"][1] ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo $groups[$i]["profile_pic"][2] ?></td>
                                        <td><?php echo $groups[$i]["profile_pic"][3] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                <?php } ?>
            </div>
            <script>
                var fa = $("#fa")
                fa.width("95%")

                function getFriends(e, user_id, start, limit) {
                    var center = $("<center id='post_loader'>")
                    $.ajax({
                        url: "manager/FriendManager.php",
                        cache: false,
                        type: "GET",
                        dataType: "json",
                        data: "req=get_friends&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                        beforeSend: function() {
                            var feed_loader = $("<img style='margin-top:20px'>")
                            feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                            center.html(feed_loader)
                            e.append(center)
                        },
                        success: function(data) {
                            friends = data
                            center.remove()
<?php if ($ref == 1) { ?>
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
                                    var nfr = $("<span class='fstat'>")
                                    nfr.html(data[i].followers + " followers")
                                    var nfe = $("<span class='fstat'>")
                                    nfe.html(data[i].followee + " following")
                                    finfo.append(nset)
                                    finfo.append(nfr)
                                    finfo.append(nfe)
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

                                    if(data[i].uid!=<?php echo $id ?>){
                                        var add_friend = $("<input type='button' class='gbutton' value='Add friend' style='width:100px;margin-top:10px;'>");
                                        finfo.append("<br>");
                                        finfo.append(add_friend);
                                        
                                        var follow = $("<input type='button' class='wbutton' value='Follow' style='width:100px;margin:10px;'>");
                                        finfo.append(follow);
                                    }
                                    tile.append(finfo)
                                    
                                    nset.click(function(e) {
                                        scriptLoader("setsLib", "setsLib", [[$(this).parent().parent().attr("uid"), $(this).parent().children("a").html()]], 0)
                                    })
                                }
                                orignal_list = e.html()
<?php } else if ($ref == 3) {
    ?>
                                init()
<?php } ?>
                        }, error: function(jqXHR, textStatus, errorThrown) {
                            alertBox()
                        }
                    })
                }
                $(document).ready(function() {
                    $("#inline_menu_div").css("min-width", screen.width)
<?php if ($ref == 3) { ?>
                        $("#gd").height(0.7 * screen.height)
                        start()
<?php } else if ($ref == 1) { ?>
                        getFriends(fa, "<?php echo $uid ?>", 0, 20);
                        $(document).on("click", "#add_friend", function() {
                        $.ajax({
                            url: "manager/FriendManager.php",
                            cache: false,
                            type: "get",
                            data: "req=add_friend&id=" + user_id + "&uid=" + puser_id,
                            beforeSend: function() {
                                $("#add_friend").replaceWith("<img id='afloader' src='img/ajax_loader_horizontal.gif'>");
                            },
                            success: function(friendship_id) {
                                if (friendship_id != null) {
                                    $("#afloader").replaceWith('<input type="button" data-friendship-id="' + friendship_id + '" id="cancel_friend_request" value="Cancel request" class="wbutton metd" style="width:120px">')
                                    $("#follow").replaceWith("<input type='button' id='following' class='wbutton' value='Unfollow' style='width:110px;'>")
                                    return
                                } else {
                                    alertBox();
                                    $("#afloader").replaceWith("<input type='button' id='add_friend' class='gbutton' value='Add Friend' style='width:120px;'>");
                                    return
                                }
                            }, error: function() {
                                alertBox();
                                $("#afloader").replaceWith("<input type='button' id='add_friend' class='gbutton' value='Add Friend' style='width:120px;'>");
                            }
                        })
                    });
                    $(document).on("click", "#cancel_friend_request", function() {
                        var friendship_id = $(this).attr("data-friendship-id");
                        $.ajax({
                            url: "manager/FriendManager.php",
                            cache: false,
                            type: "get",
                            data: "req=cancel_req&id=" + friendship_id,
                            beforeSend: function() {
                                $("#cancel_friend_request").replaceWith("<img id='floader' src='img/ajax_loader_horizontal.gif'>");
                            },
                            success: function(html) {
                                if (html == 1) {
                                    $("#floader").replaceWith("<input type='button' id='add_friend' class='gbutton' value='Add Friend' style='width:120px;'>")
                                    $("#following").replaceWith("<input type='button' id='follow' class='wbutton' value='Follow' style='width:110px'>");
                                    return
                                } else {
                                    alertBox()
                                    $("#floader").replaceWith('<input type="button" data-friendship-id="' + friendship_id + '" id="cancel_friend_request" value="Cancel request" class="wbutton metd" style="width:120px">')
                                    return
                                }
                            }, error: function() {
                                alertBox()
                                $("#floader").replaceWith('<input type="button" data-friendship-id="' + friendship_id + '" id="cancel_friend_request" value="Cancel request" class="wbutton metd" style="width:120px">')
                            }
                        })
                    });
                    $(document).on("click", "#follow", function() {
                        $.ajax({
                            url: "manager/FriendManager.php",
                            type: "get",
                            cache: false,
                            data: "req=follow&uid=" + user_id + "&fuid=" + puser_id,
                            beforeSend: function() {
                                $("#follow").replaceWith("<img id='flloader' src='img/ajax_loader_horizontal.gif'>");
                            }, success: function(follow_id) {
                                if (follow_id != null) {
                                    $("#flloader").replaceWith("<input type='button' id='following' class='wbutton' value='Unfollow' style='width:110px' data-follow-id='"+follow_id+"'>");
                                    return
                                } else {
                                    alertBox()
                                    $("#flloader").replaceWith('<input type="button" id="follow" class="wbutton" value="Follow" style="width:110px">')
                                    return
                                }
                            }, error: function(e, f) {
                                alertBox();
                            }
                        });
                    });
                    $(document).on("click", "#unfriend", function() {
                        var friendship_id = $(this).attr("data-friendship-id");
                        $.ajax({
                            url: "manager/FriendManager.php",
                            type: "get",
                            cache: false,
                            data: "req=unfriend&fid=" + friendship_id + "&uid=" + user_id + "&fuid=" + puser_id,
                            beforeSend: function() {
                                $("#unfriend").replaceWith("<img id='uloader' src='img/ajax_loader_horizontal.gif'>");
                            }, success: function(html) {
                                if (html == 1) {
                                    $("#uloader").replaceWith("<input type='button' id='add_friend' class='gbutton' value='Add Friend' style='width:120px;'>")
                                } else {
                                    alertBox();
                                    $("#uloader").replaceWith('<input type="button" id="unfriend" class="gbutton" value="Unfriend" style="width:110px;" data-friendship-id="<?php echo $decArray[2] ?>">');
                                }
                            }, error: function(e, f) {
                                alertBox();
                                $("#uloader").replaceWith('<input type="button" id="unfriend" class="gbutton" value="Unfriend" style="width:110px;" data-friendship-id="<?php echo $decArray[2] ?>">');
                            }
                        });
                    });
                    $(document).on("click", "#following", function() {
                        var follow_id = $(this).attr("data-follow-id");
                        $.ajax({
                            url: "manager/FriendManager.php",
                            type: "get",
                            cache: false,
                            data: "req=unfollow&fid=" + follow_id + "&uid=" + user_id + "&fuid=" + puser_id,
                            beforeSend: function() {
                                $("#following").replaceWith("<img id='uflloader' src='img/ajax_loader_horizontal.gif'>");
                            }, success: function(html) {
                                if (html == 1) {
                                    $("#uflloader").replaceWith("<input type='button' id='follow' class='wbutton' value='Follow' style='width:110px;'>")
                                } else {
                                    $("#uflloader").replaceWith('<input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="'+follow_id+'">');
                                }
                            }, error: function(e, f) {
                                alertBox();
                                $("#uflloader").replaceWith('<input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="'+follow_id+'">');
                            }
                        });
                    });
    <?php
} else if ($ref == 2) {
    ?>
                        getFriends($("#friend_list"), "<?php echo $uid ?>", 0, 20)
    <?php
}
if ($ref == 1 || $ref == 3) {
    ?>
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
<?php } ?>
                })
            </script>
        </div>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <script src="js/jquery-ui.js"></script>
        <?php
        if ($ref == 2) {
            ?>
            <script type="text/javascript" src="js/jQueryRotate.js"></script>
            <script type="text/javascript" src="js/rangeSlider.js"></script>
            <link href="css/rangeslider.css" rel="stylesheet">
        <?php } ?>
    </body>
</html>