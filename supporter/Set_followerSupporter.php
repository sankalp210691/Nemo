<?php

function followSet($user_id, $set_id) {
    if ($user_id == null || $set_id == null) {
        return -1;
    }

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $query = "update sets set followers=followers+1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i", $set_id);
    $statement->execute();
    $statement->close();

    $set_follower = new Set_follower();
    $set_followercon = new Set_followerController();
    $set_follower->setSet_id($set_id);
    $set_follower->setUser_id($user_id);
    $follow_id = $set_followercon->insert($set_follower, $persistent_connection);
    $db_connection->mysqli_connect_close();

    return $follow_id;
}

function unfollowSet($follow_id) {
    if ($follow_id == null) {
        return -1;
    }

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    
    $set_followercon = new Set_followerController();
    $set_follower = $set_followercon->getByPrimaryKey($follow_id, array("set_id"), null, $persistent_connection);
    
    $query = "update sets set followers=followers-1 where id=?";
    $statement = $persistent_connection->prepare($query);
    $statement->bind_param("i",  $set_follower->getSet_id());
    $statement->execute();
    $statement->close();
    
    $set_followercon->delete($follow_id, null);
    $db_connection->mysqli_connect_close();
}

function getFollowers($set_id, $persistent_connection) {
    $set_follower = new Set_follower();
    $set_follower->setSet_id($set_id);
    $set_followercon = new Set_followerController();
    $set_followers = $set_followercon->findByAll($set_follower, array("user_id"), "", $persistent_connection);
    $set_followers_size = sizeof($set_followers);
    if ($set_followers_size > 12)
        $set_followers_size = 12;

    $users = array();
    $usercon = new UserController();
    for ($i = 0; $i < $set_followers_size; $i++) {
        $user = $usercon->getByPrimaryKey($set_followers[$i]->getUser_id(), array("id", "first_name", "last_name", "profile_pic"), null, $persistent_connection);
        $profile_pic = $user->getProfile_pic();
        if ($profile_pic == null || strlen($profile_pic) == 0)
            $profile_pic = "img/default_profile_pic.jpg";
        $users[$i] = array(
            "id" => $user->getId(),
            "name" => $user->getFirst_name() . " " . $user->getLast_name(),
            "profile_pic" => $profile_pic
        );
    }
    return $users;
}

?>
