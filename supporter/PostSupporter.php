<?php

function createPost($postObject) {
    $type = $postObject->postType;
    $set = $postObject->set;
    if ($type == null || strlen($type) == 0 || $set == null || strlen($set) == 0)
        return -1;
    $title = $postObject->title;
    $description = $postObject->description;
    $interest_tags = array();
    $friend_tags = array();
    if (property_exists($postObject, 'interest_tag'))
        $interest_tags = $postObject->interest_tag;
    if (property_exists($postObject, 'friend_tags'))
        $friend_tags = $postObject->friend_tag;
    $src = $postObject->src;
    $user_id = $postObject->user_id;

    $post = new Post();
    if ($type == "photo") {
        $post->setWidth($postObject->width);
        $post->setHeight($postObject->height);
    }

    if ($src == null || strlen($src) == 0 || $user_id == null || strlen($user_id) == 0)
        return -1;

    if ($type == "video") {
        $url = video_image($src);
        if ($url != -1)
            fetchImage($url);
        else
            return -1;
    }else if ($type == "link") {
        $post->setUrl_content_type($postObject->url_content_type);
        $post->setUrl($postObject->url);
        if ($postObject->url_content_type == "photo") {
            $post->setWidth($postObject->width);
            $post->setHeight($postObject->height);
        }
    }

    $post->setTitle($title);
    $post->setSet_id($set);
    $post->setDescription($description);
    $post->setSrc($src);
    $post->setType($type);
    $post->setUser_id($user_id);
    $timestamp = explode(" ", microtime())[1];
    $post->setScore(getPostScore($timestamp, array(
        "likes" => 0,
        "comments" => 0,
        "shares" => 0
                    ), "recalculate"));
    $postcon = new PostController();
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $post_id = $postcon->insert($post, $persistent_connection);

    $sets = new Sets();
    $setscon = new SetsController();
    $sets = $setscon->getByPrimaryKey($set, array("post_count"), null, $persistent_connection);
    $sets->setPost_count($sets->getPost_count() + 1);
    $setscon->update($sets, $persistent_connection);

    if (sizeof($friend_tags) > 0) {
        $friend_post = new Friend_post();
        $friend_postcon = new Friend_postController();
        $friend_post->setPost_id($post_id);
        foreach ($friend_tags as $friend_id) {
            $friend_post->setFriend_id($friend_id);
            $friend_postcon->insert($friend_post, $persistent_connection);
        }
    }

    if (($interest_tags_length = sizeof($interest_tags)) > 0) {
        $tags = array();
        $tagcon = new TagsController();
        $post_tagscon = new Post_tagsController();
        for ($i = 0; $i < $interest_tags_length; $i++) {
            $tag_id = $interest_tags[$i][0];
            $tag_name = $interest_tags[$i][1];
            if ($tag_id == null || strlen($tag_id) == 0) {
                $tag = new Tags();
                $tag->setName($tag_name);
                $tags = $tagcon->findByAll($tag, array("id"), null, $persistent_connection);
                if (sizeof($tags) > 0)
                    $tag_id = $tags[0]->getId();
                else {
                    $tag_id = $tagcon->insert($tag, $persistent_connection);
                }
            }
            $post_tags = new Post_tags();
            $post_tags->setPost_id($post_id);
            $post_tags->setTag_id($tag_id);
            $post_tagscon->insert($post_tags, $persistent_connection);
        }
    }
    $db_connection->mysqli_connect_close();
    return $post_id;
}

function getWorldFeed($user_id, $start, $limit) {
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $tag_array = array();
    //get followed tag list
    $query = "select tf.tag_id from tag_follower tf where tf.user_id=? order by tf.score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($tag_id);
    $i = 0;
    while ($statement->fetch()) {
        $tag_array[$i] = $tag_id;
        $i++;
    }
    $statement->close();
    $sorted_tag_array = mergeSort($tag_array, $i);
    $sorted_tag_array_size = $i;

    //get tags user has associated with
    $query = "select t.id,count(t.id) as frequency from tags t join post_tags pt on pt.tag_id=t.id join post p on pt.post_id=p.id where p.user_id=? group by t.id order by frequency desc,t.score limit 100";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($tag_id, $tag_frequency);
    while ($statement->fetch()) {
        if ($sorted_tag_array_size > 0) {
            if (checkDuplicate($tag_id, $sorted_tag_array, 0, $sorted_tag_array_size - 1) == false) {
                $tag_array[$i] = $tag_id;
                $i++;
            }
        } else {
            $tag_array[$i] = $tag_id;
            $i++;
        }
    }
    $statement->close();
    if ($i > 0)
        $tag_string = implode(",", $tag_array);
    //get followed sets
    $query = "select set_id from set_follower where user_id=? order by score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($set_id);
    $set_string = "";
    while ($statement->fetch()) {
        $set_string.=$set_id . ",";
    }
    $statement->close();
    if (strlen($set_string) != 0) {
        $set_string = substr($set_string, 0, -1);
    }
    //get posts associated with tags
    $query = "select distinct a.* from (select  p.*,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic from post p ";
    if ($i > 0) {
        $query.= "join user u on u.id=p.user_id join post_tags pt on pt.post_id = p.id where 1=1 and (pt.tag_id in (" . $tag_string . ") ";
        if (strlen($set_string) > 0)
            $query.= "or p.set_id in (" . $set_string . ")) and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
        else
            $query.=") and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
    }
    else {
        $query.= "join user u on u.id=p.user_id join post_tags pt on pt.post_id = p.id where 1=1 and ";
        $query.="p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
    }
    $statement = $con->prepare($query);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $comments, $score, $sharable, $commentable, $user_name, $profile_pic);
    $i = 0;
    $posts = array();
    while ($statement->fetch()) {
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

        $profile_pic = getBlurPicAddress($profile_pic);

        if ($type == "share") {
            $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
            $statement2 = $con2->prepare($query);
            $statement2->bind_param("i", $user_id);
            $statement2->execute();
            $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
            $statement2->fetch();
            $statement2->close();
            if ($parent_type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }
            $posts[$i] = array(
                "id" => $id,
                "user_name" => $user_name,
                "set_id" => $set_id,
                "parent_type" => $parent_type,
                "parent_user_id" => $parent_user_id,
                "parent_user_name" => $parent_user_name,
                "profile_pic" => $profile_pic,
                "user_id" => $user_id,
                "share_id" => $share_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "share_text" => $share_text,
                "src" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "privacy" => $privacy,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "user_liked" => $user_liked,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable,
                "popularity_index" => (20 + 1 * ($likes + (1.5 * $shares) + (1.1 * $comments)) / (1 + pow(round(abs(strtotime($date . " " . $time) - strtotime(date('Y-m-d H:i:s'))) / 60, 2), 2)))
            );
            $i++;
            continue;
        } else if ($type == "video") {
            $src = "users/images/" . md5(video_image($src)) . ".jpg";
        }

        $posts[$i] = array(
            "id" => $id,
            "user_name" => $user_name,
            "set_id" => $set_id,
            "profile_pic" => $profile_pic,
            "user_id" => $user_id,
            "share_id" => $share_id,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "share_text" => $share_text,
            "src" => $src,
            "url" => $url,
            "url_content_type" => $url_content_type,
            "width" => $width,
            "height" => $height,
            "privacy" => $privacy,
            "date" => $date,
            "time" => $time,
            "likes" => $likes,
            "user_liked" => $user_liked,
            "shares" => $shares,
            "comments" => $comments,
            "sharable" => $sharable,
            "popularity_index" => (20 + 1 * ($likes + (1.5 * $shares) + (1.1 * $comments)) / (1 + pow(round(abs(strtotime($date . " " . $time) - strtotime(date('Y-m-d H:i:s'))) / 60, 2), 2)))
        );
        $i++;
    }

    $statement->close();
    $db_connection->mysqli_connect_close();
    return $posts;
}

function getFriendFeed($user_id, $start, $limit) {
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $tag_array = array();
    //get followed tag list
    $query = "select tf.tag_id from tag_follower tf where tf.user_id=? order by tf.score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($tag_id);
    $i = 0;
    while ($statement->fetch()) {
        $tag_array[$i] = $tag_id;
        $i++;
    }
    $statement->close();
    $sorted_tag_array = mergeSort($tag_array, $i);
    $sorted_tag_array_size = $i;

    //get tags user has associated with
    $query = "select t.id,count(t.id) as frequency from tags t join post_tags pt on pt.tag_id=t.id join post p on pt.post_id=p.id where p.user_id=? group by t.id order by frequency desc,t.score limit 100";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($tag_id, $tag_frequency);
    while ($statement->fetch()) {
        if ($sorted_tag_array_size > 0) {
            if (checkDuplicate($tag_id, $sorted_tag_array, 0, $sorted_tag_array_size - 1) == false) {
                $tag_array[$i] = $tag_id;
                $i++;
            }
        } else {
            $tag_array[$i] = $tag_id;
            $i++;
        }
    }
    $statement->close();
    $tag_string = "";
    if ($i > 0)
        $tag_string = implode(",", $tag_array);
    //get followed sets
    $query = "select set_id from set_follower where user_id=? order by score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($set_id);
    $set_string = "";
    while ($statement->fetch()) {
        $set_string.=$set_id . ",";
    }
    $statement->close();
    if (strlen($set_string) != 0) {
        $set_string = substr($set_string, 0, -1);
    }
    //get friend's user ids
    $query = "select sent_by as friend_id from friend where sent_to=? and status=1 union select sent_to as friend_id from friend where sent_by=? and status=1";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $user_id, $user_id);
    $statement->execute();
    $statement->bind_result($friend_id);
    $friend_string = "";
    while ($statement->fetch()) {
        $friend_string.=$friend_id . ",";
    }
    if (strlen($friend_id) > 0)
        $friend_string = substr($friend_string, 0, -1);
    else {
        $statement->close();
        $db_connection->mysqli_connect_close();
        return array();
    }
    //get posts associated with tags
    $query = "select distinct a.* from (select  p.*,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic from post p "
            . "join user u on u.id=p.user_id join post_tags pt on pt.post_id = p.id where user_id in (" . $friend_string . ") ";
    if (strlen($tag_string) > 0) {
        $query.=" and (pt.tag_id in (" . $tag_string . ") ";
    }
    if (strlen($set_string) > 0)
        $query.= "or p.set_id in (" . $set_string . ")) and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
    else if (strlen($tag_string) > 0)
        $query.=") and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
    else
        $query.=" and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
    $statement = $con->prepare($query);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $comments, $score, $sharable, $commentable, $user_name, $profile_pic);
    $i = 0;
    $posts = array();
    while ($statement->fetch()) {
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

        $profile_pic = getBlurPicAddress($profile_pic);

        if ($type == "share") {
            $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
            $statement2 = $con2->prepare($query);
            $statement2->bind_param("i", $user_id);
            $statement2->execute();
            $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
            $statement2->fetch();
            $statement2->close();
            if ($parent_type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }
            $posts[$i] = array(
                "id" => $id,
                "user_name" => $user_name,
                "set_id" => $set_id,
                "parent_type" => $parent_type,
                "parent_user_id" => $parent_user_id,
                "parent_user_name" => $parent_user_name,
                "profile_pic" => $profile_pic,
                "user_id" => $user_id,
                "share_id" => $share_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "share_text" => $share_text,
                "src" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "privacy" => $privacy,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "user_liked" => $user_liked,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable
            );
            $i++;
            continue;
        } else if ($type == "video") {
            $src = "users/images/" . md5(video_image($src)) . ".jpg";
        }

        $posts[$i] = array(
            "id" => $id,
            "user_name" => $user_name,
            "set_id" => $set_id,
            "profile_pic" => $profile_pic,
            "user_id" => $user_id,
            "share_id" => $share_id,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "share_text" => $share_text,
            "src" => $src,
            "url" => $url,
            "url_content_type" => $url_content_type,
            "width" => $width,
            "height" => $height,
            "privacy" => $privacy,
            "date" => $date,
            "time" => $time,
            "likes" => $likes,
            "user_liked" => $user_liked,
            "shares" => $shares,
            "comments" => $comments,
            "sharable" => $sharable
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
}

function getFollowingFeed($user_id, $start, $limit) {
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();

    //get users following
    $follow = new Follow();
    $followcon = new FollowController();
    $follow->setFollower_id($user_id);
    $follows = $followcon->findByAll($follow, array("followee_id"), "order by score desc", $con);
    $follows_size = sizeof($follows);
    $follower_string = "";
    for ($i = 0; $i < $follows_size; $i++) {
        $follower_string.=$follows[$i]->getFollowee_id() . ",";
    }
    if ($i > 0)
        $follower_string = substr($follower_string, 0, -1);

    //get followed tag list
    $tag_string = "";
    $query = "select tf.tag_id from tag_follower tf where tf.user_id=? order by tf.score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($tag_id);
    while ($statement->fetch()) {
        $tag_string.= $tag_id . ",";
    }
    $tag_string_size = strlen($tag_string);
    if ($tag_string_size != 0) {
        $tag_string = substr($tag_string, 0, -1);
    }
    $statement->close();

    //get followed sets
    $query = "select set_id from set_follower where user_id=? order by score desc";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($set_id);
    $set_string = "";
    while ($statement->fetch()) {
        $set_string.=$set_id . ",";
    }
    $set_string_size = strlen($set_string);
    if ($set_string_size != 0) {
        $set_string = substr($set_string, 0, -1);
    }
    $statement->close();

    if ($follows_size == 0 && $tag_string_size == 0 && $set_string_size == 0) {
        $db_connection->mysqli_connect_close();
        return array();
    } else {
        $query = "select distinct a.* from (select  p.*,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic from post p "
                . "join user u on u.id=p.user_id join post_tags pt on pt.post_id = p.id where 1=1";
        if ($follows_size > 0) {
            $query.=" and p.user_id in (\"" . $follower_string . "\")";
        }
        if ($tag_string_size > 0) {
            $query.=" and pt.tag_id in (\"" . $tag_string . "\")";
        }
        if ($set_string_size > 0) {
            $query.=" and pt.tag_id in (\"" . $set_string . "\")";
        }
        $query.=" and p.type<>\"share\" and privacy=2 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
        $statement = $con->prepare($query);
        $statement->execute();
        $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $comments, $score, $sharable, $commentable, $user_name, $profile_pic);
        $i = 0;
        $posts = array();
        while ($statement->fetch()) {
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

            $profile_pic = getBlurPicAddress($profile_pic);

            if ($type == "share") {
                $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                $statement2 = $con2->prepare($query);
                $statement2->bind_param("i", $user_id);
                $statement2->execute();
                $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
                $statement2->fetch();
                $statement2->close();
                if ($parent_type == "video") {
                    $src = "users/images/" . md5(video_image($src)) . ".jpg";
                }
                $posts[$i] = array(
                    "id" => $id,
                    "user_name" => $user_name,
                    "set_id" => $set_id,
                    "parent_type" => $parent_type,
                    "parent_user_id" => $parent_user_id,
                    "parent_user_name" => $parent_user_name,
                    "profile_pic" => $profile_pic,
                    "user_id" => $user_id,
                    "share_id" => $share_id,
                    "postType" => $type,
                    "title" => $title,
                    "description" => $description,
                    "share_text" => $share_text,
                    "src" => $src,
                    "url" => $url,
                    "url_content_type" => $url_content_type,
                    "width" => $width,
                    "height" => $height,
                    "privacy" => $privacy,
                    "date" => $date,
                    "time" => $time,
                    "likes" => $likes,
                    "user_liked" => $user_liked,
                    "shares" => $shares,
                    "comments" => $comments,
                    "sharable" => $sharable
                );
                $i++;
                continue;
            } else if ($type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }

            $posts[$i] = array(
                "id" => $id,
                "user_name" => $user_name,
                "set_id" => $set_id,
                "profile_pic" => $profile_pic,
                "user_id" => $user_id,
                "share_id" => $share_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "share_text" => $share_text,
                "src" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "privacy" => $privacy,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "user_liked" => $user_liked,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable,
                "popularity_index" => (20 + 1 * ($likes + (1.5 * $shares) + (1.1 * $comments)) / (1 + pow(round(abs(strtotime($date . " " . $time) - strtotime(date('Y-m-d H:i:s'))) / 60, 2), 2)))
            );
            $i++;
        }
        $statement->close();
        $db_connection->mysqli_connect_close();
    }
}

function getPrivateFeed($user_id, $start, $limit) {
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();

    //get friend ids
    $query = "(select u.id as uid from friend f join user u on u.id=f.sent_by where sent_to=? and status=1)";
    $query.="union";
    $query.="(select u.id as uid from friend f join user u on u.id=f.sent_to where sent_by=? and status=1)";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $user_id, $user_id);
    $statement->bind_result($friend_id);
    $statement->execute();
    $friend_string = "";
    while ($statement->fetch()) {
        $friend_string.=$friend_id . ",";
    }
    if (strlen($friend_string) == 0) {
        $statement->close();
        $db_connection->mysqli_connect_close();
        return array();
    } else {
        $friend_string = substr($friend_string, 0, -1);
        $query = "select distinct a.* from (select  p.*,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic from post p "
                . "join user u on u.id=p.user_id join post_tags pt on pt.post_id = p.id where p.user_id in (\"" . $friend_string . "\") and ";
        $query.="p.type<>\"share\" and privacy=1 order by p.score desc,p.date desc,p.time desc limit $start,$limit) a";
        $statement = $con->prepare($query);
        $statement->execute();
        $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $comments, $score, $sharable, $commentable, $user_name, $profile_pic);
        $i = 0;
        $posts = array();
        while ($statement->fetch()) {
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

            $profile_pic = getBlurPicAddress($profile_pic);

            if ($type == "share") {
                $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                $statement2 = $con2->prepare($query);
                $statement2->bind_param("i", $user_id);
                $statement2->execute();
                $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
                $statement2->fetch();
                $statement2->close();
                if ($parent_type == "video") {
                    $src = "users/images/" . md5(video_image($src)) . ".jpg";
                }
                $posts[$i] = array(
                    "id" => $id,
                    "user_name" => $user_name,
                    "set_id" => $set_id,
                    "parent_type" => $parent_type,
                    "parent_user_id" => $parent_user_id,
                    "parent_user_name" => $parent_user_name,
                    "profile_pic" => $profile_pic,
                    "user_id" => $user_id,
                    "share_id" => $share_id,
                    "postType" => $type,
                    "title" => $title,
                    "description" => $description,
                    "share_text" => $share_text,
                    "src" => $src,
                    "url" => $url,
                    "url_content_type" => $url_content_type,
                    "width" => $width,
                    "height" => $height,
                    "privacy" => $privacy,
                    "date" => $date,
                    "time" => $time,
                    "likes" => $likes,
                    "user_liked" => $user_liked,
                    "shares" => $shares,
                    "comments" => $comments,
                    "sharable" => $sharable
                );
                $i++;
                continue;
            } else if ($type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }

            $posts[$i] = array(
                "id" => $id,
                "user_name" => $user_name,
                "set_id" => $set_id,
                "profile_pic" => $profile_pic,
                "user_id" => $user_id,
                "share_id" => $share_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "share_text" => $share_text,
                "src" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "privacy" => $privacy,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "user_liked" => $user_liked,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable,
                "popularity_index" => (20 + 1 * ($likes + (1.5 * $shares) + (1.1 * $comments)) / (1 + pow(round(abs(strtotime($date . " " . $time) - strtotime(date('Y-m-d H:i:s'))) / 60, 2), 2)))
            );
            $i++;
        }
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
}

function getFeed($user_id, $start, $limit) {
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }

    $query = "select a.* from (select p.*,concat(u.first_name,\" \",u.last_name) as user_name,u.profile_pic from post p join user u on u.id=p.user_id where (p.user_id in (select case when sent_by=? then sent_to else sent_by end as friend_id  from friend where sent_by=? or sent_to=? ) and (((p.privacy=1 or p.privacy=2) and u.id not in (select nst.user_id from not_shared_to nst where nst.post_id = p.id)) or ( p.privacy=3 and u.id in (select st.user_id from shared_to st where st.post_id=p.id) ))) or p.user_id=?) a order by a.score desc limit $start,$limit";

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $statement = $con->prepare($query);
    $statement->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $comments, $score, $sharable, $commentable, $user_name, $profile_pic);
    $i = 0;
    $posts = array();
    while ($statement->fetch()) {
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

        $profile_pic = getBlurPicAddress($profile_pic);

        if ($type == "share") {
            $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
            $statement2 = $con2->prepare($query);
            $statement2->bind_param("i", $user_id);
            $statement2->execute();
            $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
            $statement2->fetch();
            $statement2->close();
            if ($parent_type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            }
            $posts[$i] = array(
                "id" => $id,
                "user_name" => $user_name,
                "set_id" => $set_id,
                "parent_type" => $parent_type,
                "parent_user_id" => $parent_user_id,
                "parent_user_name" => $parent_user_name,
                "profile_pic" => $profile_pic,
                "user_id" => $user_id,
                "share_id" => $share_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "share_text" => $share_text,
                "src" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "privacy" => $privacy,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "user_liked" => $user_liked,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable
            );
            $i++;
            continue;
        } else if ($type == "video") {
            $src = "users/images/" . md5(video_image($src)) . ".jpg";
        }

        $posts[$i] = array(
            "id" => $id,
            "user_name" => $user_name,
            "set_id" => $set_id,
            "profile_pic" => $profile_pic,
            "user_id" => $user_id,
            "share_id" => $share_id,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "share_text" => $share_text,
            "src" => $src,
            "url" => $url,
            "url_content_type" => $url_content_type,
            "width" => $width,
            "height" => $height,
            "privacy" => $privacy,
            "date" => $date,
            "time" => $time,
            "likes" => $likes,
            "user_liked" => $user_liked,
            "shares" => $shares,
            "comments" => $comments,
            "sharable" => $sharable
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $posts;
}

function getUserFeed($user_id, $uid, $start, $limit) {
    $user_id = trim($user_id);
    $uid = trim($uid);
    if ($user_id == null || strlen($user_id) == 0 || is_numeric($user_id) == false) {
        return array(-1);
    }
    $posts = array();
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();

    if ($uid == $user_id) {
        //get all  posts
        $user = new User();
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($user_id, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
        $user_name = $user->getFirst_name() . " " . $user->getLast_name();
        $profile_pic = "";
        $profile_pic.=getBlurPicAddress($user->getProfile_pic());

        $post = new Post();
        $postcon = new PostController();
        $post->setUser_id($user_id);
        $posts_obj = $postcon->findByAll($post, array("*"), "order by id desc limit " . $start . "," . $limit, $persistent_connection);
        $posts_length = sizeof($posts_obj);

        for ($i = 0; $i < $posts_length; $i++) {
            $likes = new Likes();
            $likescon = new LikesController();
            $likes->setPost_id($posts_obj[$i]->getId());
            $likes->setUser_id($user_id);
            $likes_array = $likescon->findByAll($likes, array("id"), null, $persistent_connection);
            $likes_array_size = sizeof($likes_array);
            if ($likes_array_size == 0) {
                $user_liked = 0;
            } else {
                $user_liked = $likes_array[0]->getId();
            }

            $src = $posts_obj[$i]->getSrc();
            $type = $posts_obj[$i]->getType();

            if ($type == "video") {
                $src = "users/images/" . md5(video_image($src)) . ".jpg";
            } else if ($type == "share") {
                $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                $statement = $persistent_connection->prepare($query);
                $statement->bind_param("i", $user_id);
                $statement->execute();
                $statement->bind_result($parent_type, $parent_user_id, $parent_user_name);
                $statement->fetch();
                $statement->close();
                if ($parent_type == "video") {
                    $src = "users/images/" . md5(video_image($src)) . ".jpg";
                }
                $posts[$i] = array(
                    "id" => $posts_obj[$i]->getId(),
                    "user_id" => $posts_obj[$i]->getUser_id(),
                    "set_id" => $posts_obj[$i]->getSet_id(),
                    "parent_type" => $parent_type,
                    "parent_user_id" => $parent_user_id,
                    "parent_user_name" => $parent_user_name,
                    "user_name" => $user_name,
                    "profile_pic" => $profile_pic,
                    "postType" => $type,
                    "share_id" => $posts_obj[$i]->getShare_id(),
                    "title" => $posts_obj[$i]->getTitle(),
                    "description" => $posts_obj[$i]->getDescription(),
                    "share_text" => $posts_obj[$i]->getShare_text(),
                    "src" => $src,
                    "url" => $posts_obj[$i]->getUrl(),
                    "url_content_type" => $posts_obj[$i]->getUrl_content_type(),
                    "width" => $posts_obj[$i]->getWidth(),
                    "height" => $posts_obj[$i]->getHeight(),
                    "date" => $posts_obj[$i]->getDate(),
                    "time" => $posts_obj[$i]->getTime(),
                    "likes" => $posts_obj[$i]->getLikes(),
                    "shares" => $posts_obj[$i]->getShares(),
                    "comments" => $posts_obj[$i]->getComments(),
                    "sharable" => $posts_obj[$i]->getSharable(),
                    "user_liked" => $user_liked
                );
                continue;
            }

            $posts[$i] = array(
                "id" => $posts_obj[$i]->getId(),
                "user_id" => $posts_obj[$i]->getUser_id(),
                "set_id" => $posts_obj[$i]->getSet_id(),
                "user_name" => $user_name,
                "profile_pic" => $profile_pic,
                "postType" => $type,
                "share_id" => $posts_obj[$i]->getShare_id(),
                "title" => $posts_obj[$i]->getTitle(),
                "description" => $posts_obj[$i]->getDescription(),
                "share_text" => $posts_obj[$i]->getShare_text(),
                "src" => $src,
                "url" => $posts_obj[$i]->getUrl(),
                "url_content_type" => $posts_obj[$i]->getUrl_content_type(),
                "width" => $posts_obj[$i]->getWidth(),
                "height" => $posts_obj[$i]->getHeight(),
                "date" => $posts_obj[$i]->getDate(),
                "time" => $posts_obj[$i]->getTime(),
                "likes" => $posts_obj[$i]->getLikes(),
                "shares" => $posts_obj[$i]->getShares(),
                "comments" => $posts_obj[$i]->getComments(),
                "sharable" => $posts_obj[$i]->getSharable(),
                "user_liked" => $user_liked
            );
        }
    } else {
        $decArray = areFriends($uid, $user_id);
        if ($decArray[0] == false || ($decArray[0] == true && ($decArray[1] == "0" || $decArray[1] == "3"))) {
            //public posts only
            $user = new User();
            $usercon = new UserController();
            $user = $usercon->getByPrimaryKey($user_id, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
            $user_name = $user->getFirst_name() . " " . $user->getLast_name();
            $profile_pic = "";
            $profile_pic.=getBlurPicAddress($user->getProfile_pic());

            $post = new Post();
            $postcon = new PostController();
            $post->setUser_id($user_id);
            $post->setPrivacy(2);   //very important. 2 = public only
            $posts_obj = $postcon->findByAll($post, array("*"), "order by id desc limit " . $start . "," . $limit, $persistent_connection);
            $posts_length = sizeof($posts_obj);

            for ($i = 0; $i < $posts_length; $i++) {
                $likes = new Likes();
                $likescon = new LikesController();
                $likes->setPost_id($posts_obj[$i]->getId());
                $likes->setUser_id($uid);
                $likes_array = $likescon->findByAll($likes, array("id"), null, null);
                $likes_array_size = sizeof($likes_array);
                if ($likes_array_size == 0) {
                    $user_liked = 0;
                } else {
                    $user_liked = $likes_array[0]->getId();
                }

                $src = $posts_obj[$i]->getSrc();
                $type = $posts_obj[$i]->getType();
                if ($type == "video") {
                    $src = "users/images/" . md5(video_image($src)) . ".jpg";
                } else if ($type == "share") {
                    $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                    $statement = $persistent_connection->prepare($query);
                    $statement->bind_param("i", $user_id);
                    $statement->execute();
                    $statement->bind_result($parent_type, $parent_user_id, $parent_user_name);
                    $statement->fetch();
                    $statement->close();
                    if ($parent_type == "video") {
                        $src = "users/images/" . md5(video_image($src)) . ".jpg";
                    }
                    $posts[$i] = array(
                        "id" => $posts_obj[$i]->getId(),
                        "user_id" => $posts_obj[$i]->getUser_id(),
                        "set_id" => $posts_obj[$i]->getSet_id(),
                        "parent_type" => $parent_type,
                        "parent_user_id" => $parent_user_id,
                        "parent_user_name" => $parent_user_name,
                        "user_name" => $user_name,
                        "profile_pic" => $profile_pic,
                        "postType" => $type,
                        "share_id" => $posts_obj[$i]->getShare_id(),
                        "title" => $posts_obj[$i]->getTitle(),
                        "description" => $posts_obj[$i]->getDescription(),
                        "share_text" => $posts_obj[$i]->getShare_text(),
                        "src" => $src,
                        "url" => $posts_obj[$i]->getUrl(),
                        "url_content_type" => $posts_obj[$i]->getUrl_content_type(),
                        "width" => $posts_obj[$i]->getWidth(),
                        "height" => $posts_obj[$i]->getHeight(),
                        "date" => $posts_obj[$i]->getDate(),
                        "time" => $posts_obj[$i]->getTime(),
                        "likes" => $posts_obj[$i]->getLikes(),
                        "shares" => $posts_obj[$i]->getShares(),
                        "comments" => $posts_obj[$i]->getComments(),
                        "sharable" => $posts_obj[$i]->getSharable(),
                        "user_liked" => $user_liked
                    );
                    continue;
                }

                $posts[$i] = array(
                    "id" => $posts_obj[$i]->getId(),
                    "user_id" => $posts_obj[$i]->getUser_id(),
                    "set_id" => $posts_obj[$i]->getSet_id(),
                    "user_name" => $user_name,
                    "profile_pic" => $profile_pic,
                    "postType" => $posts_obj[$i]->getType(),
                    "share_id" => $posts_obj[$i]->getShare_id(),
                    "title" => $posts_obj[$i]->getTitle(),
                    "description" => $posts_obj[$i]->getDescription(),
                    "share_text" => $posts_obj[$i]->getShare_text(),
                    "src" => $src,
                    "url" => $posts_obj[$i]->getUrl(),
                    "url_content_type" => $posts_obj[$i]->getUrl_content_type(),
                    "width" => $posts_obj[$i]->getWidth(),
                    "height" => $posts_obj[$i]->getHeight(),
                    "date" => $posts_obj[$i]->getDate(),
                    "time" => $posts_obj[$i]->getTime(),
                    "likes" => $posts_obj[$i]->getLikes(),
                    "shares" => $posts_obj[$i]->getShares(),
                    "comments" => $posts_obj[$i]->getComments(),
                    "sharable" => $posts_obj[$i]->getSharable(),
                    "user_liked" => $user_liked
                );
            }
        } else {
            if ($decArray[1] == "1" || $decArray[1] == "2") {
                //show all posts keeping in mind the privacy
                $user = new User();
                $usercon = new UserController();
                $user = $usercon->getByPrimaryKey($user_id, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
                $user_name = $user->getFirst_name() . " " . $user->getLast_name();
                $profile_pic = getBlurPicAddress($user->getProfile_pic());

                $query = "select p.* from post p left join not_shared_to nst on nst.post_id=p.id where p.user_id=? and (p.privacy=2 or (p.privacy=1 and (select count(*) from not_shared_to nst where nst.user_id=?)=0)  or (p.privacy=3 and (select count(*) from shared_to st where st.user_id=?)>0)) order by p.id desc limit $start,$limit";
                $statement = $persistent_connection->prepare($query);
                $statement->bind_param("iii", $user_id, $uid, $uid);
                $statement->execute();
                $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable);
                $i = 0;

                while ($statement->fetch()) {
                    $likes = new Likes();
                    $likescon = new LikesController();
                    $likes->setPost_id($id);
                    $likes->setUser_id($uid);
                    $likes_array = $likescon->findByAll($likes, array("id"), null, null);
                    $likes_array_size = sizeof($likes_array);
                    if ($likes_array_size == 0) {
                        $user_liked = 0;
                    } else {
                        $user_liked = $likes_array[0]->getId();
                    }


                    if ($type == "video") {
                        $src = "users/images/" . md5(video_image($src)) . ".jpg";
                    } else if ($type == "share") {
                        $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                        $statement2 = $persistent_connection->prepare($query);
                        $statement2->bind_param("i", $user_id);
                        $statement2->execute();
                        $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
                        $statement2->fetch();
                        $statement2->close();
                        if ($parent_type == "video") {
                            $src = "users/images/" . md5(video_image($src)) . ".jpg";
                        }
                        $posts[$i] = array(
                            "id" => $id,
                            "user_name" => $user_name,
                            "set_id" => $set_id,
                            "parent_type" => $parent_type,
                            "parent_user_id" => $parent_user_id,
                            "parent_user_name" => $parent_user_name,
                            "profile_pic" => $profile_pic,
                            "user_id" => $user_id,
                            "share_id" => $share_id,
                            "postType" => $type,
                            "title" => $title,
                            "description" => $description,
                            "share_text" => $share_text,
                            "src" => $src,
                            "url" => $url,
                            "url_content_type" => $url_content_type,
                            "width" => $width,
                            "height" => $height,
                            "privacy" => $privacy,
                            "date" => $date,
                            "time" => $time,
                            "likes" => $likes,
                            "user_liked" => $user_liked,
                            "shares" => $shares,
                            "comments" => $comments,
                            "sharable" => $sharable
                        );
                        continue;
                    }

                    $posts[$i] = array(
                        "id" => $id,
                        "user_name" => $user_name,
                        "set_id" => $set_id,
                        "profile_pic" => $profile_pic,
                        "user_id" => $user_id,
                        "share_id" => $share_id,
                        "postType" => $type,
                        "title" => $title,
                        "description" => $description,
                        "share_text" => $share_text,
                        "src" => $src,
                        "url" => $url,
                        "url_content_type" => $url_content_type,
                        "width" => $width,
                        "height" => $height,
                        "privacy" => $privacy,
                        "date" => $date,
                        "time" => $time,
                        "likes" => $likes,
                        "user_liked" => $user_liked,
                        "shares" => $shares,
                        "comments" => $comments,
                        "sharable" => $sharable
                    );
                    $i++;
                }
                $statement->close();
            } else if ($decArray[1] == "4") {
                //not blocked but cant see private posts unless tagged
                $user = new User();
                $usercon = new UserController();
                $user = $usercon->getByPrimaryKey($user_id, array("first_name", "last_name", "profile_pic"), null, $persistent_connection);
                $user_name = $user->getFirst_name() . " " . $user->getLast_name();
                $profile_pic.=getBlurPicAddress($user->getProfile_pic());
                $profile_pic = getBlurPicAddress($profile_pic);

                $query = "select p.* from post p left join friend_post fp on fp.post_id=p.id where p.user_id=? and (p.privacy=2 or (p.privacy=1 and (select count(*) from friend_post fp where fp.friend_id=?)>0))";
                $statement = $persistent_connection->prepare($query);
                $statement->bind_param("ii", $user_id, $uid);
                $statement->execute();
                $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable, $user_name, $profile_pic);
                $i = 0;

                while ($statement->fetch()) {
                    $user_liked = 0;
                    $db_connection2 = new DBConnect("mysqli", "nemo", "", "", "");
                    $con2 = $db_connection2->getCon();
                    $query = "select l.id from likes l where l.user_id=? and l.post_id=?";
                    $statement2 = $con2->prepare($query);
                    $statement2->bind_param("ii", $uid, $id);
                    $statement2->execute();
                    $statement2->bind_result($user_liked);
                    $statement2->fetch();
                    $statement2->close();

                    if ($type == "video") {
                        $src = "users/images/" . md5(video_image($src)) . ".jpg";
                    } else if ($type == "share") {
                        $query = "select p.type,p.user_id,concat(u.first_name,\"\",u.last_name) from post p join user u on u.id=p.user_id where p.id=?";
                        $statement2 = $persistent_connection->prepare($query);
                        $statement2->bind_param("i", $user_id);
                        $statement2->execute();
                        $statement2->bind_result($parent_type, $parent_user_id, $parent_user_name);
                        $statement2->fetch();
                        $statement2->close();
                        if ($parent_type == "video") {
                            $src = "users/images/" . md5(video_image($src)) . ".jpg";
                        }
                        $posts[$i] = array(
                            "id" => $id,
                            "user_name" => $user_name,
                            "set_id" => $set_id,
                            "parent_type" => $parent_type,
                            "parent_user_id" => $parent_user_id,
                            "parent_user_name" => $parent_user_name,
                            "profile_pic" => $profile_pic,
                            "user_id" => $user_id,
                            "share_id" => $share_id,
                            "postType" => $type,
                            "title" => $title,
                            "description" => $description,
                            "share_text" => $share_text,
                            "src" => $src,
                            "url" => $url,
                            "url_content_type" => $url_content_type,
                            "width" => $width,
                            "height" => $height,
                            "privacy" => $privacy,
                            "date" => $date,
                            "time" => $time,
                            "likes" => $likes,
                            "user_liked" => $user_liked,
                            "shares" => $shares,
                            "comments" => $comments,
                            "sharable" => $sharable
                        );
                        continue;
                    }

                    $posts[$i] = array(
                        "id" => $id,
                        "user_name" => $user_name,
                        "set_id" => $set_id,
                        "profile_pic" => $profile_pic,
                        "user_id" => $user_id,
                        "share_id" => $share_id,
                        "postType" => $type,
                        "title" => $title,
                        "description" => $description,
                        "share_text" => $share_text,
                        "src" => $src,
                        "url" => $url,
                        "url_content_type" => $url_content_type,
                        "width" => $width,
                        "height" => $height,
                        "privacy" => $privacy,
                        "date" => $date,
                        "time" => $time,
                        "likes" => $likes,
                        "user_liked" => $user_liked,
                        "shares" => $shares,
                        "comments" => $comments,
                        "sharable" => $sharable
                    );
                    $i++;
                }
                $statement->close();
            }
        }
    }

    $db_connection->mysqli_connect_close();
    return $posts;
}

function getUserAlbums($user_id, $start, $limit) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select * from post where type=\"album\" and user_id=?
union
select * from post where type=\"photo\" and user_id=? order by id desc limit 1;";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $user_id, $user_id);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable);
    $albums = array();
    $i = 1;
    $albums[0] = array(
        "id" => -1,
        "user_id" => $user_id,
        "postType" => "album",
        "title" => "Wall Photos",
        "description" => "",
        "src" => "",
        "url" => "",
        "url_content_type" => "",
        "width" => "",
        "height" => "",
        "date" => "",
        "time" => "",
        "likes" => "",
        "shares" => "",
        "comments" => "",
        "sharable" => 0
    );
    while ($statement->fetch()) {
        if ($type == "photo") {
            $albums[0]["src"] = $src;
        } else {
            $albums[$i] = array(
                "id" => $id,
                "user_id" => $user_id,
                "postType" => $type,
                "title" => $title,
                "description" => $description,
                "src1" => $src,
                "url" => $url,
                "url_content_type" => $url_content_type,
                "width" => $width,
                "height" => $height,
                "date" => $date,
                "time" => $time,
                "likes" => $likes,
                "shares" => $shares,
                "comments" => $comments,
                "sharable" => $sharable
            );
            $i++;
        }
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $albums;
}

function getAlbumPhotos($user_id, $album_id, $start, $limit) {
    if ($album_id == -1) {
        $post = new Post();
        $post->setUser_id($user_id);
        $post->setType("photo");
        $postcon = new PostController();
        $posts = $postcon->findByAll($post, array("*"), "order by id desc limit $start, $limit", null);
        $posts_size = sizeof($posts);
        $post_array = array();
        $i = 0;
        while ($i < $posts_size) {
            $post_array[$i] = array(
                "id" => $posts[$i]->getId(),
                "postType" => $posts[$i]->getType(),
                "src" => $posts[$i]->getSrc(),
                "url" => $posts[$i]->getUrl(),
                "url_content_type" => $posts[$i]->getUrl_content_type(),
                "width" => $posts[$i]->getWidth(),
                "height" => $posts[$i]->getHeight(),
                "user_id" => $posts[$i]->getUser_id(),
                "title" => $posts[$i]->getTitle(),
                "description" => $posts[$i]->getDescription(),
                "date" => $posts[$i]->getDate(),
                "time" => $posts[$i]->getTime(),
                "likes" => $posts[$i]->getLikes(),
                "shares" => $posts[$i]->getShares(),
                "comments" => $posts[$i]->getComments(),
                "sharable" => $posts[$i]->getSharable(),
            );
            $i++;
        }
    } else {
        //get pics from the album table based on album id
    }
    return $post_array;
}

function getUserVideos($user_id, $start, $limit) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select * from post where type=\"video\" and user_id=?
union
select * from post where type=\"video\" and user_id=? order by id desc limit ? , ?;";
    $statement = $con->prepare($query);
    $statement->bind_param("iiii", $user_id, $user_id, $start, $limit);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable);
    $videos = array();
    $i = 0;
    while ($statement->fetch()) {
        $videos[$i] = array(
            "id" => $id,
            "user_id" => $user_id,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "src" => "users/images/" . md5(video_image($src)) . ".jpg",
            "url" => $url,
            "url_content_type" => $url_content_type,
            "width" => $width,
            "height" => $height,
            "date" => $date,
            "time" => $time,
            "likes" => $likes,
            "shares" => $shares,
            "comments" => $comments,
            "sharable" => $sharable
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $videos;
}

function getUserWeblinks($user_id, $start, $limit) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select * from post where type=\"link\" and user_id=?
union
select * from post where type=\"link\" and user_id=? order by id desc limit ? , ?;";
    $statement = $con->prepare($query);
    $statement->bind_param("iiii", $user_id, $user_id, $start, $limit);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable);
    $links = array();
    $i = 0;
    while ($statement->fetch()) {
        $links[$i] = array(
            "id" => $id,
            "user_id" => $user_id,
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
            "shares" => $shares,
            "comments" => $comments,
            "sharable" => $sharable
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $links;
}

function getPost($id) {
    if ($id == null || strlen($id) == 0 || is_numeric($id) == false)
        return array(-1);

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select p.*,u.first_name,u.last_name,u.profile_pic from post p join user u on u.id = p.user_id where p.id=?";
    $statement = $con->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $likes, $shares, $score, $comments, $sharable, $commentable, $first_name, $last_name, $profile_pic);
    $statement->fetch();
    $statement->close();
    $db_connection->mysqli_connect_close();

    return array(
        "id" => $id,
        "user_id" => $user_id,
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
        "shares" => $shares,
        "comments" => $comments,
        "sharable" => $sharable,
        "first_name" => $first_name,
        "last_name" => $last_name,
        "profile_pic" => $profile_pic
    );
}

function getPostDetail($id, $uid, $persistent_connection) {
    if ($id == null || strlen($id) == 0 || is_numeric($id) == false)
        return array(-1);
    if ($persistent_connection == null) {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
    }
    $query = "select p.*,u.first_name,u.last_name,u.profile_pic from post p join user u on u.id = p.user_id where p.id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->bind_result($id, $user_id, $set_id, $type, $share_id, $title, $description, $share_text, $src, $url, $url_content_type, $width, $height, $privacy, $date, $time, $no_of_likes, $no_of_shares, $score, $no_of_comments, $sharable, $commentable, $first_name, $last_name, $profile_pic);
    $statement->fetch();
    $statement->close();

    $query = "select t.id,t.name from tags t join post_tags pt on pt.tag_id=t.id where pt.post_id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $tag = array();
    $i = 0;
    $statement->bind_result($tag_id, $tag_name);
    while ($statement->fetch()) {
        $tag[$i] = array(
            "id" => $tag_id,
            "name" => $tag_name
        );
        $i++;
    }
    $statement->close();

    $query = "select l.id from likes l where l.user_id=? and l.post_id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("ii", $uid, $id);
    $statement->execute();
    $statement->bind_result($user_liked);
    $statement->fetch();
    $statement->close();
    if ($user_liked == null)
        $user_liked = 0;

    $commentscon = new CommentsController();
    $comment = new Comments();
    $comment->setPost_id($id);
    $comments = $commentscon->findByAll($comment, array("*"), "order by id desc limit 10", $persistent_connection);
    $comments_array = array();
    $comments_size = sizeof($comments);
    for ($i = 0; $i < $comments_size; $i++) {
        $comment_user = new User();
        $comment_usercon = new UserController();
        $comment_user = $comment_usercon->getByPrimaryKey($comments[$i]->getUser_id(), array("first_name", "last_name", "profile_pic"), null, $persistent_connection);

        $comments_array[$i] = array(
            "id" => $comments[$i]->getId(),
            "user_id" => $comments[$i]->getUser_id(),
            "user_name" => $comment_user->getFirst_name() . " " . $comment_user->getLast_name(),
            "profile_pic" => getBlurPicAddress($comment_user->getProfile_pic()),
            "post_id" => $comments[$i]->getPost_id(),
            "type" => $comments[$i]->getType(),
            "comment" => $comments[$i]->getComment(),
            "likes" => $comments[$i]->getLikes(),
            "date" => $comments[$i]->getDate(),
            "time" => $comments[$i]->getTime()
        );
    }

    $activity = array(
        "no_of_likes" => $no_of_likes,
        "no_of_comments" => $no_of_comments,
        "no_of_shares" => $no_of_shares,
        "user_liked" => $user_liked,
        "comments" => $comments_array,
        "uid" => $uid,
        "id" => $id
    );

    if ($type == "photo") {
        $main_post = array(
            "id" => $id,
            "user_id" => $user_id,
            "set_id" => $set_id,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "src" => $src,
            "width" => $width,
            "height" => $height,
            "date" => $date,
            "time" => $time,
            "sharable" => $sharable,
            "commentable" => $commentable,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "profile_pic" => $profile_pic,
            "tags" => $tag,
            "activity" => $activity
        );
    } else if ($type == "video") {
        $url = video_image($src);
        $image_url = parse_url($src);
        if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com') {
            $width = "480";
            $height = "360";
        }
        $main_post = array(
            "id" => $id,
            "user_id" => $user_id,
            "set_id" => $set_id,
            "preview" => $url,
            "postType" => $type,
            "title" => $title,
            "description" => $description,
            "src" => $src,
            "width" => $width,
            "height" => $height,
            "date" => $date,
            "time" => $time,
            "sharable" => $sharable,
            "commentable" => $commentable,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "profile_pic" => $profile_pic,
            "tags" => $tag,
            "activity" => $activity
        );
    } else if ($type == "link") {
        $main_post = array(
            "id" => $id,
            "user_id" => $user_id,
            "set_id" => $set_id,
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
            "sharable" => $sharable,
            "commentable" => $commentable,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "profile_pic" => $profile_pic,
            "tags" => $tag,
            "activity" => $activity
        );
    }

    $set_post_array = array();
    $postcon = new PostController();
    $post = new Post();
    $post->setSet_id($set_id);
    $posts = $postcon->findByAll($post, array("id", "src", "type", "width", "height","url_content_type"), "order by score desc limit 0,30", $persistent_connection);
    $posts_size = sizeof($posts);

    $k = 0;
    for ($j = 0; $j < $posts_size; $j++) {
        if($posts[$j]->getId()==$id){continue;}
        if ($posts[$j]->getType() == "photo" || ($posts[$j]->getType() == "link" && $posts[$j]->getUrl_content_type() == "photo")) {
            $set_post_array[$k] = array(
                "id" => $posts[$j]->getId(),
                "src" => $posts[$j]->getSrc(),
                "type" => $posts[$j]->getType(),
                "width" => $posts[$j]->getWidth(),
                "height" => $posts[$j]->getHeight()
            );
        } else if ($posts[$j]->getType() == "video" || ($posts[$j]->getType() == "link" && $posts[$j]->getUrl_content_type() == "video")) {
            $url = "users/images/" . md5(video_image($posts[$j]->getSrc())) . ".jpg";
            $image_url = parse_url($posts[$j]->getSrc());
            if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com') {
                $width = "480";
                $height = "360";
            }
            $set_post_array[$k] = array(
                "id" => $posts[$j]->getId(),
                "src" => $url,
                "type" => $posts[$j]->getType(),
                "width" => $width,
                "height" => $height
            );
        }
        $k++;
    }
    
    $query = "select p.id, p.type, p.src, p.width, p.height,p.url_content_type from post p join post_tags pt on pt.post_id=p.id where pt.tag_id in (select ptin.tag_id from post_tags ptin where ptin.post_id=?) group by p.id order by p.id desc limit 0,30";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->bind_result($post_id, $post_type, $post_src, $width, $height,$url_content_type);
    $tag_post_array = array();
    $i = 0;
    while ($statement->fetch()) {
        if($post_id==$id){continue;}
        if ($post_type == "photo" || ($post_type=="link" && $url_content_type=="photo")) {
            $tag_post_array[$i] = array(
                "id" => $post_id,
                "postType" => $post_type,
                "src" => $post_src,
                "width" => $width,
                "height" => $height
            );
        } else if ($post_type == "video" || ($post_type=="link" && $url_content_type=="video")) {
            $url = "users/images/" . md5(video_image($post_src)) . ".jpg";
            $image_url = parse_url($post_src);
            if ($image_url['host'] == 'www.youtube.com' || $image_url['host'] == 'youtube.com') {
                $width = "480";
                $height = "360";
            }
            $tag_post_array[$i] = array(
                "id" => $post_id,
                "postType" => $post_type,
                "src" => $url,
                "width" => $width,
                "height" => $height
            );
        }
        $i++;
    }
    $statement->close();
    if ($persistent_connection == null)
        $db_connection->mysqli_connect_close();
    return array(
        "main_post" => $main_post,
        "set_post_array" => $set_post_array,
        "tag_post_array" => $tag_post_array
    );
}

function likePost($id, $user_id) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();

    $like = new Likes();
    $likecon = new LikesController();
    $like->setPost_id($id);
    $like->setUser_id($user_id);
    $likes = $likecon->findByAll($like, array("id"), null, $persistent_connection);
    if (sizeof($likes) == 0)
        $like_id = $likecon->insert($like, $persistent_connection);
    else
        return $likes[0]->getId();

    $postcon = new PostController();
    $post = $postcon->getByPrimaryKey($id, array("type", "share_id", "likes", "score", "date", "time"), null, $persistent_connection);

    $time = explode(":", $post->getTime());
    $date = explode("-", $post->getDate());
    $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

    $query = "update post set likes=likes+1,score=score+" . getPostScore($timestamp, null, "like_update") . " where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $id);
    $statement->execute();
    $statement->close();

    if ($post->getType() == "share") {
        $share_id = $post->getShare_id();
        $post = $postcon->getByPrimaryKey($share_id, array("score", "date", "time"), null, $persistent_connection);

        $time = explode(":", $post->getTime());
        $date = explode("-", $post->getDate());
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

        $query = "update post set score=score+" . getPostScore($timestamp, null, "like_update") . " where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $share_id);
        $statement->execute();
        $statement->close();
    }

    $db_connection->mysqli_connect_close();
    return $like_id;
}

function unlikePost($post_id, $like_id) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();

    $likecon = new LikesController();
    $like = $likecon->getByPrimaryKey($like_id, array("id"), null, $persistent_connection);
    if ($like->getId() != null)
        $likecon->delete($like_id, $persistent_connection);
    else
        return;

    $postcon = new PostController();
    $post = $postcon->getByPrimaryKey($post_id, array("type", "share_id", "likes", "score", "date", "time"), null, $persistent_connection);

    $time = explode(":", $post->getTime());
    $date = explode("-", $post->getDate());
    $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

    $query = "update post set likes=likes-1,score=score-" . getPostScore($timestamp, null, "like_update") . " where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $post_id);
    $statement->execute();
    $statement->close();

    if ($post->getType() == "share") {
        $share_id = $post->getShare_id();
        $post = $postcon->getByPrimaryKey($share_id, array("score", "date", "time"), null, $persistent_connection);

        $time = explode(":", $post->getTime());
        $date = explode("-", $post->getDate());
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

        $query = "update post set score=score-" . getPostScore($timestamp, null, "like_update") . " where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $share_id);
        $statement->execute();
        $statement->close();
    }

    $db_connection->mysqli_connect_close();
}

function postComment($post_id, $user_id, $comment, $type) {
    try {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $comments = new Comments();
        $commentscon = new CommentsController();
        $comments->setComment($comment);
        $comments->setType($type);
        $comments->setPost_id($post_id);
        $comments->setUser_id($user_id);
        echo $commentscon->insert($comments, $persistent_connection);

        $postcon = new PostController();
        $post = $postcon->getByPrimaryKey($post_id, array("type", "share_id", "comments", "score", "date", "time"), null, $persistent_connection);

        $time = explode(":", $post->getTime());
        $date = explode("-", $post->getDate());
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

        $query = "update post set comments=comments+1,score=score+" . getPostScore($timestamp, null, "comment_update") . " where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $post_id);
        $statement->execute();
        $statement->close();

        if ($post->getType() == "share") {
            $share_id = $post->getShare_id();
            $post = $postcon->getByPrimaryKey($share_id, array("score", "date", "time"), null, $persistent_connection);

            $time = explode(":", $post->getTime());
            $date = explode("-", $post->getDate());
            $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

            $query = "update post set score=score+" . getPostScore($timestamp, null, "comment_update") . " where id=?";
            $statement = $persistent_connection->prepare($query);
            $statement->bind_param("i", $share_id);
            $statement->execute();
            $statement->close();
        }

        $db_connection->mysqli_connect_close();
    } catch (Exception $exc) {
        echo "-1";
    }
}

function mergeSort($array, $size) {
    // Only process if we're not down to one piece of data
    if (count($array) > 1) {

        // Find out the middle of the current data set and split it there to obtain to halfs
        $array_middle = round($size / 2, 0, PHP_ROUND_HALF_DOWN);
        // and now for some remergeSort  cursive magic
        $array_part1 = mergeSort(array_slice($array, 0, $array_middle), $array_middle);
        $array_part2 = mergeSort(array_slice($array, $array_middle, $size), $size - $array_middle);

        // Setup counters so we can remember which piece of data in each half we're looking at
        $counter1 = $counter2 = 0;

        // iterate over all pieces of the currently processed array, compare size & reassemble
        for ($i = 0; $i < $size; $i++) {
            // if we're done processing one half, take the rest from the 2nd half
            if ($counter1 == count($array_part1)) {
                $array[$i] = $array_part2[$counter2];
                ++$counter2;
                // if we're done with the 2nd half as well or as long as pieces in the first half are still smaller than the 2nd half
            } elseif (($counter2 == count($array_part2)) or ($array_part1[$counter1] < $array_part2[$counter2])) {
                $array[$i] = $array_part1[$counter1];
                ++$counter1;
            } else {
                $array[$i] = $array_part2[$counter2];
                ++$counter2;
            }
        }
    }
    return $array;
}

function sharePost($postObject) {
    if ($postObject == null) {
        return 0;
    } else {
        $parent_id = $postObject->id;
        $sharer_id = $postObject->sharer_id;
        $share_text = $postObject->share_text;
        $set_id = $postObject->set;
        $tag_array = $postObject->tag;
        $is_sharable = $postObject->sharable;
        $is_commentable = $postObject->commentable;

        if (($parent_id == null && strlen($parent_id) == 0) || ($set_id == null && strlen($set_id) == 0) || ($sharer_id == null && strlen($sharer_id) == 0) || ($set_id == null && strlen($set_id) == 0) || ($is_sharable == null && strlen($is_sharable) == 0) || ($is_commentable == null && strlen($is_commentable) == 0)) {
            return 0;
        }

        if (!filter_var($parent_id, FILTER_VALIDATE_INT) || !filter_var($set_id, FILTER_VALIDATE_INT) || !filter_var($sharer_id, FILTER_VALIDATE_INT) || !filter_var($is_sharable, FILTER_VALIDATE_INT) || !filter_var($is_commentable, FILTER_VALIDATE_INT)) {
            return 0;
        }

        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $parent_postcon = new PostController();
        $parent_post = $parent_postcon->getByPrimaryKey($parent_id, array("id", "type", "sharable", "title", "description", "src", "url", "url_content_type", "width", "height", "date", "time", "shares", "score"), null, $persistent_connection);
        if ($parent_post->getSharable() == "0" || $parent_post->getType() == "share") {
            $db_connection->mysqli_connect_close();
            return 0;   //not allowed to share
        }
        $time = explode(":", $parent_post->getTime());
        $date = explode("-", $parent_post->getDate());
        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

        $query = "update post set shares=shares+1,score=score+" . getPostScore($timestamp, null, "share_update") . " where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $parent_id);
        $statement->execute();
        $statement->close();

        $post = new Post();
        $post->setUser_id($sharer_id);
        $post->setSet_id($set_id);
        $post->setShare_id($parent_id);
        $post->setShare_text($share_text);
        $post->setType("share");
        $post->setDescription($parent_post->getDescription());
        $post->setHeight($parent_post->getHeight());
        $post->setWidth($parent_post->getWidth());
        $post->setSharable($is_sharable);
        $post->setCommentable($is_commentable);
        $post->setSrc($parent_post->getSrc());
        $post->setTitle($parent_post->getTitle());
        $post->setUrl($parent_post->getUrl());
        $post->setSrc($parent_post->getSrc());
        $post->setUrl_content_type($parent_post->getUrl_content_type());
        $postcon = new PostController();
        $share_id = $postcon->insert($post, $persistent_connection);

        //TAGS FOR SHARED POSTS
//        if (($interest_tags_length = sizeof($tag_array)) > 0) {
//            $tags = array();
//            $tagcon = new TagsController();
//            $post_tagscon = new Post_tagsController();
//            for ($i = 0; $i < $interest_tags_length; $i++) {
//                $tag_id = $tag_array[$i][0];
//                $tag_name = $tag_array[$i][1];
//                if ($tag_id == null || strlen($tag_id) == 0) {
//                    $tag = new Tags();
//                    $tag->setName($tag_name);
//                    $tags = $tagcon->findByAll($tag, array("id"), null, $persistent_connection);
//                    if (sizeof($tags) > 0)
//                        $tag_id = $tags[0]->getId();
//                    else {
//                        $tag_id = $tagcon->insert($tag, $persistent_connection);
//                    }
//                }
//                $post_tags = new Post_tags();
//                $post_tags->setPost_id($share_id);
//                $post_tags->setTag_id($tag_id);
//                $post_tagscon->insert($post_tags, $persistent_connection);
//            }
//        }

        $db_connection->mysqli_connect_close();
        return $share_id;
    }
}

function checkDuplicate($element, $array, $start_index, $end_index) {
    $mid = ($end_index + $start_index) / 2;
    if ($element == null || $start_index > $end_index || ($start_index == $end_index && $element != $array[$mid])) {
        return false;
    } else {
        if ($element == $array[$mid]) {
            return true;
        } else if ($element < $array[$mid]) {
            return checkDuplicate($element, $array, $start_index, $mid - 1);
        } else {
            return checkDuplicate($element, $array, $mid + 1, $end_index);
        }
    }
}

function getPostScore($timestamp, $data_array, $purpose) {
    $f = 20;
    $a = 1;
    $D = pow(10, 4);
    $wl = 1;
    $wc = 1.5;
    $ws = 1.1;
    if ($purpose == "like_update") {
        return ($a * $timestamp * $wl) / $D;
    } else if ($purpose == "comment_update") {
        return ($a * $timestamp * $wc) / $D;
    } else if ($purpose == "share_update") {
        return ($a * $timestamp * $ws) / $D;
    } else if ($purpose == "recalculate") {
        return ($f + $a * (($wl * $data_array["likes"]) + ($wc * $data_array["comments"]) + ($ws * $data_array["shares"]))) * $timestamp / $D;
    } else {
        return 0;
    }
}

function getQuickPosts($user_id,$start,$limit,$persistent_connection){
    $post = new Post();
    $post->setUser_id($user_id);
    $postcon = new PostController();
    $posts = $postcon->findByAll($post, array("id","src","type","url_content_type","title"), " and type<> \"share\" order by id desc limit $start,$limit", $persistent_connection);
    $post_array = array();
    $posts_size = sizeof($posts);
    for($i=0;$i<$posts_size;$i++){
        $post_array[$i] = array(
            "id"=>$posts[$i]->getId(),
            "src"=>$posts[$i]->getSrc(),
            "type"=>$posts[$i]->getType(),
            "url_content_type"=>$posts[$i]->getUrl_content_type(),
            "title"=>$posts[$i]->getTitle()
        );
    }
    return $post_array;
}

?>