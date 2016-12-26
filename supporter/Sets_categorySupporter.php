<?php

function getSetsByCategory($cid, $start, $limit, $persistent_connection) {
    if ($cid == null || strlen($cid) == 0) {
        return array();
    } else {
        $query = "select s.id,s.name,s.description,s.post_count,s.rating,s.followers,s.views
 from sets s 
 join sets_category sc on sc.set_id=s.id where sc.category_id=? order by s.rating desc
 limit ? , ?";
        $statement = $persistent_connection->prepare($query);
        $statement->bind_param('iii', $cid, $start, $limit);
        $statement->execute();
        $statement->bind_result($set_id, $set_name, $description, $post_count, $rating, $followers, $views);
        $i = 0;
        $setarray = array();
        while ($statement->fetch()) {
            $setarray[$i] = array(
                "id" => $set_id,
                "name" => $set_name,
                "description" => $description,
                "post_count" => $post_count,
                "rating" => $rating,
                "followers" => $followers,
                "views" => $views
            );
            $i++;
        }
        $statement->close();

        if ($i > 0) {
            $j = 0;
            $datatype_list = "";
            $argument_array = array();
            $query = "SELECT post.set_id,post.src,post.type FROM post JOIN (SELECT t1.id,t1.set_id, COUNT(t2.set_id) AS theCount FROM post t1 LEFT JOIN post t2 ON t1.set_id = t2.set_id AND t1.id > t2.id GROUP BY t1.set_id, t1.id HAVING theCount < 3) AS dt USING (set_id, id) where post.set_id in (";
            for ($j = 0; $j < $i - 1; $j++) {
                $datatype_list.="i";
                $argument_array[$j] = $setarray[$j]["id"];
                $query.=" ? ,";
            }
            $datatype_list.="i";
            $argument_array[$j] = $setarray[$j]["id"];
            $query.=" ? )";
            
            $statement = $persistent_connection->prepare($query);
            $argument_array = array_merge(array($datatype_list), array_values($argument_array));
            $tmp = array();
            foreach ($argument_array as $key => $value)
                $tmp[$key] = &$argument_array[$key];
            if (strlen($datatype_list) > 0)
                call_user_func_array(array($statement, 'bind_param'), $tmp);
            $statement->execute();
            $statement->bind_result($set_id,$src, $type);
            $set_based_post_array = array();
            $j = 0;
            $k = 0;
            $sid = -1;
            while ($statement->fetch()) {
                if ($sid == -1) {
                    $set_based_post_array[$k] = array();
                    $sid = $set_id;
                    $set_based_post_array[$k][$j] = array($set_id, $src, $type);
                    $j++;
                } else {
                    if ($sid != $set_id) {
                        $sid = $set_id;
                        $k++;
                        $j = 0;
                        $set_based_post_array[$k] = array();
                    }
                    $set_based_post_array[$k][$j] = array($set_id, $src, $type);
                    $j++;
                }
            }
            $statement->close();
            $set_based_post_array_size = $k+1;
            for ($k = 0; $k < $i; $k++) {
                $f=0;
              for($j=0;$j<$set_based_post_array_size;$j++){
                  if($setarray[$k]["id"]==$set_based_post_array[$j][0][0]){
                      $f=1;
                      $setarray[$k]["posts"] = $set_based_post_array[$j];
                      break;
                  }
              }
              if($f==0){
                  $setarray[$k]["posts"] = array();
              }
            }
        }
        return $setarray;
    }
}

?>