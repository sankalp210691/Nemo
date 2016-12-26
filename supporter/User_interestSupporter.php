<?php
function getUserInterests($user_id) {
    if($user_id==null || $user_id<1){
        return array();
    }
    $query = "select interests.id,name,image_src,rank from interests join user_interest on user_interest.interest_id = interests.id where user_interest.user_id=? order by user_interest.rank desc;";
    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $con = $db_connection->getCon();
    $statement = $con->prepare($query);
    $statement->bind_param("i", $user_id);
    $statement->execute();
    $statement->bind_result($interest_id, $interest_name, $image_src, $rank);
    $user_interests = array();
    $i=0;
    while($statement->fetch()){
        $user_interests[$i] = array(
            "interest_id"=>$interest_id,
            "interest_name"=>$interest_name,
            "image_src"=>$image_src,
            "rank"=>$rank
        );
        $i++;
    }
    $statement->close();
    $db_connection->mysqli_connect_close();
    return $user_interests;
}
?>
