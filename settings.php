<?php
session_start();
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

$usrcon = new UserController();
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$persistent_connection = $db_connection->getCon();
$user = $usrcon->getByPrimaryKey($id, array("first_name", "last_name", "profile_pic", "signup_stage", "email_id"), null, $persistent_connection);
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
$email_id = $user->getEmail_id();
$profile_pic = $user->getProfile_pic();
if ($profile_pic == null || strlen($profile_pic) == 0) {
    $profile_pic = "img/default_profile_pic.jpg";
}
$blur_profile_pic = getBlurPicAddress($profile_pic);

$db_connection->mysqli_connect_close();
$ref = 1;
if (isset($_GET["ref"])) {
    $ref = $_GET["ref"];
    if ($ref != 2) {
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
        <title>Account Settings</title>
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
<?php
if ($ref == 1) {
    ?>
                function save_settings(e) {
                    var fname = $.trim($("#fname").val());
                    var lname = $.trim($("#lname").val());
                    var email = $.trim($("#email").val());
                    var his = $("#his").is(":checked") ? 1 : 0;
                    var err = 0;
                    if (fname.length == 0 || lname.length == 0 || email.length == 0) {
                        err = 1;
                    }
                    if (err == 1) {
                        if (fname.length == 0) {
                            $("#fname").addClass("errorInput");
                        }
                        if (lname.length == 0) {
                            $("#lname").addClass("errorInput");
                        }
                        if (email.length == 0) {
                            $("#email").addClass("errorInput");
                        }
                        return;
                    }
                    else {
                        if (validateEmailAddress(email) == false) {
                            $("#email").addClass("errorInput");
                            return;
                        }
                        $("#fname").removeClass("errorInput");
                        $("#lname").removeClass("errorInput");
                        $("#email").removeClass("errorInput");
                        $.ajax({
                            url:"manager/UserManager.php",
                            cache:false,
                            type:"POST",
                            data:"req=accsett&user_id="+user_id+"&fname="+encodeURIComponent(fname)+"&lname="+encodeURIComponent(lname)+"&email="+encodeURIComponent(email)+"&his="+his,
                            beforeSend:function(){
                                $(e).replaceWith("<img id='loader' src='img/ajax_loader_horizontal.gif'>");
                            },success:function(sig){
                                
                            },error:function(e,f){
                                alertBox();
                                $("#loader").replaceWith('<input type="button" class="bbutton" value="Save" style="width:80px" onclick="save_settings(this)">');
                            }
                        })
                    }
                }
<?php } ?>

            function changePassword() {
                var pswdBox = new Box("pswdBox", "35", "32");
                pswdBox.createOverlay(1);
                pswdBox.heading = "Change Password";
                var main_body = pswdBox.createBox();

                var table = $("<table style='margin:20px;'>");
                main_body.html(table);

                var tr1 = $("<tr>");
                table.append(tr1);
                var td11 = $("<td>");
                var td12 = $("<td>");
                tr1.html(td11);
                tr1.append(td12);

                var oldpasswordlabel = $("<label for='old_password'>");
                oldpasswordlabel.html("Old password");
                var old_password = $("<input id='old_password' type='password'>");
                td11.html(oldpasswordlabel);
                td12.html(old_password);

                var tr2 = $("<tr>");
                table.append(tr2);
                var td21 = $("<td>");
                var td22 = $("<td>");
                tr2.html(td21);
                tr2.append(td22);

                var newpasswordlabel = $("<label for='new_password'>");
                newpasswordlabel.html("New password");
                var new_password = $("<input id='new_password' type='password'>");
                td21.append(newpasswordlabel);
                td22.append(new_password);

                var tr3 = $("<tr>");
                table.append(tr3);
                var td31 = $("<td>");
                var td32 = $("<td>");
                tr3.html(td31);
                tr3.append(td32);

                var renewpasswordlabel = $("<label for='renew_password'>");
                renewpasswordlabel.html("Reenter new password");
                var renew_password = $("<input id='renew_password' type='password'>");
                td31.append(renewpasswordlabel);
                td32.append(renew_password);

                var center = $("<center>");
                var save = $("<input type='button' class='bbutton' value='Save' style='width:80px;'>")
                center.html(save);
                main_body.append(center);

                save.click(function() {
                    var old_password = $("#old_password").val();
                    var new_password = $("#new_password").val();
                    var renew_password = $("#renew_password").val();

                    if (new_password.length == 0 || old_password.length == 0 || renew_password.length == 0) {
                        alertBox("You must fill in all the details");
                        return;
                    }

                    if (new_password.indexOf(' ') > -1) {
                        alertBox("You cannot has a space in your password");
                        $("#new_password").addClass("errorInput");
                        return;
                    } else {
                        if (new_password != renew_password) {
                            alertBox("Your new password and re-entered passwords do not match");
                            $("#renew_password").addClass("errorInput");
                            return;
                        }
                    }
                    $("#new_password").removeClass("errorInput");
                    $("#renew_password").removeClass("errorInput");

                    $.ajax({
                        url: "manager/UserManager.php",
                        cache: false,
                        type: "POST",
                        data: "req=change_password&user_id=" + user_id + "&op=" + old_password + "&np=" + new_password + "&rnp=" + renew_password,
                        beforeSend: function() {
                            save.replaceWith("<img id='loader' src='img/ajax_loader_horizontal.gif'>")
                        },
                        success: function(sig) {
                            if (sig == -1) {
                                alertBox("You cannot has a space in your password");
                            } else if (sig == -2) {
                                alertBox("You must fill in all the details");
                            } else if (sig == -3) {
                                alertBox("Your new password and re-entered passwords do not match");
                            } else if (sig == 0) {
                                alertBox();
                            }
                            else if (sig == 2) {
                                alertBox("Your old password is wrong.");
                                pswdBox.closeBox()
                            }
                            else if (sig == 1) {
                                pswdBox.closeBox();
                            }
                        },
                        error: function(e, f) {
                            alertBox();
                            pswdBox.close();
                        }
                    })
                });
            }
        </script>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container" style="padding-top:40px">
            <div style="background:white;margin:40px;padding:20px;width:100%;width:calc(100% - 7.5em);" class=" uni_shadow_light">
                <div style="display:table;">
                    <ul class="hori_menu">
                        <?php if ($ref == 1) { ?>
                            <li class="ctb">Account Settings</li>
                            <li><a href="settings.php?ref=2">Notifications</a></li>
                        <?php } else if ($ref == 2) { ?>
                            <li><a href="settings.php">Account Settings</a></li>
                            <li class="ctb">Notifications</li>
                        <?php } ?>
                    </ul>
                </div>
                <?php if ($ref == 1) { ?>
                    <div style="padding-top:20px;width:100%;">
                        <table style="width:50%;">
                            <tr>
                                <td><label for="fname" style="font-weight: bold;font-size:20px">Name</label></td>
                                <td style="padding-bottom:10px;margin-left:20px"><input id="fname" type="text" placeholder="First name" value="<?php echo $first_name ?>">&nbsp;<input id="lname" type="text" placeholder="Last name" value="<?php echo $last_name ?>"></td>
                            </tr>
                            <tr>
                                <td><label for="email" style="font-weight: bold;font-size:20px">Email Address</label></td>
                                <td style="padding-bottom:10px;margin-left:20px"><input id="email" type="email" placeholder="Your email address" value="<?php echo $email_id ?>"></td>
                            </tr>
                            <tr>
                                <td style="padding-bottom:10px;margin-left:20px"><input type="button" class="wbutton" value="Change password" style="width:150px" onclick="changePassword()"></td>
                            </tr>
                        </table>
                        <input id="his" type="checkbox" checked>&nbsp;<label style="font-size:20px" for="his">Let Nemo show you posts and other recommendations based on other sites you have visited recently</label>
                        <br><br><input type="button" class="bbutton" value="Save" style="width:80px" onclick="save_settings(this)">
                        <br><br><a href="#" style="font-size:20px   ">Deactivate account</a>
                    </div>
                <?php } else if ($ref == 2) { ?>
                    <div style="padding-top:20px;width:100%;">
                        <table style="width:40%;">
                            <tr>
                                <td><label for="fname" style="font-size:20px">System notifications</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody starts following me/my set</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody likes my post/set</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody shares my post/set</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody comments on my post/set</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody sends me a message</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody tags me</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                            <tr>
                                <td><label for="fname" style="font-size:20px">Somebody suggests me a tag/set to follow</label></td>
                                <td style="padding-bottom:10px;"><input id="his" type="checkbox" checked></td>
                            </tr>
                        </table>
                        <br><input type="button" class="bbutton" value="Save" style="width:80px" onclick="save_settings(this)">
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>