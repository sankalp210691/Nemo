<?php
session_start();
$cid = trim(stripslashes(preg_replace("/#.*?\n/", "\n", preg_replace("/\/*.*?\*\//", "", preg_replace("/\/\/.*?\n/", "\n", preg_replace("/<!--.*?-->/", "", str_replace('"', "", str_replace("'", "", $_GET["id"]))))))));
if (strpos($cid, ".") != false) {
    header("location:badpage.html");
    return;
}
if (is_numeric($cid) == false) {
    header("location:badpage.html");
    return;
}
if ($cid < 1) {
    header("location:badpage.html");
    return;
}
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    return;
}

$id = $_SESSION["id"];

require "db/DBConnect.php";
include "model/UserModel.php";
include "controller/UserController.php";
include "model/CategoryModel.php";
include "controller/CategoryController.php";
include "model/SetsModel.php";
include "controller/SetsController.php";
include "model/Sets_categoryModel.php";
include "controller/Sets_categoryController.php";
include "supporter/Sets_categorySupporter.php";
include "req/SpecialFunctions.php";

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

$categorycon = new CategoryController();
$category = $categorycon->getByPrimaryKey($cid, array("*"), null, $persistent_connection);
$category_name = $category->getName();
$category_image = $category->getImage_src();
$category_rank = $category->getRank();

$setarray = getSetsByCategory($cid, 0, 35, $persistent_connection);

$db_connection->mysqli_connect_close();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="css/special.css" rel="stylesheet">
        <link href="css/category.css" rel="stylesheet">
        <script src="js/jquery-latest.js"></script>
        <script src="js/special.js"></script>
        <title><?php echo $category_name ?></title>
        <script type="text/javascript">
            user_id = "<?php echo $id ?>"
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
                $(".imgn").height($(".imgn").width())
                $("#mostmedia").height($("#most").height() + 12)
                $("#mostdesc").height($("#most").height() - 28)
            })
        </script>
    </head>
    <body>
        <?php
        include "head_menu.html";
        include "dock.html";
        ?>
        <div id="container">
            <div id="filler">
                <div id="arena" class="dshadow">
                    <div id="larena">
                        <div id="intro" class="block whiteblock">
                            <center><img src="<?php echo $category_image ?>" id="cimage"></center>
                            <h1><?php echo $category_name ?></h1>
                            <center>
                                <span><b>25,000 posts</b></span>&nbsp;&nbsp;<span><b>400 Sets</b></span>
                                <br><br>
                                <input type="button" class="gbutton" style="width:120px;" value="Create set">
                                &nbsp;
                                <input type="button" class="wbutton" style="width:150px;" value="Add to existing set">
                            </center>
                        </div>
                        <div id='most' class='block whiteblock'>
                            <center style='width:100%;'>
                                <h1>Most popular posts</h1>
                            </center>
                            <br>
                            <div style='width:100%;display:table;'>
                                <?php
                                for ($i = 0; $i < 21; $i++) {
                                    ?>
                                    <div class="imgn" style="background-image: url('img/interests/Alexandra Potter (<?php echo ($i + 1) % 13 ?>).jpg')"></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div id="marena">
                        <div id="catsearchdiv" class="block">
                            <input type="search" id="catsearch" placeholder='Search this category'>
                        </div>
                        <div style="background-image:url('img/interests/Alexandra Potter (3).jpg')" id='mostmedia'></div>
                    </div>
                    <div id="rarena">
                        <div id="tagged" class="block whiteblock">
                            <h2>Popular Tags</h2>
                            <?php
                            for ($i = 0; $i < 15; $i++) {
                                ?>
                                <a href="tag.php?id=<?php echo $i + 1 ?>">
                                    <div class='tag cp'>
                                        <input type='hidden' value='<?php echo $i + 1 ?>'>
                                        <?php
                                        if ($i % 10 == 0) {
                                            ?>
                                            <span class='val'>Facebook</span>
                                        <?php } else if ($i % 10 == 1) { ?>
                                            <span class='val'>Google</span>
                                        <?php } else if ($i % 10 == 2) { ?>
                                            <span class='val'>Selena Gomez</span>
                                        <?php } else if ($i % 10 == 3) { ?>
                                            <span class='val'>Apple</span>
                                        <?php } else if ($i % 10 == 4) { ?>
                                            <span class='val'>Shreya Ghoshal</span>
                                        <?php } else if ($i % 10 == 5) { ?>
                                            <span class='val'>iPad</span>
                                        <?php } else if ($i % 10 == 6) { ?>
                                            <span class='val'>Twitter</span>
                                        <?php } else if ($i % 10 == 7) { ?>
                                            <span class='val'>Brad Pitt</span>
                                        <?php } else if ($i % 10 == 8) { ?>
                                            <span class='val'>Android</span>
                                        <?php } else if ($i % 10 == 9) { ?>
                                            <span class='val'>Whatsapp</span>
                                        <?php } ?>
                                    </div>
                                </a>
                            <?php } ?>
                        </div>
                        <div id="mostdesc" class="block whiteblock">
                            <h2>Alexandra Potter's new music video out</h2>
                            <br>
                            <p>The much awaited music video is finally out. Alexandra Potter is all set to roll out in this new video as a crazy vampire who doesn't...</p>
                            <div style='display:table;margin-left:-5px;'>
                                <?php
                                for ($i = 0; $i < 5; $i++) {
                                    ?>
                                    <a href="tag.php?id=<?php echo $i + 1 ?>">
                                        <div class='tag cp'>
                                            <input type='hidden' value='<?php echo $i + 1 ?>'>
                                            <?php
                                            if ($i % 5 == 0) {
                                                ?>
                                                <span class='val'>Facebook</span>
                                            <?php } else if ($i % 5 == 1) { ?>
                                                <span class='val'>Google</span>
                                            <?php } else if ($i % 5 == 2) { ?>
                                                <span class='val'>Selena Gomez</span>
                                            <?php } else if ($i % 5 == 3) { ?>
                                                <span class='val'>Apple</span>
                                            <?php } else if ($i % 5 == 4) { ?>
                                                <span class='val'>Shreya Ghoshal</span>
                                            <?php } ?>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                            <br>
                            <div>
                                <input type='button' class='bbutton' value='View post' style='width:80px;'>
                            </div>
                        </div>
                    </div>
                </div>
                <div id='lpart' class="dshadow">
                    <div id='menudiv'>
                        <ul class='hori_menu' id='menu'>
                            <li class='ctb'>Sets</li>
                            <li>Photos</li>
                            <li>Videos</li>
                            <li>Places</li>
                            <li>Panorama</li>
                        </ul>
                    </div>
                    <div id="setarea">
                        <?php
                        $setarray_length = sizeof($setarray);
                        for ($i = 0; $i < $setarray_length; $i++) {
                            ?>
                            <div class="set gen_hover_shadow" id="s<?php echo $setarray[$i]["id"] ?>">
                                <?php
                                if (($psize = sizeof($setarray[$i]["posts"])) != 0) {
                                    ?>
                                    <div style="background-image:url('<?php echo $setarray[$i]["posts"][0][1] ?>')" class='sgal'></div>
                                <?php } ?>
                                <div class='sinfo'>
                                    <p class='stitle'><a class="black_link" href="set.php?id=<?php echo $setarray[$i]["id"] ?>"><?php echo $setarray[$i]["name"] ?></a></p>
                                    <br>
                                    <p class='setdesc'><?php echo $setarray[$i]["description"] ?></p>
                                    <div class='ratingdiv fl' id='rating<?php echo $setarray[$i]["id"] ?>'><input type='hidden' value='<?php echo $setarray[$i]["rating"] ?>'></div>
                                    <?php
                                    if ($psize != 0) {
                                        ?>
                                        <div class="spreview">
                                            <?php
                                            for ($j = 0; $j < $psize; $j++) {
                                                ?>
                                                <div class='pr' style="background-image:url('<?php echo $setarray[$i]["posts"][$j][1] ?>')"></div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div style="clear:both;">
                                        <input type='button' class='gbutton' value='Follow' style='width:80px;margin-top:10px;'>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <script>
                            $(document).ready(function() {
                                $(".ratingdiv").each(function() {
                                    $(this).ratingWidget({
                                        max: 5,
                                        theme: "gold",
                                        fixed: true
                                    })
                                })
                            })
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="js/jquery.masonry.min.js"></script>    
    <script type="text/javascript" src="js/jquery-ui.js"></script>
</html>
