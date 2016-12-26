<?php
session_start();
if (isset($_SESSION['id'])) {
    $id = $_SESSION["id"];
} else {
    $id = -1;
}
require "db/DBConnect.php";
include "req/SpecialFunctions.php";
if ($id != -1) {
    include "model/UserModel.php";
    include "controller/UserController.php";

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
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/discover.css" rel="stylesheet">
        <script src="js/jquery-latest.js"></script>
        <script src="js/special.js"></script>
        <title>Discover</title>
        <script type="text/javascript">
            user_id = "<?php echo $id ?>"
            user_name = "<?php echo $first_name . " " . $last_name ?>"
            profile_pic = "<?php echo $profile_pic ?>"
            var blur_profile_pic = $("<img>")
            blur_profile_pic.attr({
                "src": "<?php echo $blur_profile_pic ?>"
            })
            //Faking Placeholder
            $('input[placeholder]').placeholder();
            $('textarea[placeholder]').placeholder();
            //Faking Placeholder ends
            $(document).ready(function(){
                 $("#filler").width(0.95 * screen.width)
                 $("#bpost").height((3/4)*$("#bpost").width())
                 $("#mpost,#spostcontainer").height($("#bpost").height())
            })
        </script>
    </head>
    <body>
        <?php
        if ($id != -1) {
            include "head_menu.html";
            include "dock.html";
        }
        ?>
        <div id="container">
            <div id="filler">
                <div id="trendiv">
                    <div id="tpostdiv" class="dshadow">
                        <h3>Trending Posts</h3>
                        <div id="tgal">
                            <div id="bpost" class="fl tpost" style="background-image:url('users/images/de84dffb5c22a012824fe81b383565e0.jpg')">
                                <div class='tpostinfo' id='bpostinfo'>
                                    <h2>Selena Gomez breaks up with boyfriend Justin Beiber without asking me!</h2>
                                    <input type='button' class='gbutton' value='View post' style='width:80px;margin-top:5px;'>
                                </div>
                            </div>
                            <div id="mpost" class="fl tpost" style="background-image:url('users/images/6f0cbcaa418faa7688793ad488869aec.jpg')">
                                <div class='tpostinfo' id='mpostinfo'></div>
                            </div>
                            <div id="spostcontainer" class="fl">
                                <div id="uspost" class="tpost" style="background-image:url('users/images/0aefbcf528df2ece791b582a9ecefee6.jpg')">
                                    <div class='tpostinfo' id='uspostinfo'></div>
                                </div>
                                <div id="lspost" class="tpost" style="background-image:url('users/images/7369d237fdc761a3a186b133a4596075.jpg')">
                                    <div class='tpostinfo' id='lspostinfo'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ttagdiv" class="dshadow">
                        
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="js/jquery.masonry.min.js"></script>    
        <script type="text/javascript" src="js/jquery-ui.js"></script>
</html>
