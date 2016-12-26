<?php
session_start();
if (!isset($_GET["id"])) {
    header("location:badpage.html");
    return;
}
$uid = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($uid, ".") != false || is_numeric($uid) == false || $uid < 1) {
    header("location:badpage.html");
    return;
}

$id = $_SESSION["id"];
$_SESSION["id"] = $id;
//Keep the above part same, everywhere
require "db/DBConnect.php";
require "req/SpecialFunctions.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";
include "supporter/FriendSupporter.php";
include "model/TagsModel.php";
include "controller/TagsController.php";
include "supporter/TagsSupporter.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "supporter/SetsSupporter.php";

$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
if ($uid != $id) {
    $self = 0;
    $decArray = areFriends($uid, $id);
    if ($decArray[0] == false) {    //Public Profile
        $db_connection->mysqli_connect_close();
        header("location:pubPr.php?id=$uid");
    } else {
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage"), null, $persistent_connection);
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
        $puser = $pusercon->getByPrimaryKey($uid, array("first_name", "last_name", "profile_pic", "cover_pic", "email_id", "address", "ph_no", "sets", "interests", "friends", "gender", "rel_status", "nick", "dob"), null, $persistent_connection);
        $pfirst_name = $puser->getFirst_name();
        $plast_name = $puser->getLast_name();
        $pprofile_pic = $puser->getProfile_pic();
        $pcover_pic = $puser->getCover_pic();
        $psets = $puser->getSets();
        $pfriends = $puser->getFriends();
        $pinterests = $puser->getInterests();
        if ($pprofile_pic == null || strlen($pprofile_pic) == 0) {
            $pprofile_pic = "img/default_profile_pic.jpg";
        }
        $pblur_profile_pic = substr($pprofile_pic, 0, strrpos($pprofile_pic, "/")) . "/pblur_" . substr($pprofile_pic, strrpos($pprofile_pic, "/") + 1);
    }
} else {
    $pusercon = new UserController();
    $puser = $pusercon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "cover_pic", "email_id", "address", "ph_no", "sets", "interests", "friends", "gender", "rel_status", "nick", "dob", "signup_stage"), null, $persistent_connection);
    if ($puser->getSignup_stage() == 0) {
        session_destroy();
        header("location:index.php");
        return;
    } else if ($puser->getSignup_stage() == 1) {
        header("location:getting_started.php");
        return;
    }
    $pfirst_name = $first_name = $puser->getFirst_name();
    $plast_name = $last_name = $puser->getLast_name();
    $pprofile_pic = $profile_pic = $puser->getProfile_pic();
    $pcover_pic = $cover_pic = $puser->getCover_pic();
    $psets = $puser->getSets();
    $pfriends = $puser->getFriends();
    $pinterests = $puser->getInterests();
    if ($profile_pic == null || strlen($profile_pic) == 0) {
        $pprofile_pic = $profile_pic = "img/default_profile_pic.jpg";
    }
    $pblur_profile_pic = $blur_profile_pic = substr($profile_pic, 0, strrpos($profile_pic, "/")) . "/blur_" . substr($profile_pic, strrpos($profile_pic, "/") + 1);
    $self = 1;
}
$sets = getUserSets($uid, $id, $persistent_connection);
$friends = getFriends($uid, "0", "10");
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE HTML>
<html style="height:100%;width:100%;">
    <head>
        <title>Profile - <?php echo $pfirst_name . " " . $plast_name ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet" type='text/css'>
        <style>
            #container{
                width:100%;
                height:100%;
            }

            #pillar{
                float:left;
                height:85%;
                width:22%;
                margin-top:80px;
                margin-left:30px;
                border-radius:3px;
                background:white;
                position:fixed;

                box-shadow: 0 0 5px  #777;
                -o-box-shadow: 0 0 5px  #777;
                -webkit-box-shadow:  0 0 5px  #777;
                -moz-box-shadow: 0 0 5px  #777;
                -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(color=#777,strength=5)";
            }

            #main{
                background:white;
                height:100%;

                box-shadow: 0 0 5px  #777;
                -o-box-shadow: 0 0 5px  #777;
                -webkit-box-shadow:  0 0 5px  #777;
                -moz-box-shadow: 0 0 5px  #777;
                -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(color=#777,strength=5)";
            }

            #header{
                width:100%;
                height:120px;
                border-bottom:1px solid #ccc;
                padding-top:55px;
                overflow:hidden;
            }

            #cover_overlay{
                position:absolute;
                width:100%;
                height:120px;
                background:transparent;
                background-image: -ms-linear-gradient(top, transparent 0%, #000 100%);
                background-image: -moz-linear-gradient(top, transparent 0%, #000 100%);
                background-image: -o-linear-gradient(top, transparent 0%, #000 100%);
                background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, transparent), color-stop(1, #000));
                background-image: -webkit-linear-gradient(top, transparent 0%, #000 100%);
                background-image: linear-gradient(to bottom, transparent 0%, #000 100%);
                -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='transparent', endColorstr='#000')";
            }

            #cover_pic{
                width:100%;
                z-index:10;
            }

            #profile_overlay{
                width:100%;
            }

            #pp_div{
                border:5px solid white;
                margin-top:20px;
                width:183px;
                height:183px;

                box-shadow: 0 0 5px  #777;
                -o-box-shadow: 0 0 5px  #777;
                -webkit-box-shadow:  0 0 5px  #777;
                -moz-box-shadow: 0 0 5px  #777;
                -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(color=#777,strength=5)";
            }
            
            #pp_div img{
                width:100%;
            }

            #cpp{
                display:none;
                cursor:pointer;
                position:absolute;
                margin-top:-48px;
                background:black;
                opacity:0.9;
                padding-top:15px;
                padding-bottom:15px;
                color:white;
            }

            #cpp:hover{
                text-shadow: 0 0 4px #aaa;
            }

            #set_gallery_div,friend_div{
                display:table;
                width:100%;
                margin-top:10px;
                margin-bottom:10px;
            }

            .set{
                float:left;
                margin-right:5px;
            }

            .set img{
                border-radius:2px;
            }

            .album{
                display:table;
                float:left;
                margin-right:10px;
                margin-left:10px;
                width:240px;
                height:135px;

                border:1px solid #ccc;
                background:white;
            }

            .album:hover{
                cursor:pointer;
                box-shadow: 0 0 5px  #777;
                -o-box-shadow: 0 0 5px  #777;
                -webkit-box-shadow:  0 0 5px  #777;
                -moz-box-shadow: 0 0 5px  #777;
                -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(color=#777,strength=5)";
            }

            .aimg{
                width:100%;
                display:table;
                overflow:hidden;
            }

            .ainfo{
                padding:10px;
                width:100%;
                color:#444;
            }

            .img_div{
                float:left;
                width:160px;
                height:120px;
                border:1px solid #ccc;
                overflow:hidden;
                margin:10px;
                background:#f6f6f6;
            }
        </style>
        <script src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>
        <script src="js/special.js"></script>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img style='width:30px;height:30px;'>")
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })

            function getUserFeed(e, start, limit) {
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_user_feed&uid=<?php echo $id ?>&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='width:50px;margin-top:80px'>")
                        feed_loader.attr("src", "img/massive_ajax_loader.gif")
                        center.html(feed_loader)
                        e.append(center)
                    },
                    success: function(data) {
                        center.remove()
                        var data_length = data.length, i
                        for (i = 0; i < data_length; i++) {
                            var post = new PostTile(data[i])
                            post.arrangeTile(e, 4, "append")
                        }
                    },
                    error: function(e, f) {
                        alertBox("Some error occured. Please try again later.")
                    }
                })
            }

            function getUserAlbums(e, start, limit) {
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_user_albums&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='width:50px;margin-top:80px'>")
                        feed_loader.attr("src", "img/massive_ajax_loader.gif")
                        center.html(feed_loader)
                        e.append(center)
                    },
                    success: function(data) {
                        center.remove()
                        var data_length = data.length, i
                        for (i = 0; i < data_length; i++) {
                            var album = $("<div id='a" + data[i].id + "' class='album'>")
                            var album_img_div = $("<div class='aimg'>")
                            var album_info_div = $("<div class='ainfo'>")
                            var img = $("<img src='" + data[i].src + "' width='100%'>")
                            album_img_div.html(img)
                            if (i == 0)
                                album_info_div.html("<b>Wall Photos</b>")
                            album.html(album_img_div)
                            album.append(album_info_div)
                            e.append(album)

                            album.click(function() {
                                $.ajax({
                                    url: "manager/PostManager.php",
                                    cache: false,
                                    type: "GET",
                                    dataType: "json",
                                    data: "req=get_album_photos&user_id=<?php echo $uid ?>&album_id=" + album.attr("id").substr(1) + "&start=0&limit=20",
                                    beforeSend: function() {
                                        var feed_loader = $("<img style='width:50px;margin-top:80px'>")
                                        feed_loader.attr("src", "img/massive_ajax_loader.gif")
                                        center.html(feed_loader)
                                        e.html(center)
                                    }, success: function(data) {
                                        center.remove()
                                        var data_length = data.length, i
                                        for (i = 0; i < data_length; i++) {
                                            var img_div = $("<div class='img_div' id='i" + data[i].id + "'>")
                                            if (data[i].height < data[i].width)
                                                var img = $("<img src='" + data[i].src + "' width='100%';>")
                                            else
                                                var img = $("<img src='" + data[i].src + "' height='100%;'>")
                                            e.append(img_div)
                                            img_div.html("<center style='width:100%;height:100%;'></center>")
                                            img_div.children("center").html(img)
                                        }
                                    }, error: function(e, f) {
                                        center.remove()
                                        alertBox("Some error occured. Please try again later.")
                                    }
                                })
                            })
                        }
                    },
                    error: function(e, f) {
                        center.remove()
                        alertBox("Some error occured. Please try again later.")
                    }
                })
            }

<?php
if ($uid != $id) {
    ?>
                var puser_id = "<?php echo $uid ?>"
                var puser_name = "<?php echo $pfirst_name . ' ' . $plast_name ?>"
                var pprofile_pic = "<?php echo $pprofile_pic ?>"
                var pblur_profile_pic = $("<img>")
                pblur_profile_pic.attr({
                    "src": "<?php echo $pblur_profile_pic ?>"
                })
<?php } else { ?>
                var crop_x, crop_y, crop_x1, crop_y1, crop_h, crop_w, resize_factor
                function change_profile_pic() {
                    var file = document.getElementById("cpp_input").files[0]
                    var reader = new FileReader()
                    // init the reader event handlers
                    reader.onload = (function(e) {
                        var fileName = file.name
                        var result = e.target.result
                        $.ajax({
                            url: "req/uploader.php",
                            type: "post",
                            cache: false,
                            dataType: "json",
                            data: "req=upload&name=" + fileName + "&value=" + result + "&record=0",
                            beforeSend: function() {
                                $("#cpp_input").prop("disabled", true)
                                $("#cpp_t").html("<img src='img/ajax_loader_horizontal.gif'>")
                            },
                            success: function(data) {
                                if (data[0] == -1) {
                                    alertBox("Invalid file")
                                    $("#cpp_t").html("Change Profile Pic")
                                    $("#cpp_input").prop("disabled", false)
                                    return
                                }
                                var img = $("<img>")
                                img.attr("src", data.photo_address)
                                var w = data.photo_width
                                var h = data.photo_height
                                cropBox(img, w, h, "profile_pic", $("#profile_pic"))
                                $("#cpp_t").html("Change Profile Pic")
                            }, error: function(e, f) {
                                $("#cpp_input").prop("disabled", false)
                                $("#cpp_t").html("Change Profile Pic")
                            }
                        })
                    })
                    reader.readAsDataURL(file)
                }

                function cropBox(img, w, h, purpose, e) {
                    var cropBox = new Box("crop_box", "40", "50")
                    cropBox.heading = "Profile pic"
                    cropBox.createOverlay(0)
                    var main_body = cropBox.createBox()
                    main_body.width(main_body.parent().width())
                    main_body.height(main_body.parent().height())
                    var pic_area = $("<div id='pic_area' style='width:100%;height:75%;border-bottom:1px solid #ccc;'>")
                    var b_area = $("<div id='b_area' style='width:100%;height:24.5%;'>")
                    var save = $("<input type='button' class='bbutton' value='Save' style='width:80px;float:right;margin:10px;'>")
                    var cancel = $("<input type='button' class='wbutton' value='Cancel' style='width:80px;float:right;margin:10px;'>")
                    cancel.click(function() {
                        cropBox.closeBox()
                    })
                    save.click(function() {
                        var cords = [crop_x, crop_y, crop_x2, crop_y2, crop_w, crop_h]
                        $.ajax({
                            url: "manager/UserManager.php",
                            cache: false,
                            type: "post",
                            data: "req=change_profile_pic&user_id=<?php echo $id ?>&radd=" + img.attr("src") + "&coords=" + encodeURIComponent(JSON.stringify(cords)),
                            beforeSend: function() {
                                save.prop("disabled", true)
                                cancel.prop("disabled", true)
                                b_area.html("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin:10px;'>")
                            }, success: function(address) {
                                if (address == -1) {
                                    alertBox("Some error occured. Please try again later.")
                                    b_area.html(save)
                                    b_area.append(cancel)
                                    save.prop("disabled", false)
                                    cancel.prop("disabled", false)
                                }
                                $("#profile_pic").attr("src", address)
                                var sp = $(".uimg img")
                                sp.attr("src", address)
                                sp.width(30)
                                sp.height(30)
                                cropBox.closeBox()
                            }, error: function(e, f) {
                                alertBox("Some error occured. Please try again later.")
                                b_area.html(save)
                                b_area.append(cancel)
                                save.prop("disabled", false)
                                cancel.prop("disabled", false)
                            }
                        })
                    })
                    b_area.html(save)
                    b_area.append(cancel)
                    main_body.html(pic_area)
                    main_body.append(b_area)
                    pic_area.fitImage(img, w, h, "both")
                    resize_factor = pic_area.children(".wi").val() / pic_area.children(".wo").val()
                    img.Jcrop({
                        aspectRatio: 1,
                        setSelect: [0, 0, 183, 183],
                        allowSelect: false,
                        onChange: getCoords,
                        onSelect: getCoords
                    })
                }

                function getCoords(c) {
                    crop_x = c.x * resize_factor
                    crop_y = c.y * resize_factor
                    crop_x2 = c.x2 * resize_factor
                    crop_y2 = c.y2 * resize_factor
                    crop_w = c.w * resize_factor
                    crop_h = c.h * resize_factor
                }
<?php } ?>

            $(document).ready(function() {
                $("#pillar").width(0.22 * screen.width)
                $("#main").css({
                    "margin-left": $("#pillar").width() + 60,
                    "width": screen.width - ($("#pillar").width() + 60)
                })
                $("#cover_overlay").width($("#header").width())

                var bi = $("#wall").html(), wl, ph, vd
                $("#profile_menu li").click(function() {
                    if ($(this).hasClass("ctb") == false) {
                        var cur_id = $(".ctb").attr("id")
                        $(".ctb").removeClass("ctb")
                        $(this).addClass("ctb")
                        var new_id = $(this).attr("id")

                        if (cur_id == "wl") {
                            wl = $("#wall").html()
                        } else if (cur_id == "ph") {
                            ph = $("#wall").html()
                        } else if (cur_id == "vd") {
                            vd = $("#wall").html()
                        }

                        if (new_id == "bi") {
                            $("#wall").html(bi)
                        } else if (new_id == "wl") {
                            if (wl == null) {
                                $("#wall").html("")
                                getUserFeed($("#wall"), 0, 20)
                            } else {
                                $("#wall").html(wl)
                            }
                        } else if (new_id == "ph") {
                            if (ph == null) {
                                $("#wall").html("")
                                getUserAlbums($("#wall"), 0, 20)
                            } else {
                                $("#wall").html(ph)
                            }
                        } else if (new_id == "vd") {
                            if (vd == null) {
                                $("#wall").html("")
                                alert("will call videos")
                            } else {
                                $("#wall").html(vd)
                            }
                        }
                    }
                })
<?php
if ($uid == $id) {
    ?>
                    $("#cpp").width($("#profile_pic").width())
                    $("#pp_div").hover(function() {
                        $("#cpp").show()
                    }, function() {
                        $("#cpp").hide()
                    })
<?php }
?>

            })
        </script>
    </head>
    <body style="height:100%;width:100%;">
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="pillar">
                <div id="profile_overlay">
                    <center>
                        <div id="pp_div">
                            <img src="<?php echo $pprofile_pic ?>" id="profile_pic">
                            <?php
                            if ($uid == $id) {
                                ?>
                                <div id="cpp">
                                    <input id="cpp_input" type="file" style="position:absolute;width:181px;height:45px;margin-top:-15px;cursor:pointer;opacity:0;" onchange="change_profile_pic()">
                                    <center>
                                        <span id="cpp_t">Change Profile Pic</span>
                                    </center>
                                </div>
                            <?php } ?>
                            <p class="polaroid_font"><?php echo $pfirst_name ?></p>
                        </div>
                    </center>
                </div>
                <div id="set_gallery_div">
                    <h4 style="margin-left:10px;"><a href="setsgallery.php?id=<?php echo $id ?>" class="black_link">Sets</a></h4>
                    <div id="set_gallery" style="width:100%;padding:10px;">
                        <?php
                        $sets_size = sizeof($sets);
                        if ($sets_size > 10)
                            $sets_size = 10;
                        for ($i = 0; $i < $sets_size; $i++) {
                            ?>
                            <div class="set">
                                <a href="set.php?id=<?php echo $sets[$i]["id"] ?>"><img src="<?php echo $sets[$i]["src1"] ?>" style="width:100%;"></a>
                            </div>
                            <?php
                        }
                        ?>
                        <script>
                            $(".set").width((($("#set_gallery").width() - 20) / 5) - 5)
                            $(".set").height($(".set").width())
                        </script>
                    </div>
                </div>
                <?php
                $friends_size = sizeof($friends);
                if ($friends_size > 0) {
                    ?>
                    <div id="friend_div">
                        <h4 style="margin-left:10px;"><a href="friends.php?id=<?php echo $id ?>" class="black_link">Friends</a></h4>
                        <div id="friends" style="width:100%;padding:10px;">
                            <?php
                            for ($i = 0; $i < $friends_size; $i++) {
                                ?>
                                <div class="set">
                                    <a href="profile.php?id=<?php echo $friends[$i]["uid"] ?>"><img src="<?php echo $friends[$i]["profile_pic"] ?>" style="width:100%;"></a>
                                </div>
                                <?php
                            }
                            ?>
                            <script>
                                $(".set").width((($("#set_gallery").width() - 20) / 5) - 5)
                                $(".set").height($(".set").width())
                            </script>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div id="main">
                <div id="header">
                    <div id="cover_overlay">
                        <p style="color:white;margin:10px;margin-left:20px;font-size:50px;word-spacing:-8px;"><?php echo $pfirst_name . " " . $plast_name ?></p>
                    </div>
                    <div id="cp_div"><img src="<?php echo $pcover_pic ?>" id="cover_pic"></div>
                </div>
                <div id="profile_menu_div" style="padding-left:20px;padding-top:10px;display:table;">
                    <ul id="profile_menu" class="hori_menu">
                        <li class="ctb" id="bi">Basic Info</li>
                        <li id="wl">Wall</li>
                        <li id="ph">Photos</li>
                        <li id="vd">Videos</li>
                    </ul>
                </div>
                <div id="wall" style="padding:20px;">
                    <div id="basic_info">
                        <div id="personal_info">
                            <h2>Personal Info</h2>
                            <table style="width:30%;">
                                <tr>
                                    <td>Gender</td>
                                    <?php
                                    $pgender = "";
                                    if (strtolower($puser->getGender()) == 'f')
                                        $pgender = "Female";
                                    else if (strtolower($puser->getGender()) == 'm')
                                        $pgender = "Male";
                                    ?>
                                    <td><?php echo $pgender ?></td>
                                </tr>
                                <tr>
                                    <td>Born on</td>
                                    <td><?php echo formattedDate($puser->getDob()) ?></td>
                                </tr>
                                <tr>
                                    <td>Relationship status</td>
                                    <td><?php
                                        if ($puser->getRel_status() == null) {
                                            echo "";
                                        } else if ($puser->getRel_status() == 1)
                                            echo "Single";
                                        else if ($puser->getRel_status() == 2)
                                            echo "Committed";
                                        else if ($puser->getRel_status() == 3)
                                            echo "Complicated";
                                        else if ($puser->getRel_status() == 4)
                                            echo "Married";
                                        else if ($puser->getRel_status() == 5)
                                            echo "Divorced";
                                        ?></td>
                                </tr>
                                <tr>
                                    <td>Nick</td>
                                    <td><?php echo $puser->getNick() ?></td>
                                </tr>
                            </table>
                        </div>
                        <div id="contact_info">
                            <h2>Contact Info</h2>
                            <table style="width:45%;">
                                <tr>
                                    <td>Email ID</td>
                                    <td><?php echo $puser->getEmail_id() ?></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td><?php echo $puser->getAddress() ?></td>
                                </tr>
                                <?php
                                if ($puser->getPh_no() != 0 && $puser->getPh_no() != NULL) {
                                    ?>
                                    <tr>
                                        <td>Contact Number</td>
                                        <td><?php echo $puser->getPh_no() ?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type='text/css'>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jQueryRotate.js"></script>
        <?php
        if ($uid == $id) {
            ?>
            <script type="text/javascript" src="js/jquery.Jcrop.min.js"></script>
            <link href='http://fonts.googleapis.com/css?family=Rancho' rel='stylesheet' type='text/css'>
            <link href="css/jquery.Jcrop.min.css" rel="stylesheet" type='text/css'>
        <?php } ?>
    </body>
</html>