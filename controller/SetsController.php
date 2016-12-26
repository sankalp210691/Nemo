<?php  class SetsController{
 function insert($sets,$persistent_connection){
$name = $sets->getName();
$description = $sets->getDescription();
$post_count = $sets->getPost_count();
$rating = $sets->getRating();
$followers = $sets->getFollowers();
$views = $sets->getViews();
$privacy = $sets->getPrivacy();
$user_id = $sets->getUser_id();
$date = $sets->getDate();
$time = $sets->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into sets(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($name!=null){
$query.="name,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $name;
$k++;
}
if($description!=null){
$query.="description,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $description;
$k++;
}
if($post_count!=null){
$query.="post_count,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $post_count;
$k++;
}
if($rating!=null){
$query.="rating,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rating;
$k++;
}
if($followers!=null){
$query.="followers,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followers;
$k++;
}
if($views!=null){
$query.="views,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $views;
$k++;
}
if($privacy!=null){
$query.="privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $privacy;
$k++;
}
if($user_id!=null){
$query.="user_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_id;
$k++;
}
if($date!=null){
$query.="date,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $date;
$k++;
}else{
$query.="date,";
$placeholder_list.=" CURDATE() ,";
}
if($time!=null){
$query.="time,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $time;
$k++;
}else{
$query.="time,";
$placeholder_list.=" CURTIME() ,";
}
$query = substr($query,0,-1);
$placeholder_list = substr($placeholder_list,0,-1);
$query.=") values($placeholder_list)";
$statement = $con->prepare($query);
$argument_array = array_merge(array($datatype_list),array_values($argument_array));
$tmp = array();
foreach($argument_array as $key => $value) $tmp[$key] = &$argument_array[$key];
call_user_func_array(array($statement,'bind_param'),$tmp);
$statement->execute();
$statement->close();
$statement = $con->prepare("select LAST_INSERT_ID() from sets");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($sets,$persistent_connection){
$id = $sets->getId();
$name = $sets->getName();
$description = $sets->getDescription();
$post_count = $sets->getPost_count();
$rating = $sets->getRating();
$followers = $sets->getFollowers();
$views = $sets->getViews();
$privacy = $sets->getPrivacy();
$user_id = $sets->getUser_id();
$date = $sets->getDate();
$time = $sets->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update sets set";
if($name!=null || strlen($name)!=0){
$query.=" name=? ,";
$datatype_list.="s";
$argument_array[$k] = $name;
$k++;
}
if($description!=null || strlen($description)!=0){
$query.=" description=? ,";
$datatype_list.="s";
$argument_array[$k] = $description;
$k++;
}
if(strlen($post_count)!=0){
$query.=" post_count=? ,";
$datatype_list.="i";
$argument_array[$k] = $post_count;
$k++;
}
if(strlen($rating)!=0){
$query.=" rating=? ,";
$datatype_list.="d";
$argument_array[$k] = $rating;
$k++;
}
if(strlen($followers)!=0){
$query.=" followers=? ,";
$datatype_list.="i";
$argument_array[$k] = $followers;
$k++;
}
if(strlen($views)!=0){
$query.=" views=? ,";
$datatype_list.="i";
$argument_array[$k] = $views;
$k++;
}
if(strlen($privacy)!=0){
$query.=" privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $privacy;
$k++;
}
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if($date!=null){
$query.="date=? ,";
$datatype_list.="s";
$argument_array[$k] = $date;
$k++;
}
if($time!=null){
$query.="time=? ,";
$datatype_list.="s";
$argument_array[$k] = $time;
$k++;
}
$query = substr($query,0,-1);
$datatype_list.="i";
$argument_array[$k] = $id;
$k++;

$query.=" where id=?";
$statement = $con->prepare($query);
$argument_array = array_merge(array($datatype_list),array_values($argument_array));
$tmp = array();
foreach($argument_array as $key => $value) $tmp[$key] = &$argument_array[$key];
call_user_func_array(array($statement,'bind_param'),$tmp);
$statement->execute();
$statement->close();if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return 1;
}
 function delete($id,$persistent_connection) {
if($id==null ){return;}
if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="delete from sets where id=?";$statement = $con->prepare($query);
$statement->bind_param("i",$id);
$statement->execute();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return 1;
}
 function getByPrimaryKey($id,$request,$clause,$persistent_connection) {
if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
if(sizeof($request)==1 && $request[0]=="*")$query="select * from sets where id=?";
else{
$query = "select ".implode(",",$request)." from sets where id=?";
}
$statement = $con->prepare($query);
 $statement->bind_param("i",$id);
$statement->execute();
$meta = $statement->result_metadata();
while($field = $meta->fetch_field()){
$var = $field->name;
$$var = null;
$parameters[$field->name] = &$$var;
}
call_user_func_array(array($statement, 'bind_result'), $parameters);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}

if(sizeof($parameters)==0){
return null;
}
$sets = new Sets();
if (in_array('id', $request) || $request[0] == '*')$sets->setId($parameters["id"]);
if (in_array('name', $request) || $request[0] == '*')$sets->setName($parameters["name"]);
if (in_array('description', $request) || $request[0] == '*')$sets->setDescription($parameters["description"]);
if (in_array('post_count', $request) || $request[0] == '*')$sets->setPost_count($parameters["post_count"]);
if (in_array('rating', $request) || $request[0] == '*')$sets->setRating($parameters["rating"]);
if (in_array('followers', $request) || $request[0] == '*')$sets->setFollowers($parameters["followers"]);
if (in_array('views', $request) || $request[0] == '*')$sets->setViews($parameters["views"]);
if (in_array('privacy', $request) || $request[0] == '*')$sets->setPrivacy($parameters["privacy"]);
if (in_array('user_id', $request) || $request[0] == '*')$sets->setUser_id($parameters["user_id"]);
if (in_array('date', $request) || $request[0] == '*')$sets->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$sets->setTime($parameters["time"]);
return $sets;

}
 function findByAll($sets,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from sets where 1=1";
}else{
$query = "select ".implode(",",$request)." from sets where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $sets->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $sets->getName())!=null || strlen(($e = $sets->getName()))!=0){
$query.=" and name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $sets->getDescription())!=null || strlen(($e = $sets->getDescription()))!=0){
$query.=" and description=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getPost_count()))!=0){
$query.=" and post_count=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getRating()))!=0){
$query.=" and rating=?";
$datatype_list.="d";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getFollowers()))!=0){
$query.=" and followers=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getViews()))!=0){
$query.=" and views=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getPrivacy()))!=0){
$query.=" and privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets->getTime()))!=0){
$query.=" and time=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen($clause)!=0 || $clause!=null){
$query.=" ".$clause;}
if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$statement = $con->prepare($query);
$argument_array = array_merge(array($datatype_list), array_values($argument_array));
$tmp = array();
foreach ($argument_array as $key => $value)
$tmp[$key] = &$argument_array[$key];
if(strlen($datatype_list)>0)
call_user_func_array(array($statement, 'bind_param'), $tmp);
$statement->execute();
$meta = $statement->result_metadata();
while ($field = $meta->fetch_field()) {
$var = $field->name;
$$var = null;
$parameters[$field->name] = &$$var;
}
call_user_func_array(array($statement, 'bind_result'), $parameters);

$i = 0;
$setss = array();
while ($statement->fetch()) {
$setss[$i] = new Sets();
if(in_array("id",$request) || $request[0]=='*')$setss[$i]->setId($parameters["id"]);
if(in_array("name",$request) || $request[0]=='*')$setss[$i]->setName($parameters["name"]);
if(in_array("description",$request) || $request[0]=='*')$setss[$i]->setDescription($parameters["description"]);
if(in_array("post_count",$request) || $request[0]=='*')$setss[$i]->setPost_count($parameters["post_count"]);
if(in_array("rating",$request) || $request[0]=='*')$setss[$i]->setRating($parameters["rating"]);
if(in_array("followers",$request) || $request[0]=='*')$setss[$i]->setFollowers($parameters["followers"]);
if(in_array("views",$request) || $request[0]=='*')$setss[$i]->setViews($parameters["views"]);
if(in_array("privacy",$request) || $request[0]=='*')$setss[$i]->setPrivacy($parameters["privacy"]);
if(in_array("user_id",$request) || $request[0]=='*')$setss[$i]->setUser_id($parameters["user_id"]);
if(in_array("date",$request) || $request[0]=='*')$setss[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$setss[$i]->setTime($parameters["time"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $setss;
}
} ?>