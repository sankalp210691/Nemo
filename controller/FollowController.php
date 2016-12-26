<?php  class FollowController{
 function insert($follow,$persistent_connection){
$follower_id = $follow->getFollower_id();
$followee_id = $follow->getFollowee_id();
$score = $follow->getScore();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into follow(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($follower_id!=null){
$query.="follower_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $follower_id;
$k++;
}
if($followee_id!=null){
$query.="followee_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followee_id;
$k++;
}
if($score!=null){
$query.="score,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $score;
$k++;
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
$statement = $con->prepare("select LAST_INSERT_ID() from follow");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($follow,$persistent_connection){
$id = $follow->getId();
$follower_id = $follow->getFollower_id();
$followee_id = $follow->getFollowee_id();
$score = $follow->getScore();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update follow set";
if(strlen($follower_id)!=0){
$query.=" follower_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $follower_id;
$k++;
}
if(strlen($followee_id)!=0){
$query.=" followee_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $followee_id;
$k++;
}
if(strlen($score)!=0){
$query.=" score=? ,";
$datatype_list.="d";
$argument_array[$k] = $score;
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
$query="delete from follow where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from follow where id=?";
else{
$query = "select ".implode(",",$request)." from follow where id=?";
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
$follow = new Follow();
if (in_array('id', $request) || $request[0] == '*')$follow->setId($parameters["id"]);
if (in_array('follower_id', $request) || $request[0] == '*')$follow->setFollower_id($parameters["follower_id"]);
if (in_array('followee_id', $request) || $request[0] == '*')$follow->setFollowee_id($parameters["followee_id"]);
if (in_array('score', $request) || $request[0] == '*')$follow->setScore($parameters["score"]);
return $follow;

}
 function findByAll($follow,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from follow where 1=1";
}else{
$query = "select ".implode(",",$request)." from follow where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $follow->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $follow->getFollower_id()))!=0){
$query.=" and follower_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $follow->getFollowee_id()))!=0){
$query.=" and followee_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $follow->getScore()))!=0){
$query.=" and score=?";
$datatype_list.="d";
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
$follows = array();
while ($statement->fetch()) {
$follows[$i] = new Follow();
if(in_array("id",$request) || $request[0]=='*')$follows[$i]->setId($parameters["id"]);
if(in_array("follower_id",$request) || $request[0]=='*')$follows[$i]->setFollower_id($parameters["follower_id"]);
if(in_array("followee_id",$request) || $request[0]=='*')$follows[$i]->setFollowee_id($parameters["followee_id"]);
if(in_array("score",$request) || $request[0]=='*')$follows[$i]->setScore($parameters["score"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $follows;
}
} ?>