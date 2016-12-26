<?php

if (isset($_GET["req"])) {
    $req = $_GET["req"];
    if ($req == null || strlen($req) == 0) {
        header("location:badpage.html");
    } else {
        if (isset($_GET["q"]))
            $query = trim($_GET["q"]);
        else if (isset($_GET["text"]))
            $query = trim($_GET["text"]);
        if ($query == null || strlen($query) == 0) {
            echo json_encode(array());
            return;
        } else {
            require "db/DBConnect.php";
            $sugg = 0;
            if (isset($_GET["sugg"])) {
                $sugg = $_GET["sugg"];
            }
            if ($sugg != 0 && $sugg != 1) {
                header("location:badpage.html");
                return;
            }
            if ($req == "gs") {
                include "req/SpecialFunctions.php";
                if ($sugg == 1) {
                    if (strlen($query) != 3) {
                        echo json_encode(array());
                        return;
                    } else {
                        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
                        $con = $db_connection->getCon();
                        $sqlquery = "select s.query as terms from search s where s.query like ? union select t.name as terms from tags t where t.name like ?";
                        $statement = $con->prepare($sqlquery);
                        $query = $query . "%";
                        $statement->bind_param("ss", $query, $query);
                        $statement->bind_result($terms);
                        $statement->execute();
                        $i = 0;
                        $reqarray = array();
                        while ($row = $statement->fetch()) {
                            $reqarray[$i] = $terms;
                            $i++;
                        }
                        $statement->close();
                        $db_connection->mysqli_connect_close();
                        echo json_encode($reqarray);
                        return;
                    }
                } else {
                    if (isset($_GET["type"]) == false) {
                        echo json_encode(array());
                        return;
                    }
                    $type = $_GET["type"];
                    include('/home/sankalp/Downloads/sphinx-2.1.6-release/api/sphinxapi.php');
                    $cl = new SphinxClient();
                    $cl->SetServer("localhost", 3321);
                    $cl->SetMatchMode(SPH_MATCH_ANY);
                    $cl->SetFieldWeights(array('tag_name' => 50, 'title' => 30, 'description' => 20));
                    if ($type == "php") {
                        $cl->SetFilter("type", array(1));
                    } else if ($type == "vdp") {
                        $cl->SetFilter("type", array(2));
                    } else if ($type == "plp") {
                        $cl->SetFilter("type", array(3));
                    } else if ($type == "pnp") {
                        $cl->SetFilter("type", array(4));
                    } else if ($type == "wlp") {
                        $cl->SetFilter("type", array(5));
                    } else if ($type == "evp") {
                        $cl->SetFilter("type", array(6));
                    } else if ($type == "pop") {
                        $cl->SetFilter("type", array(7));
                    } else if ($type == "app") {
                        $cl->SetFilter("type", array(8));
                    }
                    $result = $cl->Query($query, 'postview_index');
                    if ($result === false) {
                        echo json_encode(array());
                        return;
                    } else {
                        if ($cl->GetLastWarning()) {
                            echo json_encode(array());
                            return;
                        }
                        $ids_str = implode(',', array_keys($result['matches']));
                        $db_connection = new DBConnect("mysql", "nemo", "", "", "");
                        $con = $db_connection->getCon();
                        $sqlquery = "select p.id as id,p.set_id as set_id,p.user_id as user_id,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic as profile_pic,p.title as title,p.description as description,p.src as src,p.url as url,p.url_content_type as url_content_type,p.width as width, p.height as height, p.type as type,p.likes as likes,p.comments as comments,p.shares as shares,p.sharable as sharable,p.date as date,p.time as time from post p join user u on u.id = p.user_id where p.id in ($ids_str)";
                        $res_db = mysql_query($sqlquery);
                        $posts = array();
                        $i = 0;
                        if ($res_db === false) {
                            echo json_encode(array());
                            $db_connection->mysql_connect_close();
                            return;
                        } else {
                            while ($row = mysql_fetch_array($res_db)) {
                                $id = $row["id"];
                                $poster_user_id = $row["user_id"];
                                $set_id = $row["set_id"];
                                $type = $row["type"];
                                $title = $row["title"];
                                $description = $row["description"];
                                $src = $row["src"];
                                $url = $row["url"];
                                $url_content_type = $row["url_content_type"];
                                $width = $row["width"];
                                $height = $row["height"];
                                $date = $row["date"];
                                $time = $row["time"];
                                $likes = $row["likes"];
                                $shares = $row["shares"];
                                $comments = $row["comments"];
                                $sharable = $row["sharable"];
                                $user_name = $row["user_name"];
                                $profile_pic = getBlurPicAddress($row["profile_pic"]);
                                if ($sharable == 1) {
                                    $user_liked = 0;
                                    $db_connection2 = new DBConnect("mysqli", "nemo", "", "", "");
                                    $con2 = $db_connection2->getCon();
                                    $query = "select l.id from likes l where l.user_id=? and l.post_id=?";
                                    $statement2 = $con2->prepare($query);
                                    $statement2->bind_param("ii", $user_id, $id);
                                    $statement2->execute();
                                    $statement2->bind_result($user_liked);
                                    $statement2->fetch();
                                    $statement2->close();
                                    $db_connection2->mysqli_connect_close();

                                    if ($type == "video") {
                                        $src = "users/images/" . md5(video_image($src)) . ".jpg";
                                    }

                                    $posts[$i] = array(
                                        "id" => $id,
                                        "user_name" => $user_name,
                                        "set_id" => $set_id,
                                        "profile_pic" => $profile_pic,
                                        "user_id" => $poster_user_id,
                                        "postType" => $type,
                                        "title" => $title,
                                        "description" => $description,
                                        "src" => $src,
                                        "url" => $url,
                                        "url_content_type" => $url_content_type,
                                        "width" => $width,
                                        "height" => $height,
                                        "date" => $date,
                                        "time" => $time,
                                        "likes" => $likes,
                                        "user_liked" => $user_liked,
                                        "shares" => $shares,
                                        "comments" => $comments,
                                        "popularity_index" => (20 + 1 * ($likes + (1.5 * $shares) + (1.1 * $comments)) / (1 + pow(round(abs(strtotime($date . " " . $time) - strtotime(date('Y-m-d H:i:s'))) / 60, 2), 2)))
                                    );
                                    $i++;
                                }
                            }
                        }
                        $db_connection->mysql_connect_close();
                        echo json_encode($posts);
                    }
                    return;
                }
            } else if ($req == "main") {
                include "req/SpecialFunctions.php";
                include("req/Java.inc");
                $bridgeObject = new java("nemojavabridge.NemoJavaBridge");
                echo $bridgeObject->mainSearch($query,"All",10);
            }
        }
    }
} else {
    header("location:badpage.html");
}
?>