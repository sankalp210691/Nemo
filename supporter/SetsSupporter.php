<?php

function createSet($set_name, $description, $privacy, $category_list, $user_id) {
    if (strlen($set_name) == 0 || strlen($privacy) == 0 || strlen($category_list) == 0 || strlen($user_id) == 0)
        return -1;
    else {
        $sets = new Sets();
        $setscon = new SetsController();

        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
        $sets->setName($set_name);
        $sets->setDescription($description);
        $sets->setPrivacy($privacy);
        $sets->setUser_id($user_id);
        $set_id = $setscon->insert($sets, $persistent_connection);

        $query = "update user set sets=sets+1 where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $user_id);
        $statement->execute();
        $statement->close();

        $category_id_list = explode(",", $category_list);
        $category_id_list_size = sizeof($category_id_list);
        $sets_category = new Sets_category();
        $sets_categorycon = new Sets_categoryController();
        $sets_category->setSet_id($set_id);
        for ($i = 0; $i < $category_id_list_size; $i++) {
            $sets_category->setCategory_id($category_id_list[$i]);
            $sets_categorycon->insert($sets_category, $persistent_connection);
        }
        $db_connection->mysqli_connect_close();
        return $set_id;
    }
}

function getSets($user_id, $get_preview) {
    if (strlen($user_id) == 0 || $user_id == null) {
        return array(-1);
    } else {
        $set = new Sets();
        $setscon = new SetsController();

        $set->setUser_id($user_id);
        $sets = $setscon->findByAll($set, array("id", "name"), null, null);
        $rset = array();
        $sets_size = sizeof($sets);
        $set_ids = "";
        for ($i = 0; $i < $sets_size; $i++) {
            $rset[$i] = array(
                "id" => $sets[$i]->getId(),
                "name" => $sets[$i]->getName()
            );
            $set_ids.="\"" . $sets[$i]->getId() . "\",";
        }
        if ($get_preview == 0)
            return $rset;
        else {
//actually you have to get pics here
            return $rset;
        }
    }
}

function getUserSets($uid, $id, $type, $persistent_connection) {
    $private = 0;   //privacy in the table is set to 1
    if ($uid == $id) {
        $private = 1;
    } else {
        $decArray = areFriends($uid, $id);
        if ($decArray[0] == true || $decArray[1] > 0)
            $private = 2;   //privacy in table could be >= 0
    }
    $sets_array = array();
    $i = 0;
    if ($type == "created") {
        if ($private == 1) {
            $query = "select s.*,-1 from sets s where s.user_id=?";
        } else if ($private == 2) {
            //are friends so show them all except SECRET
            $query = "select s.*,-1 from sets s where s.user_id=? and privacy<>3";     //3 is for SECRET
        } else {
            //not friends, show only public
            $query = "select s.*,-1 from sets s where s.user_id=? and s.privacy=2";
        }
    } else if ($type == "following") {
        if ($private == 1) {
            $query = "select s.*,sf.id from sets s join set_follower sf on sf.set_id=s.id where sf.user_id=?";
        } else if ($private == 2) {
            //are friends so show them all except those followed SECRETLY
            $query = "select s.*,sf.id from sets s join set_follower sf on sf.set_id=s.id where sf.user_id=? and sf.secret=0";     //3 is for SECRET
        } else {
            //not friends, show only public
            $query = "select s.*,sf.id from sets s join set_follower sf on sf.set_id=s.id where s.user_id=? and s.privacy=2";
        }
    } else {
        return array();
    }

    $id_str = "";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $uid);
    $statement->execute();
    $statement->bind_result($set_id, $name, $description, $post_count, $rating, $followers, $views, $privacy, $user_id, $date, $time, $follow_id);
    while ($statement->fetch()) {
        $id_str.=$set_id . ",";
        $sets_array[$i] = array(
            "id" => $set_id,
            "name" => $name,
            "description" => $description,
            "post_count" => $post_count,
            "rating" => $rating,
            "followers" => $followers,
            "views" => $views,
            "privacy" => $privacy,
            "user_id" => $user_id,
            "date" => $date,
            "time" => $time,
            "follow_id" => $follow_id
        );
        $i++;
    }
    $statement->close();
    if ($uid != $id) {
        if ($i > 0) {
            $id_str = substr($id_str, 0, -1);
            $query = "select id from set_follower where set_id in ($id_str) and user_id=$id";
            $statement = $persistent_connection->prepare($query);
            $statement->execute();
            $statement->bind_result($follow_id);
            $j = 0;
            while ($statement->fetch()) {
                $sets_array[$j]["follow_id"] = $follow_id;
                $j++;
            }
        }
    }
    for ($j = 0; $j < $i; $j++) {
        $sets_array[$j]["display_pics"] = getSetDisplayPics($sets_array[$j]["id"], 3, $persistent_connection);
    }
    return $sets_array;
}

function getSetDisplayPics($set_id, $limit, $persistent_connection) {
    $query = "select p.src,p.type,p.height,p.width from post p where set_id = ? limit ?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("ii", $set_id, $limit);
    $statement->execute();
    $statement->bind_result($src, $type, $height, $width);
    $posts = array();
    $i = 0;
    while ($statement->fetch()) {
        if ($type == "video") {
            $src = "users/images/" . md5(video_image($src)) . ".jpg";
        }
        $posts[$i] = array(
            "src" => $src,
            "width" => $width,
            "height" => $height,
        );
        $i++;
    }
    $statement->close();
    return $posts;
}

function editSet($set_id, $set_name) {
    if ($set_id == null || strlen($set_id) == 0 || $set_name == null || strlen($set_name) == 0)
        return -1;
    $sets = new Sets();
    $sets->setId($set_id);
    $sets->setName($set_name);
    $setscon = new SetsController();
    return $setscon->update($sets, null);
}

function getSetCategories($set_id, $persistent_connection) {
    if ($set_id == null || strlen($set_id) == 0)
        return array(-1);
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "select c.id,name,image_src from category c join sets_category sc on sc.category_id=c.id where sc.set_id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $set_id);
    $statement->execute();
    $statement->bind_result($id, $name, $image_src);
    $category_array = array();
    $i = 0;
    while ($statement->fetch()) {
        $category_array[$i] = array(
            "id" => $id,
            "name" => $name,
            "image_src" => $image_src
        );
        $i++;
    }
    if ($persistent_connection == null) {
        $db_connection->mysqli_connect_close();
    }
    return $category_array;
}
?>
