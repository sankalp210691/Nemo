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
if ($uid == $id) {
    header("location:priPr.php?id=$id");
} else {
    require "db/DBConnect.php";
    include "model/UserModel.php";
    include "controller/UserController.php";
    include "supporter/FriendSupporter.php";
    $decArray = areFriends($uid, $id);
    if ($decArray[0] == true && $decArray[1] > 0) {    //Public Profile
        header("location:priPr.php?id=$uid");
    } else {
        $usercon = new UserController();
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
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
        $puser = $pusercon->getByPrimaryKey($uid, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
        $pfirst_name = $user->getFirst_name();
        $plast_name = $user->getLast_name();
        $pprofile_pic = $user->getProfile_pic();
        if ($pprofile_pic == null || strlen($pprofile_pic) == 0) {
            $pprofile_pic = "img/default_profile_pic.jpg";
        }
        $pblur_profile_pic = substr($pprofile_pic, 0, strrpos($pprofile_pic, "/")) . "/pblur_" . substr($pprofile_pic, strrpos($pprofile_pic, "/") + 1);
        $db_connection->mysqli_connect_close();
    }
}
?>
<html>
    <head>
        <script src="js/jquery-latest.js"></script>
        <script src="js/special.js"></script>
        <script type="text/javascript">
            var user_id = "<?php echo $id ?>"
            var user_name = "<?php echo $first_name . ' ' . $last_name ?>"
            var profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })
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
                $(document).ready(function() {
                    $("#add_friend").click(function() {
                        $.ajax({
                            url: "manager/FriendManager.php",
                            cache: false,
                            type: "get",
                            data: "req=add_friend&id=" + user_id + "&uid=" + puser_id,
                            beforeSend: function() {
                                $(this).prop('disabled', true);
                            },
                            success: function(html) {
                                if (html == 1) {
                                    $(this).removeProp('disabled')
                                    $(this).replaceWith('<input type="button" value="Cancel Friend Request" class="wbutton metd" id="cancel_friend_request">')
                                    return
                                } else {
                                    alertBox("Some problem occured. Please try again later.")
                                    $(this).removeProp('disabled');
                                    return
                                }
                            }, error: function() {
                                alertBox("Some problem occured. Please try again later.")
                                $(this).removeProp('disabled');
                            }
                        })
                    })
                    $("#cancel_req").click(function() {
                        var friendship_id = $("#req_id").val()
                        $.ajax({
                            url: "manager/FriendManager.php",
                            cache: false,
                            type: "get",
                            data: "req=cancel_req&id=" + friendship_id,
                            beforeSend: function() {
                                $(this).prop('disabled', true);
                            },
                            success: function(html) {
                                if (html == 1) {
                                    $(this).removeProp('disabled')
                                    $(this).replaceWith('<input type="button" value="Cancel Friend Request" class="wbutton metd" id="cancel_friend_request">')
                                    return
                                } else {
                                    alertBox("Some problem occured. Please try again later.")
                                    $(this).removeProp('disabled');
                                    return
                                }
                            }, error: function() {
                                alertBox("Some problem occured. Please try again later.")
                                $(this).removeProp('disabled');
                            }
                        })
                    })
                })
<?php } ?>
        </script>
    </head>
    <body>
        <?php
        if ($decArray[0] == true && $decArray[1] == 0) {
            ?>
            <input type="hidden" value="<?php echo $decArray[2] ?>" id="req_id">
            <input type="button" class="bbutton" value="Cancel friend's request" id="cancel_req">
        <?php } else {
            ?>
            <input type="button" class="bbutton" value="Add friend" id="add_friend">
        <?php } ?>
    </body>
</html>
