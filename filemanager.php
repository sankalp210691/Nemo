<?php

//session_start();
//if (!isset($_SESSION['admin_id'])) {
//    header("location:index.php");
//}
//$id = $_SESSION["admin_id"];
//$_SESSION["admin_id"] = $id;
//Keep the above part same, everywhere
set_time_limit(0);

function listFolderFiles($dir, $con) {
    $ffs = scandir($dir);
    echo '<ol>';
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..') {
            echo '<li>' . $ff;
            if (is_dir($dir . '/' . $ff)) {
                listFolderFiles($dir . '/' . $ff, $con);
            } else {
                $interest_name = substr($ff, 0, strlen($ff) - 4);
                $dir_name_array = explode('/', $dir);
                $dir_name = $dir_name_array[3];
                echo "$$ $dir -> " . $dir_name . "<br>";
                if ($interest_name != $dir_name) {
                    $query = "select count(id) from interests where image_src=\"$dir/$ff\"";
                    $result = mysql_query($query);
                    while ($row = mysql_fetch_array($result)) {
                        $c = $row["count(id)"];
                    }
                    if ($c < 1) {
                        $query = "select id from category where name=\"$dir_name\"";
                        $result = mysql_query($query) or die("Error: " . mysql_error());
                        while ($row = mysql_fetch_array($result)) {
                            $category_id = $row['id'];
                        }
                        if ($interest_name == "Thumb") {
                            echo "</li>";
                            continue;
                        }
                        date_default_timezone_set('Asia/Kolkata');
                        $today = date("Y-m-d");
                        $query = "insert into interests(name,category_id,image_src,added_date,description) values(\"$interest_name\",\"$category_id\",\"$dir/$ff\",\"$today\",\"$interest_name\")";
                        mysql_query($query) or die("Error: " . mysql_error());
                        echo "---->" . $query . "<br>";
                    }
                }
            }
            echo '</li>';
        }
    }
    echo '</ol>';
}

function createCategoryTable($dir, $con) {
    $ffs = scandir($dir);
    echo '<ol>';
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..') {
            echo '<li>' . $ff;
            if (is_dir($dir . '/' . $ff)) {
                $query = "select count(id) from category where name=\"$ff\" and image_src=\"$dir/$ff/$ff" . ".jpg" . "\"";
                $result = mysql_query($query);
                while ($row = mysql_fetch_array($result)) {
                    $c = $row["count(id)"];
                }
                if ($c < 1) {
                    $query = "insert into category(name,image_src) values(\"$ff\",\"$dir/$ff/$ff" . ".jpg" . "\")";
                    echo "---->" . $query;
                    mysql_query($query);
                }
            } else {
                
            }
            echo '</li>';
        }
    }
    echo '</ol>';
}

function updateAddress($dir, $con) {
    $ffs = scandir($dir);
//    echo '<ol>';
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..') {
//            echo '<li>' . $ff;
            if (is_dir($dir . '/' . $ff)) {
                updateAddress($dir . '/' . $ff, $con);
            } else {
                $interest_name = substr($ff, 0, strlen($ff) - 4);
                $dir_name_array = explode('/', $dir);
                $dir_name = $dir_name_array[1];
//                echo "$$ $dir -> " . $dir_name . "<br>";
//                if ($interest_name != $dir_name) {
                $query = "select id,image_src from interests where interest_name=\"$interest_name\"";
                $result1 = mysql_query($query);
                while ($row1 = mysql_fetch_array($result1)) {
                    $id = $row1["id"];
                    $image_src = $row1["image_src"];
                    if ("$image_src" != "$dir/$ff") {
                        echo $image_src . "  --> $dir/$ff<br>";
                        $query = "update interests set image_src=\"$dir/$ff\" where id=$id";
                        mysql_query($query) or die("Error: " . mysql_error());
                    }
                }
//                }
            }
//            echo '</li>';
        }
    }
//    echo '</ol>';
}

$con = mysql_connect("localhost", "root", "root");
if (!$con) {
    die("Could not connect: " . mysql_error());
}
mysql_select_db("nemo", $con);
//createCategoryTable('img/interests/category', $con);
//listFolderFiles('img/interests/category', $con);
//updateAddress('interests_dir/Books/Bestsellers', $con);
mysql_close($con);
?>