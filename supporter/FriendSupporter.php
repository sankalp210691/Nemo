<?php

function getRequests($user_id) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,'pending' as req_type from friend f join user u on u.id=f.sent_by where sent_to=? and status=0)";
    $query.="union";
    $query.="(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,'sent' as req_type from friend f join user u on u.id=f.sent_to where sent_by=? and status=0)";
    $statement = $con->prepare($query);
    $statement->bind_param("ii", $user_id, $user_id);
    $statement->bind_result($id, $uid, $name, $profile_pic, $req_type);
    $statement->execute();
    $i = 0;
    $reqarray = array();
    while ($row = $statement->fetch()) {
        if ($profile_pic == null || strlen($profile_pic) == 0) {
            $profile_pic = "img/default_profile_pic.jpg";
        }
        $reqarray[$i] = array(
            "id" => $id,
            "uid" => $uid,
            "name" => $name,
            "profile_pic" => $profile_pic,
            "req_type" => $req_type
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $reqarray;
}

function getFriends($user_id, $start, $limit) {
    if ($user_id == null || $start == null || $limit == null) {
        return array(-1);
    } else if (strlen($user_id) == 0 || $user_id == 0 || strlen($start) == 0 || strlen($limit) == 0) {
        return array(-1);
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $con = $db_connection->getCon();
        $query = "(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,u.sets as sets, u.followers as followers, u.followee as followee from friend f join user u on u.id=f.sent_by where sent_to=? and status>0)";
        $query.="union";
        $query.="(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,u.sets as sets, u.followers as followers, u.followee as followee from friend f join user u on u.id=f.sent_to where sent_by=? and status>0)";
        $statement = $con->prepare($query);
        $statement->bind_param("ii", $user_id, $user_id);
        $statement->bind_result($id, $uid, $name, $profile_pic, $sets, $followers, $followee);
        $statement->execute();
        $i = 0;
        $reqarray = array();
        while ($row = $statement->fetch()) {
            if ($profile_pic == null || strlen($profile_pic) == 0) {
                $profile_pic = "img/default_profile_pic.jpg";
            }
            $reqarray[$i] = array(
                "id" => $id,
                "uid" => $uid,
                "name" => $name,
                "blur_profile_pic" => getBlurPicAddress($profile_pic),
                "profile_pic" => $profile_pic,
                "mutual_friend_count" => getMutualFriendsCount($user_id, $uid),
                "set_count" => $sets,
                "followers"=>$followers,
                "followee"=>$followee
            );
            $i++;
        }
        $statement->close();
        $db_connection->mysqli_connect_close();
        return $reqarray;
    }
}

function areFriends($id1, $id2) {
    $count = 0;
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select id,status from friend where (sent_by =? and sent_to=?) or (sent_by =? and sent_to=?)";
    $statement = $con->prepare($query);
    $statement->bind_param("iiii", $id1, $id2, $id2, $id1);
    $statement->execute();
    $statement->bind_result($id, $status);
    while ($row = $statement->fetch()) {
        $count++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    if ($count > 0) {
        return array(true, $status, $id);
    } else {
        return array(false, "", -1);
    }
}

function addFriend($sent_by, $sent_to) {
    if ($sent_by == $sent_to) {
        return;
    }
    $decArray = areFriends($sent_to, $sent_by);
    if ($decArray[0] === true) {
        return;
    }

    $friend = new Friend();
    $friendcon = new FriendController();
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $friend->setSent_by($sent_by);
    $friend->setSent_to($sent_to);
    $friend->setStatus(0);
    $friendship_id = $friendcon->insert($friend, $persistent_connection);

    $query = "update user set followers=followers+1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $friend->getSent_to());
    $statement->execute();
    $statement->close();

    $usercon = new UserController();
    $user = $usercon->getByPrimaryKey($sent_by, array("sent_friend_request"), null, $persistent_connection);
    $user->setId($sent_by);
    $user->setSent_friend_request($user->getSent_friend_request() + 1);
    $usercon->update($user, $persistent_connection);

    $user = $usercon->getByPrimaryKey($sent_to, array("pending_friend_request"), null, $persistent_connection);
    $user->setId($sent_to);
    $user->setPending_friend_request($user->getPending_friend_request() + 1);
    $usercon->update($user, $persistent_connection);

    $db_connection->mysqli_connect_close();
    return $friendship_id;
}

function cancelRequest($friendship_id) {
    if ($friendship_id == null || $friendship_id < 1) {
        return;
    }

    $friendcon = new FriendController();
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $friend = $friendcon->getByPrimaryKey($friendship_id, array("sent_by", "sent_to"), null, $persistent_connection);
    if ($friend == null) {
        $db_connection->mysqli_connect_close();
        return 2;
    }
    $friendcon->delete($friendship_id, $persistent_connection);
    $sent_by = $friend->getSent_by();
    $sent_to = $friend->getSent_to();

    $query = "update user set followers=followers-1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $friend->getSent_to());
    $statement->execute();
    $statement->close();

    $usercon = new UserController();
    $user = $usercon->getByPrimaryKey($sent_by, array("sent_friend_request"), null, $persistent_connection);
    $user->setId($sent_by);
    $user->setSent_friend_request($user->getSent_friend_request() - 1);
    $usercon->update($user, $persistent_connection);

    $usercon = new UserController();
    $user = $usercon->getByPrimaryKey($sent_to, array("pending_friend_request"), null, $persistent_connection);
    $user->setId($sent_to);
    $user->setPending_friend_request($user->getPending_friend_request() - 1);
    $usercon->update($user, $persistent_connection);
    $db_connection->mysqli_connect_close();
    return 1;
}

function decideRequest($decision, $friendship_id) {
    if ($friendship_id == null || strlen($friendship_id) == 0 || $friendship_id < 1) {
        return 0;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $friendcon = new FriendController();
        $friend = $friendcon->getByPrimaryKey($friendship_id, array("sent_by", "sent_to"), null, $persistent_connection);
        if ($friend == null) {
            $db_connection->mysqli_connect_close();
            return 2;
        }
        if ($decision == "accept") {
            $friend->setId($friendship_id);
            $friend->setStatus(1);
            $friendcon->update($friend, $persistent_connection);

            $query = "update user set friends=friends+1 where id=? or id=?";
            $statement = $persistent_connection->prepare($query);
            $statement->bind_param("i", $friend->getSent_to(), $friend->getSent_by());
            $statement->execute();
            $statement->close();
        } else if ($decision == "reject") {
            $friendcon->delete($friendship_id, $persistent_connection);
        } else {
            return;
        }

        $user = new User();
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($friend->getSent_by(), array("sent_friend_request"), null, $persistent_connection);
        $user->setId($friend->getSent_by());
        if ($user->getSent_friend_request() > 0)
            $user->setSent_friend_request($user->getSent_friend_request() - 1);
        else
            $user->setSent_friend_request(0);
        $usercon->update($user, $persistent_connection);

        $user = new User();
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($friend->getSent_to(), array("pending_friend_request"), null, $persistent_connection);
        $user->setId($friend->getSent_to());
        if ($user->getPending_friend_request() > 0)
            $user->setPending_friend_request($user->getPending_friend_request() - 1);
        else
            $user->setPending_friend_request(0);
        $usercon->update($user, $persistent_connection);

        $db_connection->mysqli_connect_close();
        return 1;
    }
}

function unfriend($friendship_id, $uid, $fuid) {
    if ($friendship_id == null || strlen(trim($friendship_id)) == 0 || $uid == $fuid) {
        return -1;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $friendcon = new FriendController();
        $friendcon->delete($friendship_id, $persistent_connection);

        $query = "update user set friends=friends-1 where id=? or id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("ii", $uid, $fuid);
        $statement->execute();
        $statement->close();

        $db_connection->mysqli_connect_close();
        return 1;
    }
}

function follow($follower_id, $followee_id) {
    if ($follower_id == null || strlen($follower_id) == 0 || $followee_id == null || strlen($followee_id) == 0 || $followee_id == $follower_id) {
        return -1;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $follow = new Follow();
        $followcon = new FollowController();
        $follow->setFollower_id($follower_id);
        $follow->setFollowee_id($followee_id);
        $follow_id = $followcon->insert($follow, $persistent_connection);

        $query = "update user set followers=followers+1 where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $follower_id);
        $statement->execute();
        $statement->close();

        $query = "update user set followee=followee+1 where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $followee_id);
        $statement->execute();
        $statement->close();

        $db_connection->mysqli_connect_close();
        return $follow_id;
    }
}

function unfollow($follow_id,$follower_id,$followee_id) {
    if ($follow_id==null || strlen($follow_id)==0 || $follower_id == null || strlen($follower_id) == 0 || $followee_id == null || strlen($followee_id) == 0 || $followee_id == $follower_id) {
        return -1;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $query = "update user set followers=followers-1 where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $follower_id);
        $statement->execute();
        $statement->close();

        $query = "update user set followee=followee-1 where id=?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param("i", $followee_id);
        $statement->execute();
        $statement->close();
        
        $followcon = new FollowController();
        $followcon->delete($follow_id,$persistent_connection);

        $db_connection->mysqli_connect_close();
        return 1;
    }
}

function getMutualFriendsCount($id1, $id2) {
    if (strlen($id1) == 0 || strlen($id2) == 0 || $id1 < 1 || $id2 < 1 || $id1 == $id2)
        return 0;
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select count(*) from user u where u.id in(select a.friend_id from (select case when sent_by=? then sent_to else sent_by end as friend_id  from friend where sent_by=? or sent_to=? ) a join ( select case when sent_by=? then sent_to else sent_by end as friend_id from friend where sent_by=? or sent_to=? ) b on b.friend_id=a.friend_id)";
    $statement = $con->prepare($query);
    $statement->bind_param("iiiiii", $id1, $id1, $id1, $id2, $id2, $id2);
    $statement->execute();
    $statement->bind_result($count);
    $statement->fetch();
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $count;
}

function getMutualFriends($id1, $id2) {
    if (strlen($id1) == 0 || strlen($id2) == 0 || $id1 < 1 || $id2 < 1 || $id1 == $id2)
        return array(-1);
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $query = "select u.id as id,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic from user u where u.id in(select a.friend_id from (select case when sent_by=? then sent_to else sent_by end as friend_id  from friend where sent_by=? or sent_to=? ) a join ( select case when sent_by=? then sent_to else sent_by end as friend_id from friend where sent_by=? or sent_to=? ) b on b.friend_id=a.friend_id)";
    $statement = $con->prepare($query);
    $statement->bind_param("iiiiii", $id1, $id1, $id1, $id2, $id2, $id2);
    $statement->execute();
    $statement->bind_result($id, $name, $profile_pic);
    $mutual_friends = array();
    $i = 0;
    while ($statement->fetch()) {
        if ($profile_pic == null || strlen($profile_pic) == 0)
            $profile_pic = "img/default_profile_pic.jpg";
        $mutual_friends[$i] = array(
            "id" => $id,
            "name" => $name,
            "profile_pic" => $profile_pic,
            "blur_profile_pic" => getBlurPicAddress($profile_pic)
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $mutual_friends;
}

function searchFriend($uid, $user_id, $format, $text) {
    //use $user_id variable when you assign privacy constraint for searching friends.
    if ($uid == NULL || strlen($uid) < 1 || $uid < 1)
        return array();
    else if (strlen($text) < 3)
        return array();
    else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $con = $db_connection->getCon();
        if ($format == "friend_list") {
            $query = "(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,u.sets as sets from friend f join user u on u.id=f.sent_by where f.status>0 and (u.first_name like ? or u.last_name like ?) and f.sent_to=?)";
            $query.="union";
            $query.="(select f.id as id,u.id as uid,concat(u.first_name,' ',u.last_name) as name,u.profile_pic as profile_pic,u.sets as sets from friend f join user u on u.id=f.sent_to where f.status>0 and (u.first_name like ? or u.last_name like ?) and f.sent_by=?)";
            $statement = $con->prepare($query);
            $text = $text . "%";
            $statement->bind_param("ssissi", $text, $text, $uid, $text, $text, $uid);
            $statement->bind_result($id, $fuid, $name, $profile_pic, $sets);
            $statement->execute();
            $i = 0;
            $reqarray = array();
            while ($row = $statement->fetch()) {
                if ($profile_pic == null || strlen($profile_pic) == 0) {
                    $profile_pic = "img/default_profile_pic.jpg";
                }
                $reqarray[$i] = array(
                    "id" => $id,
                    "uid" => $fuid,
                    "name" => $name,
                    "profile_pic" => $profile_pic,
                    "mutual_friend_count" => getMutualFriendsCount($user_id, $fuid),
                    "set_count" => $sets
                );
                $i++;
            }
        }
        $statement->close();
        $db_connection->mysqli_connect_close();
        return $reqarray;
    }
}

function createGroup($user_id, $name, $group_type, $blocked, $private_post_sharing, $suggest, $list_string) {
    $user_id = trim($user_id);
    $name = trim($name);
    $group_type = trim($group_type);
    $blocked = trim($blocked);
    $private_post_sharing = trim($private_post_sharing);
    $suggest = trim($suggest);
    if (strlen($user_id) == 0 || strlen($name) == 0 || strlen($group_type) == 0 || strlen($blocked) == 0 || strlen($private_post_sharing) == 0 || strlen($suggest) == 0) {
        return -1;
    } else {
        $list = explode(",", $list_string);
        $list_size = sizeof($list);

        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        if ($blocked == "false")
            $blocked = 0;
        else
            $blocked = 1;
        if ($private_post_sharing == "false")
            $private_post_sharing = 0;
        else
            $private_post_sharing = 1;
        if ($suggest == "false")
            $suggest = 0;
        else
            $suggest = 1;

        $groups = new Groups();
        $groups->setName($name);
        $groups->setUser_id($user_id);
        $groups->setGroup_type($group_type);
        $groups->setBlocked($blocked);
        $groups->setPrivate_post_sharing($private_post_sharing);
        $groups->setSuggest($suggest);
        $groupcon = new GroupsController();
        $group_id = $groupcon->insert($groups, $persistent_connection);

        for ($i = 0; $i < $list_size; $i++) {
            $groups_friend = new Groups_friend();
            $groups_friend->setGroup_id($group_id);
            $groups_friend->setUser_id($list[$i]);
            $groups_friendcon = new Groups_friendController();
            $groups_friendcon->insert($groups_friend, $persistent_connection);
        }

        $db_connection->mysqli_connect_close();
        return $group_id;
    }
}

function getUserGroups($user_id, $with_reference) {
    $user_id = trim($user_id);
    if (strlen($user_id) == 0) {
        return array(-1);
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
        $groups = new Groups();
        $groups->setUser_id($user_id);
        $groupscon = new GroupsController();
        $groupss = $groupscon->findByAll($groups, array("id", "name"), null, $persistent_connection);
        $groupss_size = sizeof($groupss);
        $group_array = array();
        for ($i = 0; $i < $groupss_size; $i++) {
            if ($with_reference == 1) {
                $query = "select u.profile_pic from user u join groups_friend gf on gf.user_id=u.id where gf.group_id=" . $groupss[$i]->getId() . " limit 4";
                $statement = $persistent_connection->prepare($query);
                $profile_pic = "";
                $profile_pic_array = array();
                $j = 0;
                $statement->bind_result($profile_pic);
                $statement->execute();
                while ($row = $statement->fetch()) {
                    $profile_pic_array[$j] = $profile_pic;
                    $j++;
                }
                $group_array[$i] = array(
                    "id" => $groupss[$i]->getId(),
                    "name" => $groupss[$i]->getName(),
                    "profile_pic" => $profile_pic_array
                );
            } else {
                $group_array[$i] = array(
                    "id" => $groupss[$i]->getId(),
                    "name" => $groupss[$i]->getName()
                );
            }
        }
        $db_connection->mysqli_connect_close();
        return $group_array;
    }
}

function getGroupDetails($id) {
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();

    $groupscon = new GroupsController();
    $groups = $groupscon->getByPrimaryKey($id, array("*"), null, $persistent_connection);

    $groups_friend = new Groups_friend();
    $groups_friend->setGroup_id($id);
    $groups_friendcon = new Groups_friendController();
    $groups_friends = $groups_friendcon->findByAll($groups_friend, array("user_id"), null, $persistent_connection);
    $groups_friends_size = sizeof($groups_friends);

    $groups_friends_array = array();
    for ($i = 0; $i < $groups_friends_size; $i++) {
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($groups_friends[$i]->getUser_id(), array("first_name", "last_name", "profile_pic"), null, $persistent_connection);

        $profile_pic = $user->getProfile_pic();
        if ($profile_pic == null || strlen($profile_pic) == 0)
            $profile_pic = "img/default_profile_pic.jpg";

        $groups_friends_array[$i] = array(
            "id" => $groups_friends[$i]->getUser_id(),
            "name" => $user->getFirst_name() . " " . $user->getLast_name(),
            "profile_pic" => $profile_pic
        );
    }

    $group_details_array = array(
        "id" => $id,
        "user_id" => $groups->getUser_id(),
        "name" => $groups->getName(),
        "blocked" => $groups->getBlocked(),
        "shared" => $groups->getPrivate_post_sharing(),
        "suggest" => $groups->getSuggest(),
        "type" => $groups->getGroup_type(),
        "group_friends" => $groups_friends_array
    );

    $db_connection->mysqli_connect_close();
    return $group_details_array;
}

function updateGroup($group_id, $name, $user_id, $group_type, $blocked, $private_post_sharing, $suggest, $list_string) {
    $user_id = trim($user_id);
    $name = trim($name);
    $group_type = trim($group_type);
    $blocked = trim($blocked);
    $private_post_sharing = trim($private_post_sharing);
    $suggest = trim($suggest);
    if (strlen($user_id) == 0 || strlen($name) == 0 || strlen($group_type) == 0 || strlen($blocked) == 0 || strlen($private_post_sharing) == 0 || strlen($suggest) == 0) {
        return -1;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();


        if ($blocked == "false")
            $blocked = 0;
        else
            $blocked = 1;
        if ($private_post_sharing == "false")
            $private_post_sharing = 0;
        else
            $private_post_sharing = 1;
        if ($suggest == "false")
            $suggest = 0;
        else
            $suggest = 1;

        $groups = new Groups();
        $groups->setId($group_id);
        $groups->setName($name);
        $groups->setGroup_type($group_type);
        $groups->setBlocked($blocked);
        $groups->setPrivate_post_sharing($private_post_sharing);
        $groups->setSuggest($suggest);
        $groupcon = new GroupsController();
        $groupcon->update($groups, $persistent_connection);

        $new_list = explode(",", $list_string);
        $new_list_size = sizeof($new_list);
        $groups_friend = new Groups_friend();
        $groups_friend->setGroup_id($group_id);
        $groups_friendcon = new Groups_friendController();
        $groups_friends = $groups_friendcon->findByAll($groups_friend, array("id", "user_id"), "order by user_id", $persistent_connection);
        $insert_list = "";
        $delete_list = "";
        $j = 0;
        $k = 0;

        for ($i = 0; $i < $new_list_size; $i++) {
            if ($new_list[$i] > -1) {
                $index = searchBinary($new_list[$i], $groups_friends);
                if ($index > -1) {
                    $groups_friends[$index]->setUser_id(null);
                } else if ($index == -1) {
                    $insert_list.= "(" . $new_list[$i] . "," . $group_id . "),";
                    $j++;
                }
            }
        }

        $groups_friends_size = sizeof($groups_friends);
        for ($i = 0; $i < $groups_friends_size; $i++) {
            if (!is_null($groups_friends[$i]->getUser_id())) {
                $delete_list.= $groups_friends[$i]->getId() . ",";
                $k++;
            }
        }

        if (strlen($insert_list) != 0) {
            $insert_list = substr($insert_list, 0, -1);
            $insert_query = "insert into groups_friend(user_id,group_id) values " . $insert_list;
            $statement = $persistent_connection->prepare($insert_query);
            $statement->execute();
            $statement->close();
        }

        if (strlen($delete_list) != 0) {
            $delete_list = substr($delete_list, 0, -1);
            $delete_query = "delete from groups_friend where id in (" . $delete_list . ")";
            echo $delete_query;
            $statement = $persistent_connection->prepare($delete_query);
            $statement->execute();
            $statement->close();
        }

        $db_connection->mysqli_connect_close();
        return 1;
    }
}

function searchBinary($needle, $array) {
    $array_size = sizeof($array);
    if ($array_size == 0) {
        return -1;
    } else if ($needle == $array[$array_size / 2]->getUser_id()) {
        return $array_size / 2;
    } else {
        $start = 0;
        $end = $array_size - 1;
        while ($end >= $start) {
            $array_size = $end - $start + 1;
            if ($needle < $array[$array_size / 2]->getUser_id()) {
                $end = ($array_size / 2) - 1;
            } else if ($needle > $array[$array_size / 2]->getUser_id()) {
                $start = ($array_size / 2) + 1;
            } else if ($needle == $array[$array_size / 2]->getUser_id()) {
                return $array_size / 2;
            }
        }
        return -1;
    }
}

?>
