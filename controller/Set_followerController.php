<?php  class Set_followerController{
 function insert($set_follower,$persistent_connection){
$set_id = $set_follower->getSet_id();
$user_id = $set_follower->getUser_id();
$score = $set_follower->getScore();
$secret = $set_follower->getSecret();
$date = $set_follower->getDate();
$time = $set_follower->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into set_follower(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($set_id!=null){
$query.="set_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $set_id;
$k++;
}
if($user_id!=null){
$query.="user_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_id;
$k++;
}
if($score!=null){
$query.="score,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $score;
$k++;
}
if($secret!=null){
$query.="secret,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $secret;
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
$statement = $con->prepare("select LAST_INSERT_ID() from set_follower");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($set_follower,$persistent_connection){
$id = $set_follower->getId();
$set_id = $set_follower->getSet_id();
$user_id = $set_follower->getUser_id();
$score = $set_follower->getScore();
$secret = $set_follower->getSecret();
$date = $set_follower->getDate();
$time = $set_follower->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update set_follower set";
if(strlen($set_id)!=0){
$query.=" set_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $set_id;
$k++;
}
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($score)!=0){
$query.=" score=? ,";
$datatype_list.="d";
$argument_array[$k] = $score;
$k++;
}
if(strlen($secret)!=0){
$query.=" secret=? ,";
$datatype_list.="i";
$argument_array[$k] = $secret;
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
$query="delete from set_follower where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from set_follower where id=?";
else{
$query = "select ".implode(",",$request)." from set_follower where id=?";
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
$set_follower = new Set_follower();
if (in_array('id', $request) || $request[0] == '*')$set_follower->setId($parameters["id"]);
if (in_array('set_id', $request) || $request[0] == '*')$set_follower->setSet_id($parameters["set_id"]);
if (in_array('user_id', $request) || $request[0] == '*')$set_follower->setUser_id($parameters["user_id"]);
if (in_array('score', $request) || $request[0] == '*')$set_follower->setScore($parameters["score"]);
if (in_array('secret', $request) || $request[0] == '*')$set_follower->setSecret($parameters["secret"]);
if (in_array('date', $request) || $request[0] == '*')$set_follower->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$set_follower->setTime($parameters["time"]);
return $set_follower;

}
 function findByAll($set_follower,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from set_follower where 1=1";
}else{
$query = "select ".implode(",",$request)." from set_follower where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $set_follower->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getSet_id()))!=0){
$query.=" and set_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getScore()))!=0){
$query.=" and score=?";
$datatype_list.="d";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getSecret()))!=0){
$query.=" and secret=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $set_follower->getTime()))!=0){
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
$set_followers = array();
while ($statement->fetch()) {
$set_followers[$i] = new Set_follower();
if(in_array("id",$request) || $request[0]=='*')$set_followers[$i]->setId($parameters["id"]);
if(in_array("set_id",$request) || $request[0]=='*')$set_followers[$i]->setSet_id($parameters["set_id"]);
if(in_array("user_id",$request) || $request[0]=='*')$set_followers[$i]->setUser_id($parameters["user_id"]);
if(in_array("score",$request) || $request[0]=='*')$set_followers[$i]->setScore($parameters["score"]);
if(in_array("secret",$request) || $request[0]=='*')$set_followers[$i]->setSecret($parameters["secret"]);
if(in_array("date",$request) || $request[0]=='*')$set_followers[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$set_followers[$i]->setTime($parameters["time"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $set_followers;
}
} ?>