<?php
session_start();
$set_id = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($set_id, ".") != false) {
    header("location:badpage.html");
    return;
}
if (is_numeric($set_id) == false) {
    header("location:badpage.html");
    return;
}
if ($set_id < 1) {
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
include "supporter/FriendSupporter.php";
include "model/PostModel.php";
include "controller/PostController.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "supporter/SetsSupporter.php";
include "model/Set_followerModel.php";
include "controller/Set_followerController.php";
include "supporter/Set_followerSupporter.php";

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

$setcon = new SetsController();
$set = $setcon->getByPrimaryKey($set_id, array("user_id", "name", "description", "post_count", "followers", "rating", "views", "privacy"), null, $persistent_connection);

if ($set->getUser_id() == $id) {        //self user
    $self = 1;
    $post = new Post();
    $post->setSet_id($set_id);
    $postscon = new PostController();
    $posts = $postscon->findByAll($post, array("*"), "order by score desc", $persistent_connection);

    $sets_category = getSetCategories($set_id, $persistent_connection);
} else {        //some other guy checking
    $self = 0;
    $decArray = areFriends($id, $set->getUser_id());
    if ($set->getPrivacy() != 3) {
        if (($decArray[0] == true && $decArray[1] == 1) || $set->getPrivacy() == 2) {
            $post = new Post();
            $post->setSet_id($set_id);
            $postscon = new PostController();
            $posts = $postscon->findByAll($post, array("*"), "order by score desc", $persistent_connection);
            $posts_size = sizeof($posts);
        } else {
            echo "Sorry, this content is not available to you.";
            return;
        }
    } else {
        echo "Sorry, this content is not available to you.";
        return;
    }
}
$posts_size = sizeof($posts);
$post_array = array();
if ($posts_size > 0) {
    $puser = new User();
    $pusercon = new UserController();
    $puser = $pusercon->getByPrimaryKey($posts[0]->getUser_id(), array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
    for ($i = 0; $i < $posts_size; $i++) {
        $user_liked = 0;
        $query = "select l.id from likes l where l.user_id=? and l.post_id=?";
        $statement2 = $persistent_connection->prepare($query);
        $statement2->bind_param("ii", $id, $posts[$i]->getId());
        $statement2->execute();
        $statement2->bind_result($user_liked);
        $statement2->fetch();
        $statement2->close();
        $parent_type = null;
        $parent_user_name = null;
        $parent_user_id = null;
        if ($posts[$i]->getType() == "share") {
            $query = "select type,user_id from post where id=?";
            $statement2 = $persistent_connection->prepare($query);
            $statement2->bind_param("i", $posts[$i]->getShare_id());
            $statement2->execute();
            $statement2->bind_result($parent_type,$parent_user_id);
            $statement2->fetch();
            $statement2->close();
            
            $query = "select first_name,last_name from user where id=?";
            $statement2 = $persistent_connection->prepare($query);
            $statement2->bind_param("i",$parent_user_id);
            $statement2->execute();
            $statement2->bind_result($f1,$f2);
            $statement2->fetch();
            $statement2->close();
            
            $parent_user_name = $f1." ".$f2;
        }
        $src = $posts[$i]->getSrc();
        if($posts[$i]->getType()=="video"){
            $src = "users/images/" . md5(video_image($src)) . ".jpg";
        }
        $post_array[$i] = array(
            "user_id" => $posts[$i]->getUser_id(),
            "user_name" => $puser->getFirst_name() . " " . $puser->getLast_name(),
            "parent_type" => $parent_type,
            "parent_user_id" => $parent_user_id,
            "parent_user_name" => $parent_user_name,
            "profile_pic" => $puser->getProfile_pic(),
            "set_id" => $set_id,
            "share_id" => $posts[$i]->getShare_id(),
            "share_text" => $posts[$i]->getShare_text(),
            "url" => $posts[$i]->getUrl(),
            "url_content_type" => $posts[$i]->getUrl_content_type(),
            "id" => $posts[$i]->getId(),
            "set_id" => $posts[$i]->getSet_id(),
            "postType" => $posts[$i]->getType(),
            "title" => $posts[$i]->getTitle(),
            "description" => $posts[$i]->getDescription(),
            "src" => $src,
            "width" => $posts[$i]->getWidth(),
            "height" => $posts[$i]->getHeight(),
            "privacy" => $posts[$i]->getPrivacy(),
            "date" => $posts[$i]->getDate(),
            "time" => $posts[$i]->getTime(),
            "likes" => $posts[$i]->getLikes(),
            "shares" => $posts[$i]->getShares(),
            "comments" => $posts[$i]->getComments(),
            "sharable" => $posts[$i]->getSharable(),
            "user_liked" => $user_liked
        );
    }
}
$db_connection->mysqli_connect_close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <script src="js/jquery-latest.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script src="js/special.js"></script>
        <title><?php echo $set->getName() ?></title>
        <style>
            #set_header{
                display:table;
                background:white;
                width:100%;
                padding-top:60px;
                border-bottom:1px solid #ccc;
                word-spacing: -5px;

                box-shadow: 0 2px 2px  #eee;
                -o-box-shadow: 0 2px 2px  #eee;
                -webkit-box-shadow: 0 2px 2px  #eee;
                -moz-box-shadow: 0 2px 2px  #eee;
            }

            #stats{
                margin-left:40px;
                font-weight: bold;
                color:#444;
            }

            .stat{
                float:left;
                margin-right:10px;
                padding-bottom:10px;
            }

            #follower_list{
                width:400px;
                float:right;
                height:65px;
            }

            .flc{
                width:35px;
                height:70px;
                float:right;
            }

            .flc img{
                width:100%;
                height:35px;
            }

            #set_area{
                width:100%;
                width:calc(100% - 2.5em);
                padding:20px;
            }

            .sel{
                background:white;
                box-shadow: 0 2px 2px  #eee;
                -o-box-shadow: 0 2px 2px  #eee;
                -webkit-box-shadow: 0 2px 2px  #eee;
                -moz-box-shadow: 0 2px 2px  #eee;
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

<?php
if ($self == 1) {
    ?>
                var cstate = [], gstate = []
                function settings() {
                    var settingsBox = new Box("settings_box", "68", "70")
                    settingsBox.heading = "Settings"
                    settingsBox.createOverlay(1)
                    var main_body = settingsBox.createBox()
                    main_body.css("position", "relative")

                    var menu_div = $("<div style='width:100%;'>")
                    var opt_ul = $("<ul class='hori_menu' id='opt_ul' style='width:100%;margin-left:20px;'>")
                    var gen_li = $("<li id='gen_li' class='ctb'>")
                    var cat_li = $("<li id='cat_li'>")
                    cat_li.html("Categories")
                    gen_li.html("General")
                    opt_ul.html(gen_li)
                    opt_ul.append(cat_li)
                    menu_div.html(opt_ul)
                    main_body.html(menu_div)
                    var settings_div = $("<div style='width:100%;margin-top:40px;' id='settings_div'>")
                    settings_div.height(main_body.height() - 101)
                    main_body.append(settings_div)

                    gstate = [];
                    cstate = [];
                    generalBlock(settings_div);

                    var bottom_control_div = $("<div style='position:absolute;bottom:0;width:100%;border-top:1px solid #ccc;height:60px;'>")
                    var save = $("<input type='button' class='bbutton fr mr1 mt1' value='Save' style='width:80px;'>")
                    var cancel = $("<input type='button' class='wbutton fr mr2 mt1' value='Cancel' style='width:80px;'>")
                    bottom_control_div.html(cancel)
                    bottom_control_div.append(save)
                    main_body.append(bottom_control_div)

                    cancel.click(function() {
                        settingsBox.closeBox()
                    })

                    save.click(function() {

                    })

                    opt_ul.children("li").click(function() {
                        var context = $(this)
                        var id = context.attr("id")
                        if (context.hasClass("ctb")) {
                        } else {
                            $("#opt_ul li").removeClass("ctb")
                            context.addClass("ctb")
                            if (id == "gen_li") {
                                var ma = $("#catwin_area")
                                cstate = []
                                ma.find(".cat_win").each(function() {
                                    var id = $(this).find("input[type=hidden]").val()
                                    cstate[id] = id
                                })
                                generalBlock(settings_div)
                            } else if (id == "cat_li") {
                                var gb = $("#gen_block"), i = 0;
                                gstate = [];
                                gstate[0] = gb.find("#set_name_input").val();
                                var c = gb.find("#rate_input").is(":checked");
                                (c) ? (gstate[1] = true) : (gstate[1] = false)
                                c = gb.find("#follow_input").is(":checked");
                                (c) ? (gstate[2] = true) : (gstate[2] = false)
                                c = gb.find("#share_input").is(":checked");
                                (c) ? (gstate[3] = true) : (gstate[3] = false)
                                gstate[4] = gb.find("#privacy_div").html()
                                gstate[5] = []
                                gb.find("#si").find(".tag").each(function() {
                                    gstate[5][i] = $(this).find("input[type=hidden]").val()
                                    i++
                                })
                                gstate[6] = []
                                gb.find("#nsi").find(".tag").each(function() {
                                    gstate[6][i] = $(this).find("input[type=hidden]").val()
                                    i++
                                })
                                c = gb.find("#sharable").is(":checked");
                                (c) ? (gstate[7] = true) : (gstate[7] = false)
                                categoryBlock(settings_div)
                            }
                        }
                    })
                }

                var categories = [], set_categories = $.parseJSON(<?php print json_encode(json_encode($sets_category)); ?>);
                var set_name = "<?php echo $set->getName() ?>"
                function categoryBlock(settings_div) {
                    var cat_block = $("<div style='width:100%;'>")
                    cat_block.height(settings_div.height())
                    settings_div.html(cat_block)

                    var cat_area = $("<div class='fl' style='width:33%;height:100%;overflow:auto;'>")
                    cat_block.html(cat_area)

                    var main_area = $("<div id='catwin_area' class='fl' style='width:67%;height:100%;overflow:auto;'>")
                    cat_block.append(main_area)
                    if (cstate.length == 0) {
                        if (categories.length == 0) {
                            $.ajax({
                                url: "manager/CategoryManager.php",
                                type: "get",
                                dataType: "json",
                                data: "req=get_categories",
                                beforeSend: function() {
                                    cat_area.html("<center><img src='img/ajax_loader_horizontal.gif'></center>")
                                }, success: function(data) {
                                    categories = data
                                    cat_area.html("")
                                    arrangeCategories(cat_area, main_area)
                                }, error: function() {
                                    alertBox("Some error occured. Please try again later.")
                                    settings_div.parent().closeBox()
                                }
                            })
                        } else {
                            arrangeCategories(cat_area, main_area)
                        }
                    } else {
                        arrangeCategories(cat_area, main_area)
                    }
                }

                function syncCat(category_id, set, type) {
                    var i
                    if (type == "obj") {
                        for (i = 0; i < set.length; i++) {
                            if (category_id == set[i].id) {
                                return true
                            }
                        }
                    } else {
                        for (i = 0; i < set.length; i++) {
                            if (category_id == set[i]) {
                                return true
                            }
                        }
                    }
                    return false
                }

                function arrangeCategories(category_panel, category_window_area) {
                    var i
                    for (i = 0; i < categories.length; i++)
                    {
                        var category_block
                        if (cstate.length == 0) {
                            if (syncCat(categories[i].id, set_categories, "obj")) {
                                category_block = $("<div style='display:none;border-bottom:1px solid #ccc;width:100%;' id='c" + i + "'>")
                                windowCreate(i, category_window_area)
                            }
                            else
                                category_block = $("<div style='display:table;border-bottom:1px solid #ccc;width:100%;' id='c" + i + "'>")
                        } else {
                            if (syncCat(categories[i].id, cstate, "arr")) {
                                category_block = $("<div style='display:none;border-bottom:1px solid #ccc;width:100%;' id='c" + i + "'>")
                                windowCreate(i, category_window_area)
                            }
                            else
                                category_block = $("<div style='display:table;border-bottom:1px solid #ccc;width:100%;' id='c" + i + "'>")
                        }
                        category_block.addClass("cp")
                        category_block.css({
                            "padding-top": "5px",
                            "padding-bottom": "5px"
                        })
                        category_block.hover(function() {
                            $(this).css({
                                "background": "#007dff",
                                "color": "white"
                            })
                        }, function() {
                            $(this).css({
                                "background": "white",
                                "color": "black"
                            })
                        })
                        var img = $("<img>")
                        img.attr("src", categories[i].img_src)
                        img.css({
                            "width": "48px",
                            "height": "32px",
                            "border": "2px solid white"
                        })
                        img.addClass("uni_shadow_light")
                        img.addClass("ml1")
                        category_block.html(img)

                        var category_id = $("<input type='hidden'>")
                        category_id.val(categories[i].id)
                        category_block.append(category_id)

                        var category_name = $("<span>")
                        category_name.addClass("ml1")
                        category_name.html(categories[i].name)
                        category_block.append(category_name)
                        category_panel.append(category_block)

                        category_block.click(function() {
                            $(this).hide('slide', {
                                direction: 'right'
                            }, 150, function() {
                                windowCreate($(this).attr("id").substr(1), category_window_area)
                            })
                        })
                    }
                }

                function windowCreate(i, category_window_area) {
                    var category_window = $("<div class='cat_win'>")
                    category_window.css({
                        "float": "left",
                        "display": "none"
                    })
                    var img = $("<img>")
                    img.attr("src", categories[i].img_src)
                    category_window.html(img)
                    img.width("100%")
                    img.addClass("uni_shadow_light")
                    category_window.addClass("mt1")
                    category_window.css({
                        "margin-left": "30px"
                    })
                    category_window.addClass("cp")
                    img.css({
                        "border": "5px solid white",
                        "background": "white",
                        "padding-bottom": "30px"
                    })
                    category_window.hover(function() {
                        $(this).find("img").addClass("gen_hover_shadow")
                        remove.css({
                            "visibility": "visible"
                        })
                    }, function() {
                        $(this).find("img").removeClass("gen_hover_shadow")
                        remove.css({
                            "visibility": "hidden"
                        })
                    })

                    var cat_name = $("<p style='margin-top:-35px;'>")
                    cat_name.addClass("polaroid_font")
                    cat_name.html(categories[i].name)
                    category_window.append(cat_name)

                    var cat_id = $("<input type='hidden'>")
                    cat_id.val(categories[i].id)
                    category_window.append(cat_id)

                    category_window_area.append(category_window)
                    category_window.css("display", "table")
                    category_window.rotate({
                        duration: 1000,
                        angle: 180,
                        animateTo: 0
                    })

                    category_window.width(category_window_area.width() / 4)
                    img.height(img.width() / 1.5)

                    var remove = $("<p>")
                    remove.html("Remove")
                    remove.css({
                        "visibility": "hidden",
                        "text-align": "center",
                        "margin-top": "10px",
                        "color": "#007dff"
                    })
                    remove.hover(function() {
                        remove.css({
                            "color": "#14b30e",
                            "text-decoration": "underline"
                        })
                    }, function() {
                        remove.css({
                            "color": "#007dff",
                            "text-decoration": "none"
                        })
                    })
                    remove.click(function() {
                        $(this).parent().remove()
                        $("#c" + i).show('slide', {
                            direction: 'right'
                        }, 150)
                    })

                    category_window.append(remove)
                }

                function generalBlock(settings_div) {
                    var gen_block = $("<div id='gen_block' style='width:100%;overflow:auto;'>")
                    gen_block.height(settings_div.height())
                    settings_div.html(gen_block)

                    var name_div = $("<div id='name_div' class='ml2 mt2'>")
                    gen_block.html(name_div)
                    var name_label = $("<label id='name_label' class='mr2'>")
                    name_label.html("Set Name")
                    name_div.html(name_label)

                    var set_name_input;
                    if (gstate.length == 0)
                        set_name_input = $("<input id='set_name_input' type='text' placeholder='Name of this set' value='" + set_name + "' style='width:300px'>")
                    else
                        set_name_input = $("<input id='set_name_input' type='text' placeholder='Name of this set' value='" + gstate[0] + "' style='width:300px'>")
                    name_div.append(set_name_input)

                    var noti_div = $("<div id='noti_div' class='ml2 mt2'>")
                    gen_block.append(noti_div)
                    noti_div.html("<h3>Notifications</h3>")

                    var tbl = $("<table>")
                    noti_div.append(tbl)

                    var rtr = $("<tr>")
                    tbl.html(rtr)
                    var rate_label = $("<label for='rate_input'>")
                    rate_label.html("Notify me when somebody rates this set")
                    var rltd = $("<td>")
                    rltd.html(rate_label)
                    var rate_input;
                    if (gstate.length == 0)
                        rate_input = $("<input type='checkbox' id='rate_input' class='ml2'>")
                    else {
                        if (gstate[1] == 1)
                            rate_input = $("<input type='checkbox' id='rate_input' class='ml2' checked>")
                        else
                            rate_input = $("<input type='checkbox' id='rate_input' class='ml2'>")
                    }
                    var ritd = $("<td>")
                    ritd.html(rate_input)
                    rtr.html(rltd)
                    rtr.append(ritd)

                    var ftr = $("<tr>")
                    tbl.append(ftr)
                    var follow_label = $("<label for='follow_input'>")
                    follow_label.html("Notify me when somebody follows this set")
                    var fltd = $("<td>")
                    fltd.html(follow_label)
                    var follow_input;
                    if (gstate.length == 0)
                        follow_input = $("<input type='checkbox' id='follow_input' class='ml2'>")
                    else {
                        if (gstate[2] == 1)
                            follow_input = $("<input type='checkbox' id='follow_input' class='ml2' checked>")
                        else
                            follow_input = $("<input type='checkbox' id='follow_input' class='ml2'>")
                    }
                    var fitd = $("<td>")
                    fitd.html(follow_input)
                    ftr.html(fltd)
                    ftr.append(fitd)

                    var str = $("<tr>")
                    tbl.append(str)
                    var share_label = $("<label for='share_input'>")
                    share_label.html("Notify me when somebody shares this set")
                    var sltd = $("<td>")
                    sltd.html(share_label)
                    var share_input;
                    if (gstate.length == 0)
                        share_input = $("<input type='checkbox' id='share_input' class='ml2'>")
                    else {
                        if (gstate[3] == 1)
                            share_input = $("<input type='checkbox' id='share_input' class='ml2' checked>")
                        else
                            share_input = $("<input type='checkbox' id='share_input' class='ml2'>")
                    }
                    var sitd = $("<td>")
                    sitd.html(share_input)
                    str.html(sltd)
                    str.append(sitd)

                    var privacy_div = $("<div id='privacy_div' class='ml2 mt2'>")
                    privacy_div.html("<h3>Privacy</h3>")

                    var sharing_status_label = $("<label class='fl'>")
                    sharing_status_label.html("Shared with")
                    privacy_div.append(sharing_status_label)
                    var sharing_button = $("<div class='fl' style='margin-left:20px'>")
                    var sharing_specs = $("<div style='display:table;width:100%;'>")
                    var ssp_cover = $("<div style='width:50%;'>")
                    sharing_specs.html(ssp_cover)
                    privacy_div.append(sharing_specs)

                    sharing_button.menuButton({
                        source: ["Public", "Friends", "Custom"],
                        sourceImage: ["img/friendreq.png", "img/friendreq.png.", "img/friendreq.png"],
                        width: "90px",
                        callback: function(privacy) {
                            if (privacy == "Friends") {
                                var not_share_input = $("<div style='margin-top:10px' id='nsi'>")
                                not_share_input.addTagger("Do not share with")
                                ssp_cover.html(not_share_input)
                            } else if (privacy == "Public") {
                                sharing_specs.html("")
                            } else if (privacy == "Custom") {
                                var share_input = $("<div style='margin-top:10px;' id='si'>")
                                share_input.addTagger("Share with")
                                ssp_cover.html(share_input)

                                var not_share_input = $("<div style='margin-top:10px' id='nsi'>")
                                not_share_input.addTagger("Do not share with")
                                ssp_cover.append(not_share_input)
                            }
                        }
                    })
                    privacy_div.append(sharing_button)
                    privacy_div.append(sharing_specs)

                    var sharable_label = $("<label for='sharable' class='mt1'>")
                    sharable_label.html("Sharable")
                    privacy_div.append(sharable_label)
                    var sharable;
                    if (gstate.length == 0)
                        sharable = $("<input type='checkbox' id='sharable' class='ml2' checked>")
                    else {
                        if (gstate[7] == 1)
                            sharable = $("<input type='checkbox' id='sharable' class='ml2' checked>")
                        else
                            sharable = $("<input type='checkbox' id='sharable' class='ml2'>")
                    }
                    privacy_div.append(sharable)
                    gen_block.append(privacy_div)
                }
    <?php
}
?>

            $(document).ready(function() {
                var data = []
                $("#rating").addRatingWidget();
<?php
//for ($i = 1; $i < $posts_size; $i++) {
//    $j = json_encode($post_array[$i]);
//    if (strlen($j) != 0)
//        echo "data[". $i ."] = " . $j . ";";
//}
echo "data=" . json_encode($post_array) . ";\n";
?>
                var data_length = data.length, i;
                for (i = 0; i < data_length; i++) {
                    var post = new PostTile(data[i]);
                    post.arrangeTile($("#set_area"), 5, "append", null);
                }
            })
        </script>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="set_header">
                <?php
                if ($self == 0) {
                    ?>
                    <input type="button" class="wbutton fr mt2 mr2" value="Share" style="width:90px;">
                    <input type="button" class="gbutton fr mt2 mr1" value="Follow Set" style="width:90px;">
                    <?php
                } else {
                    ?>
                    <input type="button" class="wbutton fr mt2 mr2" value="Settings" style="width:90px;" onclick="settings()">
                    <?php
                }
                ?>
                <div><h1 style="color:#444;margin-left:40px;" id="set_name"><?php echo $set->getName() ?></h1></div>
                <div id="stats">
                    <div class="stat">
                        <table>
                            <tr>
                                <td><img src="img/black_view.png" style="width:20px;"></td>
                                <td><span style="font-size:20px;margin-left:3px;"><?php echo $set->getViews() ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="stat">
                        <table>
                            <tr>
                                <td><img src="img/follow.jpg" style="width:30px;"></td>
                                <td><span style="font-size:20px;margin-left:3px;"><?php echo $set->getFollowers() ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="stat">
                        <table>
                            <tr>
                                <td><img src="img/post.jpg" style="width:25px;margin-left:-5px;"></td>
                                <td><span style="font-size:20px;margin-left:3px;"><?php echo $set->getPost_count() ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="stat">
                        <table>
                            <tr>
                                <td><div class='star_black'></div></td>
                                <td><span style="font-size:20px;margin-left:3px;"><?php echo number_format($set->getRating(), 2, ".", ","); ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div id="set_area"></div>
        </div>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>
        <script type="text/javascript" src="js/jQueryRotate.js"></script>
    </body>
</html>
