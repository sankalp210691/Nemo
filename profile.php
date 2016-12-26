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
//Keep the above part same, everywhere
require "db/DBConnect.php";
require "req/SpecialFunctions.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/FriendModel.php";
include "controller/FriendController.php";
include "supporter/FriendSupporter.php";
include "model/FollowModel.php";
include "controller/FollowController.php";
include "model/TagsModel.php";
include "controller/TagsController.php";
include "supporter/TagsSupporter.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "model/PostModel.php";
include "controller/PostController.php";
include "supporter/SetsSupporter.php";

$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
if ($uid != $id) {
    $self = 0;
    $decArray = areFriends($uid, $id);
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
    $blur_profile_pic = getBlurPicAddress($profile_pic);

    $pusercon = new UserController();
    $puser = $pusercon->getByPrimaryKey($uid, array("first_name", "last_name", "profile_pic", "cover_pic", "email_id", "address", "ph_no", "sets", "interests", "friends", "followers", "followee", "gender", "rel_status", "nick", "dob", "about_me", "nick_privacy", "email_id_privacy", "dob_privacy", "address_privacy", "gender_privacy", "rel_status_privacy"), null, $persistent_connection);
    $pfirst_name = $puser->getFirst_name();
    $plast_name = $puser->getLast_name();
    $pprofile_pic = $puser->getProfile_pic();
    $pcover_pic = $puser->getCover_pic();
    $pemail_id = $puser->getEmail_id();
    $pemail_id_privacy = $puser->getEmail_id_privacy();
    $paddress = $puser->getAddress();
    $paddress_privacy = $puser->getAddress_privacy();
    $pph_no = $puser->getPh_no();
    $pdob = $puser->getDob();
    $pdob_privacy = $puser->getDob_privacy();
    $pgender = $puser->getGender();
    $pgender_privacy = $puser->getGender_privacy();
    $prel_status = $puser->getRel_status();
    $prel_status_privacy = $puser->getRel_status_privacy();
    $pnick = $puser->getNick();
    $pnick_privacy = $puser->getNick_privacy();
    $pabout_me = $puser->getAbout_me();
    $psets = $puser->getSets();
    $pfriends = $puser->getFriends();
    $pfollowers = $puser->getFollowers();
    $pfollowee = $puser->getFollowee();
    $pinterests = $puser->getInterests();
    if ($pprofile_pic == null || strlen($pprofile_pic) == 0) {
        $pprofile_pic = "img/default_profile_pic.jpg";
    }
    $pblur_profile_pic = getBlurPicAddress($pprofile_pic);

    $follow = new Follow();
    $followcon = new FollowController();
    $follow->setFollower_id($id);
    $follow->setFollowee_id($uid);
    $follows = $followcon->findByAll($follow, array("id"), null, $persistent_connection);
    if (sizeof($follows) == 0) {
        $following = 0;
    } else {
        $following = 1;
        $follow_id = $follows[0]->getId();
    }
} else {
    $pusercon = new UserController();
    $puser = $pusercon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "cover_pic", "email_id", "address", "ph_no", "sets", "interests", "friends", "followers", "followee", "gender", "rel_status", "nick", "dob", "signup_stage", "about_me", "nick_privacy", "email_id_privacy", "dob_privacy", "address_privacy", "gender_privacy", "rel_status_privacy"), null, $persistent_connection);
    if ($puser->getSignup_stage() == 0) {
        session_destroy();
        header("location:index.php");
        return;
    } else if ($puser->getSignup_stage() == 1) {
        header("location:getting_started.php");
        return;
    }
    $pfirst_name = $puser->getFirst_name();
    $plast_name = $puser->getLast_name();
    $pprofile_pic = $puser->getProfile_pic();

    $first_name = $pfirst_name;
    $last_name = $plast_name;
    $profile_pic = $pprofile_pic;

    $pcover_pic = $puser->getCover_pic();
    $pemail_id = $puser->getEmail_id();
    $pemail_id_privacy = $puser->getEmail_id_privacy();
    $paddress = $puser->getAddress();
    $paddress_privacy = $puser->getAddress_privacy();
    $pph_no = $puser->getPh_no();
    $pdob = $puser->getDob();
    $pdob_privacy = $puser->getDob_privacy();
    $pgender = $puser->getGender();
    $pgender_privacy = $puser->getGender_privacy();
    $prel_status = $puser->getRel_status();
    $prel_status_privacy = $puser->getRel_status_privacy();
    $pnick = $puser->getNick();
    $pnick_privacy = $puser->getNick_privacy();
    $pabout_me = $puser->getAbout_me();
    $psets = $puser->getSets();
    $pfriends = $puser->getFriends();
    $pfollowers = $puser->getFollowers();
    $pfollowee = $puser->getFollowee();
    $pinterests = $puser->getInterests();
    if ($profile_pic == null || strlen($profile_pic) == 0) {
        $pprofile_pic = $profile_pic = "img/default_profile_pic.jpg";
    }
    $pblur_profile_pic = getBlurPicAddress($profile_pic);

    $self = 1;
}
if ($pgender == "m")
    $pgender = "Male";
else if ($pgender == "f")
    $pgender = "Female";
else if ($pgender=="t")
    $pgender = "Transgender";
else $pgender = "";
switch ($prel_status) {
    case 0:$prel_status = "Single";
        break;
    case 1:$rel_status = "Committed";
        break;
    case 2:$rel_status = "Married";
        break;
    case 3:$rel_status = "Divorced";
        break;
    case 4:$rel_status = "Its complicated";
        break;
}
$post = new Post();
$post->setUser_id($uid);
$postcon = new PostController();
$previewposts = $postcon->findByAll($post, array("type", "src"), "order by id desc limit 6", $persistent_connection);
$previewposts_length = sizeof($previewposts);
if ($self == 0)
    $no_of_mutual_friends = getMutualFriendsCount($uid, $id);
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE HTML>
<html style="height:100%;width:100%;">
    <head>
        <title>Profile - <?php echo $pfirst_name . " " . $plast_name ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet" type='text/css'>
        <script src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>
        <script src="js/special.js"></script>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img style='width:30px;height:30px;'>")
            var categories = <?php echo json_encode($_SESSION["categories"]) ?>;
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
                        var feed_loader = $("<img style='margin-top:80px'>")
                        feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                        center.html(feed_loader)
                        e.append(center)
                    },
                    success: function(data) {
                        center.remove();
                        var data_length = data.length, i
                        if (data_length == 0) {
                            e.html("<center style='clear:both;width:100%;'><div style='font-size:20px;color:#444;width:100%;padding:20px;width:calc(100% - 2.5em);'>No posts to show</div></center>")
                        } else {
                            for (i = 0; i < data_length; i++) {
                                var post = new PostTile(data[i])
                                post.arrangeTile(e, 4, "append", null)
                            }
                        }
                    },
                    error: function(e, f) {
                        alertBox()
                    }
                })
            }

            function ma1(param) {
                var e = param[0]
                var start = param[1]
                var limit = param[2]
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_user_albums&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='margin-top:80px'>")
                        feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                        center.html(feed_loader)
                        e.append(center)
                    },
                    success: function(data) {
                        center.remove()
                        var data_length = data.length, i
                        for (i = 0; i < data_length; i++) {
                            var album = $("<div id='a" + data[i].id + "' class='album'>")
                            var album_img_div = $("<div class='aimg' style='background-image:url(\"" + data[i].src + "\")'>")
                            var album_info_div = $("<div class='ainfo'>")
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
                                        var feed_loader = $("<img style='margin-top:80px'>")
                                        feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                                        center.html(feed_loader)
                                        e.html(center)
                                    }, success: function(data) {
                                        center.remove()
                                        e.append("<br>");
                                        var data_length = data.length, i;
                                        if (data_length == 0) {
                                            e.html("<center style='clear:both;width:100%;'><div style='font-size:20px;color:#444;width:100%;padding:20px;width:calc(100% - 2.5em);'>No photos to show</div></center>")
                                        } else {
                                            for (i = 0; i < data_length; i++) {
                                                var img_div = $("<div class='img_div' id='i" + data[i].id + "' style='background-image:url(\"" + data[i].src + "\")'>")
                                                e.append(img_div)
                                                img_div.click(function() {
                                                    scriptLoader("postView", "postView", [[$(this).attr("id").substring(1)], [false]], 0, "")
                                                })
                                            }
                                        }
                                    }, error: function(e, f) {
                                        center.remove()
                                        alertBox()
                                    }
                                })
                            })
                        }
                    },
                    error: function(e, f) {
                        center.remove()
                        alertBox()
                    }
                })
            }

            function ma2(param) {
                var e = param[0]
                var start = param[1]
                var limit = param[2]
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_user_videos&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='margin-top:80px'>")
                        feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                        center.html(feed_loader)
                        e.append(center)
                    }, success: function(data) {
                        e.html("<br>")
                        var data_length = data.length, i;
                        if (data_length == 0) {
                            e.html("<center style='clear:both;width:100%;'><div style='font-size:20px;color:#444;width:100%;padding:20px;width:calc(100% - 2.5em);'>No videos to show</div></center>")
                        } else {
                            for (i = 0; i < data_length; i++) {
                                var video = $("<div id='v" + data[i].id + "' class='video'>")
                                var video_img_div = $("<div class='vimg' style='background-image:url(\"" + unrenderHTML(data[i].src) + "\")'>")
                                var video_info_div = $("<div class='vinfo'>")
                                var desc = data[i].description
                                if (desc == null)
                                    desc = "";
                                if (desc.length > 40)
                                    desc = desc.substring(0, 37) + "..."
                                video_info_div.html("<p style='text-overflow:ellipsis;overflow:hidden;height:1em'><b>" + unrenderHTML(data[i].title) + "</b></p><p style='text-overflow:ellipsis;overflow:hidden;height:1em'>" + unrenderHTML(desc) + "</p>")
                                video.html(video_img_div)
                                video.append(video_info_div)
                                e.append(video)
                                video.click(function() {
                                    scriptLoader("postView", "postView", [[$(this).attr("id").substring(1)], [false]], 0, "")
                                })
                                $(".video").width(($(".kind").width() - 80) / 3)
                                $(".video").height(3 * $(".video").width() / 4)
                            }
                        }
                    }, error: function(e, f) {
                        center.remove()
                        alertBox()
                    }
                })
            }

            function ma3(param) {
                var e = param[0]
                var start = param[1]
                var limit = param[2]
                var center = $("<center id='post_loader'>")
                $.ajax({
                    url: "manager/PostManager.php",
                    cache: false,
                    type: "GET",
                    dataType: "json",
                    data: "req=get_user_links&user_id=<?php echo $uid ?>&start=" + start + "&limit=" + limit,
                    beforeSend: function() {
                        var feed_loader = $("<img style='margin-top:80px'>")
                        feed_loader.attr("src", "img/ajax_loader_horizontal.gif")
                        center.html(feed_loader)
                        e.append(center)
                    }, success: function(data) {
                        e.html("<br>")
                        var data_length = data.length, i;
                        if (data_length == 0) {
                            e.html("<center style='clear:both;width:100%;'><div style='font-size:20px;color:#444;width:100%;padding:20px;width:calc(100% - 2.5em);'>No web links to show</div></center>")
                        } else {
                            for (i = 0; i < data_length; i++) {
                                var link = $("<div id='l" + data[i].id + "' class='link'>")
                                var link_img_div = $("<div class='limg' style='background-image:url(\"" + data[i].src + "\")'>")
                                var link_info_div = $("<div class='linfo'>")
                                var ldisp = data[i].url
                                if (ldisp.substring(0, 4) == "http") {
                                    ldisp = ldisp.split("/")[2]
                                } else {
                                    ldisp = ldisp.split("/")[0]
                                }
                                link_info_div.html("<p style='text-overflow:ellipsis;overflow:hidden;height:1em;font-size:20px;'><b><a target='_blank' href='" + data[i].url + "'>" + ldisp + "</b></a></p></b></p><p style='text-overflow:ellipsis;overflow:hidden;height:2em'><b>" + unrenderHTML(data[i].title) + "</b></p><p style='text-overflow:ellipsis;overflow:hidden;height:3em'>" + unrenderHTML(data[i].description) + "</p>")
                                link.html(link_img_div)
                                link.append(link_info_div)
                                e.append(link)
                                link.find("a").click(function(e) {
                                    e.stopPropagation();
                                });
                                link.click(function() {
                                    scriptLoader("postView", "postView", [[$(this).attr("id").substring(1)], [false]], 0, "")
                                })
                                $(".link").width(($("#ma3").width() - 120) / 3)
                                $(".link").height(3 * $(".link").width() / 4)
                            }
                        }
                    }, error: function(e, f) {
                        center.remove()
                        alertBox()
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
                var ppicf = 0;
                function change_display_pic(el) {
                    var purpose, file, input_id = $(el).attr("id"), cprefix
                    if (input_id == "cpp_input") {
                        purpose = "profile_pic"
                        cprefix = "p"
                    } else if (input_id == "ccp_input") {
                        purpose = "cover_pic"
                        cprefix = "c"
                    }
                    file = document.getElementById(input_id).files[0]
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
                                $("#" + input_id).prop("disabled", true)
                            },
                            success: function(data) {
                                if (data[0] == -1) {
                                    alertBox("Invalid file");
                                    $("#" + input_id).prop("disabled", false)
                                    return
                                }
                                var img = $("<img>")
                                img.attr("src", data.photo_address)
                                var w = data.photo_width
                                var h = data.photo_height
                                cropBox(img, w, h, purpose, $("#" + purpose))
                            }, error: function(e, f) {
                                alertBox()
                                $("#" + input_id).prop("disabled", false)
                            }
                        })
                    })
                    reader.readAsDataURL(file)
                }

                function cropBox(img, w, h, purpose, e) {
                    $("#pic_ajax_loader").remove();
                    if (purpose == "profile_pic") {
                        $("#cpp_input").prop("disabled", false);
                        $("#cpp_input").val("")
                    } else if (purpose == "cover_pic") {
                        $("#ccp_input").prop("disabled", false);
                        $("#ccp_input").val("")
                    }

                    var cropBox = new Box("crop_box", "50", "60")
                    if (purpose == "profile_pic") {
                        cropBox.heading = "Profile picture"
                    } else if (purpose == "cover_pic") {
                        cropBox.heading = "Cover picture"
                    }
                    cropBox.createOverlay(0)
                    var main_body = cropBox.createBox()
                    main_body.width(main_body.parent().width())
                    main_body.height(main_body.parent().height())
                    var pic_area = $("<div id='pic_area' style='width:100%;height:75%;border-bottom:1px solid #ccc;'>")
                    var b_area = $("<div id='b_area' style='width:100%;height:24.5%;'>")
                    var save = $("<input type='button' class='bbutton' value='Save' style='width:80px;float:right;margin:10px;'>")
                    var cancel = $("<input type='button' class='wbutton' value='Cancel' style='width:80px;float:right;margin:10px;'>")
                    cancel.click(function() {
                        cropBox.closeBox();
                    })
                    save.click(function() {
                        var cords = [crop_x, crop_y, crop_x2, crop_y2, crop_w, crop_h]
                        $.ajax({
                            url: "manager/UserManager.php",
                            cache: false,
                            type: "post",
                            data: "req=change_" + purpose + "&user_id=<?php echo $id ?>&radd=" + img.attr("src") + "&coords=" + encodeURIComponent(JSON.stringify(cords)),
                            beforeSend: function() {
                                save.prop("disabled", true)
                                cancel.prop("disabled", true)
                                b_area.html("<img src='img/ajax_loader_horizontal.gif' style='float:right;margin:10px;'>")
                            }, success: function(address) {
                                if (address == -1) {
                                    alertBox()
                                    b_area.html(save)
                                    b_area.append(cancel)
                                    save.prop("disabled", false)
                                    cancel.prop("disabled", false)
                                } else if (address == -2) {
                                    alertBox("Invalid image size. Minimum size for cover picture is 762x90 and for profile pic is 50x50.")
                                    b_area.html(save)
                                    b_area.append(cancel)
                                    save.prop("disabled", false)
                                    cancel.prop("disabled", false)
                                    cropBox.closeBox()
                                    return
                                }
                                $("#" + purpose).attr("src", address)
                                var sp = $(".uimg img")
                                sp.attr("src", address)
                                sp.width(30)
                                sp.height(30)
                                cropBox.closeBox()
                            }, error: function(e, f) {
                                alertBox()
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
                    if (purpose == "profile_pic") {
                        img.Jcrop({
                            aspectRatio: 1,
                            setSelect: [0, 0, 260, 260],
                            allowSelect: false,
                            minSize: [50, 50],
                            onChange: getCoords,
                            onSelect: getCoords
                        })
                    } else if (purpose == "cover_pic") {
                        img.Jcrop({
                            aspectRatio: 8.542,
                            setSelect: [0, 0, 762, 90],
                            minSize: [762, 90],
                            allowSelect: false,
                            onChange: getCoords,
                            onSelect: getCoords
                        })
                    }
                }

                function getCoords(c) {
                    crop_x = c.x * resize_factor
                    crop_y = c.y * resize_factor
                    crop_x2 = c.x2 * resize_factor
                    crop_y2 = c.y2 * resize_factor
                    crop_w = c.w * resize_factor
                    crop_h = c.h * resize_factor
                }

                var edit = 0;
                function editProfile() {
                    if (edit == 0) {
                        var about_me_input = $("<textarea class='ed' id='about_me_input' style='margin:20px;margin-top:0;resize:none;width:80%;width:calc(100% - 2.5em);height:" + ($("#info_div").height() - 70) + "px'>" + $.trim($("#about_me").html()) + "</textarea>");
                        $("#about_me").replaceWith(about_me_input);

                        var nick = $.trim($("#nick").html());
                        var nick_input = $("<input class='ed' id='nick_input' type='text' placeholder='What do they call you?' value='" + nick + "'>");
                        $("#nick").replaceWith(nick_input);

                        var gender = $.trim($("#gender").html());
                        var gender_input = $("<select class='ed' id='gender_input'><option value='f' id='genf'>Female</option><option value='m' id='genm'>Male</option><option value='t' id='gent'>Transgender</option></select>");
                        $("#gender").replaceWith(gender_input);
                        $("#gen" + (gender.charAt(0)).toLowerCase()).prop("selected", true);

                        var dob = $.trim($("#dob").html());
                        var current_year = (new Date).getFullYear();
                        var dob_input = $("<input class='ed' id='dob_input' type='text' class='datepicker' value='" + dob + "'>").datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: 'dd-MM-yy',
                            showAnim: 'slideDown',
                            yearRange: '1900:' + current_year,
                            onSelect: function() {
                                var dateAsObject = $(this).datepicker('getDate')
                                var date = dateAsObject.getDate()
                                var month = dateAsObject.getMonth()
                                var year = dateAsObject.getYear() + 1900
                                var output = date + " " + getMonthName(month) + ", " + year
                                $("input[type='datepicker']").val(output)
                            }
                        });
                        $("#gen" + dob).attr("selected", true);
                        $("#dob").replaceWith(dob_input);

                        var relationship = $.trim($("#rel").html());
                        var relationship_input = $("<select class='ed' id='relationship_input'><option value='1' id='rel1'>Single</option><option value='2' id='rel2'>Committed</option><option value='3' id='rel3'>It's complicated</option><option value='4' id='rel4'>Married</option><option value='5' id='rel5'>Divorced</option></select>");
                        $("#rel").replaceWith(relationship_input);
                        $("#rel" + relationship).attr("selected", true);

                        var email_id = $.trim($("#email_id").html());
                        var email_id_input = $("<input class='ed' id='email_id_input' type='email' placeholder='Your email ID' value='" + email_id + "'>");
                        $("#email_id").replaceWith(email_id_input);

                        $("#edit_profile").val("Save changes");
                        edit = 1;
                    } else if (edit == 1) {
                        var about_me = $.trim($("#about_me_input").val());
                        var nick = $.trim($("#nick_input").val());
                        var gender = $("#gender_input").val();
                        var dob = $("#dob_input").val();
                        var rel = $("#relationship_input").val();
                        var email_id = $.trim($("#email_id_input").val());

                        if (about_me == null || about_me.length == 0) {
                            $("#about_me_input").addClass("error_input");
                            return;
                        } else {
                            $("#about_me_input").removeClass("error_input")
                        }
                        if (dob == null || dob.length == 0) {
                            $("#dob_input").addClass("error_input");
                            return;
                        } else {
                            $("#dob_input").removeClass("error_input")
                        }
                        if (email_id == null || email_id.length == 0) {
                            $("#email_id_input").addClass("error_input");
                            return;
                        } else {
                            $("#email_id_input").removeClass("error_input")
                        }

                        $.ajax({
                            url: "manager/UserManager.php",
                            type: "post",
                            data: "req=edit_profile&user_id=" + user_id + "&about_me=" + encodeURIComponent(about_me) + "&nick=" + encodeURIComponent(nick) + "&gender=" + gender + "&dob=" + encodeURIComponent(dob) + "&rel=" + rel + "&email_id=" + encodeURIComponent(email_id),
                            beforeSend: function() {
                                $("#edit_profile").replaceWith("<img id='eploader' src='img/ajax_loader_horizontal.gif'>");
                                $(".ed").prop("disabled", true);
                            }, success: function(ret) {
                                alert(ret)
                                if (ret == 1) {
                                    $("#about_me_input").replaceWith("<p id='about_me'>" + about_me + "</p>");
                                    $("#nick_input").replaceWith("<td id='nick'>" + nick + "</td>");
                                    $("#gender_input").replaceWith("<td id='gender'>" + gender + "</td>");
                                    $("#dob_input").replaceWith("<td id='dob'>" + dob + "</td>");
                                    $("#rel_input").replaceWith("<td id='rel'>" + rel + "</td>");
                                    $("#email_id_input").replaceWith("<td id='email_id'>" + email_id + "</td>");

                                    $("#eploader").replaceWith("<input type='button' value='Edit Profile' style='width:100px;' class='bbutton'>");
                                    edit = 0;
                                } else {
                                    alertBox();
                                    $("#eploader").replaceWith("<input id='edit_profile' type='button' value='Save changes' style='width:100px;' class='bbutton' onclick='editProfile();'>");
                                }
                            }, error: function(e, f) {
                                alertBox();
                                $("#eploader").replaceWith("<input id='edit_profile' type='button' value='Save changes' style='width:100px;' class='bbutton' onclick='editProfile();'>");
                            }
                        })
                    }
                }
<?php } ?>
            $(document).ready(function() {
                $("#container").slimScroll({
                    height: $("body").height(),
                    size: "10px",
                    railVisible: true,
                    railColor: '#222',
                    railOpacity: 0.3,
                    wheelStep: 10
                })
                $(".uia").hover(function() {
                    $(this).find("p").css("color", "#007dff");
                }, function() {
                    $(this).find("p").css("color", "#444");
                })
                $("#up_block,#down_block").width(screen.width)
                $("#info_block,#main_block").css("min-width", 0.75 * screen.width)
                $("#info_block,#user_block").height(120 + (0.166 * screen.width))
                $("#info_div").height($("#info_block").height() - 120)
                $("#cover_overlay").width($("#info_block").width())
                $("#cover_overlay").height($("#header").height())
                $(".post_preview").height($(".post_preview").width())
                $('.post_preview').find('img').each(function() {
                    var imgClass = (this.width / this.height > 1) ? 'wide' : 'tall';
                    $(this).addClass(imgClass);
                })
                $(".user_info").height($(".user_info").width() * 2 / 3)

                $("#ccircle").css("margin-top", ($("#cover_overlay").height() - 50) / 2)

                $("#cdimmer").width($("#cover_overlay").width())
                $("#cdimmer").height($("#cover_overlay").height())
                $("#ccp_input").width($("#cover_overlay").width())
                $("#ccp_input").height($("#cover_overlay").height())

                var ma = []
                $("#profile_menu li").click(function() {
                    if ($(this).hasClass("ctb") == false) {
                        var cur_id = $(".ctb").attr("id")
                        $(".ctb").removeClass("ctb")
                        $(this).addClass("ctb")
                        var new_id = $(this).attr("id")

                        ma[cur_id.substring(2)] = $("#ma" + cur_id.substring(2)).html()
                        $("#ma" + cur_id.substring(2)).fadeOut("300")
                        $("#ma" + new_id.substring(2)).fadeIn("300")
                        if (ma[new_id.substring(2)] == null) {
                            var params = [$("#ma" + new_id.substring(2)), 0, 20]
                            var fname = "ma" + new_id.substring(2)
                            window[fname](params)
                        }
                    }
                })
<?php
if ($self == 1) {
    ?>
                    $("#cover_overlay").hover(function() {
                        $("#cdimmer").css("opacity", "0.9")
                    }, function() {
                        $("#cdimmer").css("opacity", "0")
                    })
    <?php
} else if ($self == 0) {
    ?>
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
                                    $("#flloader").replaceWith("<input type='button' id='following' class='wbutton' value='Unfollow' style='width:110px' data-follow-id='" + follow_id + "'>");
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
                                    $("#uflloader").replaceWith('<input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="' + follow_id + '">');
                                }
                            }, error: function(e, f) {
                                alertBox();
                                $("#uflloader").replaceWith('<input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="' + follow_id + '">');
                            }
                        });
                    });
<?php } ?>
            })
        </script>
        <style>
            .dshadow{
                -webkit-box-shadow:  0 0 13px #666;
                -moz-box-shadow:  0 0 13px #666;
                box-shadow:  0 0 13px #666;
                -o-box-shadow: 0 0 13px #666;
            }

            .dimmer{
                position:absolute;
                background:black;
                opacity:0;
                cursor:pointer;
            }

            .circle{
                background:white;
                margin:0 auto;
            }

            #user_block,#pinfo_block{
                float:left;
                margin:20px;
                margin-left:30px;
                border-radius:2px;
                width:260px;
                height:350px;
                background:white;
            }

            #user_block div{
                width:100%;
            }

            #ppic_div{
                position:relative;
                height:260px;
                border-bottom:1px solid #ccc;
            }

            #profile_pic{
                border-radius:2px 2px 0 0;
                width:100%;
            }

            #info_block,#main_block{
                margin:20px 5px;
                float:left;
                width:75%;
                height:350px;
                background:white;
                border-radius:2px;
            }

            #header{
                width:100%;
                height:120px;
                overflow:hidden;
            }

            #cover_overlay{
                position:absolute;
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
                border-radius:2px 2px 0 0;
            }

            #info_div{
                margin:0 auto;
                clear:both;
                width:100%;
                height:230px;
                border:1px solid #ccc;
                border-top:0;
            }

            .idt{
                width:33.33%;
                height:100%;
                border-right:1px solid #ccc;
            }

            .idt:last-child{
                border:0;
                margin-left:-2px;
            }

            .idt h2,.idt p{
                margin:10px 20px;
                color:#444;
                font-weight:lighter;
                font-family: "Microsoft Yi Baiti";
                text-align: justify;
            }

            .post_preview{
                float:left;
                width:33.33%;
                overflow:hidden;
                background-size: cover;
            }

            .dimView{
                width:100%;
                height:100%;
                background:black;
                opacity:0.9;
                color:white;
                font-size:18px;
                text-align:center;
                display:table;
                padding-top:35%;
                border-right:1px solid black;
            }

            .dimView:hover{
                color:white;
                text-decoration: none;
                outline:none;
                cursor:pointer;
                text-shadow: 0 0 3px white;
            }

            .user_info{
                float:left;
                width:50%;

                -webkit-box-shadow: 1px 1px 1px #ccc;
                -moz-box-shadow: 1px 1px 1px #ccc;
                box-shadow:  1px 1px 1px #ccc;
                -o-box-shadow: 1px 1px 1px #ccc;
            }

            #pinfo_block{
                color:#444;
                margin-top:10px;
                height:auto;
                padding-bottom:5px;
            }

            #pinfo_block h3{
                margin:10px;
                margin-bottom:-5px;
            }

            #pinfo_block table{
                margin:0 10px;
                width:70%;
            }

            #pinfo_block table tr td{
                width:50%;
                font-family: "Calibri";
                font-size:15px;
            }

            #main_block{
                margin-top:10px;
                padding-bottom:10px;
                height:auto;
            }

            #main_arena{
                width:100%;
                height:auto;
            }

            #main_arena .kind{
                width:100%;
                height:auto;
                padding:10px 20px;
                display:none;
            }

            .album,.video,.link{
                display:table;
                float:left;
                margin:10px;
                width:240px;
                height:250px;

                border:1px solid #ccc;
                border-radius:3px;
                background:#f6f6f6;
            }

            .album:hover,.img_div:hover,.video:hover,.link:hover{
                cursor:pointer;
                box-shadow: 0 0px 20px #444;
                -o-box-shadow: 0 0px 20px #444;
                -webkit-box-shadow: 0 0px 20px #444;
                -moz-box-shadow: 0 0px 20px #444;   
                -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(color=#444,strength=20)";
            }

            .aimg,.vimg,.limg{
                width:100%;
                height:240px;
                overflow:hidden;
                background-size:cover;
            }

            .ainfo,.vinfo,.linfo{
                padding:10px;
                width:100%;
                width:calc(100% - 1.25em);
                background:white;
                color:#444;
                text-align:justify;
                white-space: pre-wrap;      /* Webkit */    
                white-space: -moz-pre-wrap; /* Firefox */     
                white-space: -pre-wrap;     /* Opera <7 */    
                white-space: -o-pre-wrap;   /* Opera 7 */     
                word-wrap: break-word;      /* IE */ 
            }

            .img_div{
                float:left;
                width:240px;
                height:180px;
                border:1px solid #ccc;
                border-radius:3px;
                margin:5px;
                overflow:hidden;
                background:#f6f6f6;
                background-size:cover;
            }

            .video,.link{
                height:auto;
            }

            .inflst{
                font-size:18px;
                font-weight:bold;
                color:#444;
            }

            .inflst li{
                margin-right:20px;
            }
        </style>
    </head>
    <body style="height:100%;width:100%;">
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div style="padding-top:55px;width:100%;" id="up_block">
                <div id="user_block" class="dshadow">
                    <div id="ppic_div">
                        <img src="<?php echo $pprofile_pic ?>" id="profile_pic">
                    </div>
                    <div style="padding:10px;">
                        <p style='font-size:20px;font-family:Calibri;color:#444;margin-bottom:5px'><?php echo $pfirst_name . " " . $plast_name ?></p>
                        <?php
                        if ($self == 1) {       //self profile
                            ?>
                            <input id='edit_profile' type='button' class='bbutton' value='Edit Profile' style='width:100px;' onclick="editProfile();">
                            <input type='button' class='wbutton' value='Change profile pic' style='width:130px;'>
                            <input type='file' id='cpp_input' style='position:absolute;opacity:0;cursor:pointer;width:128px;height:33px;left:147px' onchange='change_display_pic(this)'>
                            <?php
                        } else if ($decArray[0] == true && $decArray[1] == 1) {      //normal friends
                            ?>
                            <input type="button" id='unfriend' class="gbutton" value="Unfriend" style="width:110px;" data-friendship-id="<?php echo $decArray[2] ?>">
                            <?php if ($following == 1) { ?>
                                <input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="<?php echo $follow_id ?>">
                            <?php } else { ?>
                                <input type="button" id="follow" class="wbutton" value="Follow" style="width:110px;">
                            <?php } ?>
                        <?php } else if ($decArray[0] == false) {       //totally public   ?>            
                            <input type="button" id="add_friend" class="gbutton" value="Add Friend" style="width:120px;">
                            <?php if ($following == 1) { ?>
                                <input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="<?php echo $follow_id ?>">
                            <?php } else { ?>
                                <input type="button" id="follow" class="wbutton" value="Follow" style="width:110px;">
                            <?php } ?>
                        <?php } else if ($decArray[0] == true && $decArray[1] == 0) {      //sent friend request previously  ?>
                            <input type="button" id="cancel_friend_request" class="wbutton" value="Cancel Request" style="width:120px" data-friendship-id="<?php echo $decArray[2] ?>">
                            <?php if ($following == 1) { ?>
                                <input type="button" id="following" class="wbutton" value="Unfollow" style="width:110px;" data-follow-id="<?php echo $follow_id ?>">
                            <?php } else { ?>
                                <input type="button" id="follow" class="wbutton" value="Follow" style="width:110px;">
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div id="info_block" class="dshadow">
                    <div style="position:relative">
                        <div id="header">
                            <div id="cover_overlay">
                                <?php
                                if ($self == 1) {
                                    ?>
                                    <div id='cdimmer' class='dimmer'>
                                        <div style='width:50px;height:50px;border-radius:50%;' class='circle' id="ccircle">
                                            <input type='file' id='ccp_input' style='position:absolute;opacity:0;cursor:pointer;top:0;left:0;' onchange='change_display_pic(this)'>
                                            <img src='img/camera.png' style='margin:9px;width:32px;'>
                                        </div>
                                    </div>
                                <?php } ?>
                                <p style="color:white;margin:10px;margin-left:20px;font-size:50px;word-spacing:-8px;"><?php echo $pfirst_name . " " . $plast_name ?></p>
                            </div>
                            <div id="cp_div"><img src="<?php echo $pcover_pic ?>" id="cover_pic"></div>
                        </div>
                    </div>
                    <div id="info_div">
                        <div class="fl idt">
                            <h2>About me</h2>
                            <p id="about_me">
                                <?php
                                if (strlen($pabout_me) > 430)
                                    echo decorateWithLinks(unrenderHTML(substr($pabout_me, 0, 427))) . "...";
                                else
                                    echo decorateWithLinks(unrenderHTML($pabout_me));
                                ?>
                            </p>
                        </div>
                        <div class="fl idt">
                            <?php
                            for ($i = 0; $i < $previewposts_length - 1; $i++) {
                                if ($previewposts[$i]->getType() != "video") {
                                    ?>
                                    <div class="post_preview" style="background-image: url('<?php echo $previewposts[$i]->getSrc() ?>')"></div>
                                    <?php
                                } else {
                                    $src = "users/images/" . md5(video_image($previewposts[$i]->getSrc())) . ".jpg";
                                    ?>
                                    <div class="post_preview" style="background-image: url('<?php echo $src ?>')"></div>
                                    <?php
                                }
                            }
                            if ($previewposts_length > 1) {
                                if ($previewposts[$i]->getType() != "video") {
                                    ?>
                                    <div class="post_preview" style="background-image: url('<?php echo $previewposts[$i]->getSrc() ?>');"><a href="setsgallery.php?id=<?php echo $uid ?>" class="dimView">View All Sets</a></div>
                                    <?php
                                } else {
                                    $src = "users/images/" . md5(video_image($previewposts[$i]->getSrc())) . ".jpg";
                                    ?>
                                    <div class="post_preview" style="background-image: url('<?php echo $src ?>');"><a href="setsgallery.php?id=<?php echo $uid ?>" class='dimView'>View All Sets</a></div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="post_preview" ><a href="setsgallery.php?id=<?php echo $uid ?>" class="dimView">View All Sets</a></div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="fl idt">
                            <a class='uia' href='friends.php?id=<?php echo $id ?>'><div class="user_info">
                                    <center><img src="img/friendreq.png" style="margin-top:30px;width:40px;"><p style='font-size:18px;font-weight:bold;text-align:center'><?php echo $pfollowers ?> Friends</p></center>
                                </div></a>
                            <a class='uia' href='friends.php?id=<?php echo $id ?>'><div class="user_info">
                                    <center><img src="img/follow.jpg" style="margin-top:30px;width:40px;"><p style='font-size:18px;font-weight:bold;text-align:center'><?php echo $pfollowers ?> Followers</p></center>
                                </div></a>
                            <a class='uia' href='friends.php?id=<?php echo $id ?>'><div class="user_info">
                                    <center><img src="img/follow.jpg" style="margin-top:30px;width:40px;"><p style='font-size:18px;font-weight:bold;text-align:center'><?php echo $pfollowee ?> Following</p></center>
                                </div></a>
                            <div class="user_info">
                                <center><img src="img/friendreq.png" style="margin-top:30px;width:40px;"><p style='font-size:18px;font-weight:bold;text-align:center'>Active User</p></center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="down_block" style="width:100%;">
                <div id="pinfo_block" class="dshadow">
                    <h3>Personal Information</h3>
                    <br>
                    <table>
                        <?php
                        $cnt = 0;
                        if ($self == 1 || (($pnick != null && strlen($pnick) != 0 && ($pnick_privacy == 2 || ($pnick_privacy == 1 && $decArray[0] == true && $decArray[1] == 1))))) {
                            $cnt++;
                            ?>
                            <tr><td class="b">Nick:</td><td id="nick"><?php echo $pnick ?></td></tr>
                        <?php } ?>
                        <?php
                        if ($self == 1 || $pgender_privacy == 2 || ($pgender_privacy == 1 && $decArray[0] == true && $decArray[1] == 1)) {
                            $cnt++;
                            ?>
                            <tr><td class="b">Gender:</td><td id="gender"><?php echo $pgender ?></td></tr>
                        <?php } ?>
                        <?php
                        if ($self == 1 || $pdob_privacy == 2 || ($pdob_privacy == 1 && $decArray[0] == true && $decArray[1] == 1)) {
                            $cnt++;
                            ?>
                            <tr><td class="b">Born on:</td><td id="dob"><?php echo formattedDate($pdob) ?></td></tr>
                        <?php } ?>
                        <?php
                        if ($self == 1 || $prel_status_privacy == 2 || ($prel_status_privacy == 1 && $decArray[0] == true && $decArray[1] == 1)) {
                            $cnt++;
                            ?>
                            <tr><td class="b">I am:</td><td id="rel"><?php echo $prel_status ?></td></tr>
                        <?php } ?>
                    </table>
                    <?php
                    if ($cnt == 0)
                        echo "<p style='margin-left:10px;margin-top:-10px;'>Not shared</p>";
                    $cnt = 0;
                    ?>
                    <h3>Contact Information</h3>
                    <br>
                    <table>
                        <?php
                        if ($self == 1 || $pemail_id_privacy == 2 || ($pemail_id_privacy == 1 && $decArray[0] == true && $decArray[1] == 1)) {
                            $cnt++;
                            ?>
                            <tr><td class="b">Email:</td><td id="email_id"><?php echo $pemail_id ?></td></tr>
                        <?php } ?>
                        <?php
                        if ($self == 1 || $paddress_privacy == 2 || ($paddress_privacy == 1 && $decArray[0] == true && $decArray[1] == 1)) {
                            $cnt++;
                            ?>
                            <tr><td class="b">Address:</td><td id="address"><?php echo $paddress ?></td></tr>
                        <?php } ?>
                    </table>
                    <?php
                    if ($cnt == 0)
                        echo "<p style='margin-left:10px;margin-top:-10px;'>Not shared</p>";
                    $cnt = 0;
                    ?>
                </div>
                <div id="main_block" class="dshadow">
                    <div id="profile_menu" class=" ml2 mt1" style="width:100%;">
                        <ul class="hori_menu">
                            <li id="mo0" class="ctb">Wall</li>
                            <li id="mo1">Photos</li>
                            <li id="mo2">Videos</li>
                            <li id="mo3">Web Links</li>
                            <li id="mo4">Places</li>
                            <li id="mo5">Experience</li>
                            <li id="mo6">Panorama</li>
                        </ul>
                    </div>
                    <div id="main_arena" class="mt2">
                        <div id="ma0" class="kind" style="margin-left:-5px;display:block"></div>
                        <div id="ma1" class="kind" style="margin-left:-10px;"></div>
                        <div id="ma2" class="kind" style="margin-left:-10px;"></div>
                        <div id="ma3" class="kind" style="margin-left:-5px;"></div>
                        <div id="ma4" class="kind" style="margin-left:-5px;"></div>
                        <div id="ma5" class="kind" style="margin-left:-5px;"></div>
                        <div id="ma6" class="kind" style="margin-left:-5px;"></div>
                        <script type="text/javascript">
                            getUserFeed($("#ma0"), 0, 20)
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type='text/css'>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jQueryRotate.js"></script>
        <?php
        if ($self == 1) {
            ?>
            <script type="text/javascript" src="js/jquery.Jcrop.min.js"></script>
            <link href='http://fonts.googleapis.com/css?family=Rancho' rel='stylesheet' type='text/css'>
            <link href="css/jquery.Jcrop.min.css" rel="stylesheet" type='text/css'>
        <?php } ?>
    </body>
</html>