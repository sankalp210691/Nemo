<?php  class Friend_postController{
 function insert($friend_post,$persistent_connection){
$friend_id = $friend_post->getFriend_id();
$post_id = $friend_post->getPost_id();
$date = $friend_post->getDate();
$time = $friend_post->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into friend_post(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($friend_id!=null){
$query.="friend_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $friend_id;
$k++;
}
if($post_id!=null){
$query.="post_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $post_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from friend_post");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($friend_post,$persistent_connection){
$id = $friend_post->getId();
$friend_id = $friend_post->getFriend_id();
$post_id = $friend_post->getPost_id();
$date = $friend_post->getDate();
$time = $friend_post->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update friend_post set";
if(strlen($friend_id)!=0){
$query.=" friend_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $friend_id;
$k++;
}
if(strlen($post_id)!=0){
$query.=" post_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $post_id;
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
$query="delete from friend_post where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from friend_post where id=?";
else{
$query = "select ".implode(",",$request)." from friend_post where id=?";
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
$friend_post = new Friend_post();
if (in_array('id', $request) || $request[0] == '*')$friend_post->setId($parameters["id"]);
if (in_array('friend_id', $request) || $request[0] == '*')$friend_post->setFriend_id($parameters["friend_id"]);
if (in_array('post_id', $request) || $request[0] == '*')$friend_post->setPost_id($parameters["post_id"]);
if (in_array('date', $request) || $request[0] == '*')$friend_post->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$friend_post->setTime($parameters["time"]);
return $friend_post;

}
 function findByAll($friend_post,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from friend_post where 1=1";
}else{
$query = "select ".implode(",",$request)." from friend_post where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $friend_post->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend_post->getFriend_id()))!=0){
$query.=" and friend_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend_post->getPost_id()))!=0){
$query.=" and post_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend_post->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend_post->getTime()))!=0){
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
$friend_posts = array();
while ($statement->fetch()) {
$friend_posts[$i] = new Friend_post();
if(in_array("id",$request) || $request[0]=='*')$friend_posts[$i]->setId($parameters["id"]);
if(in_array("friend_id",$request) || $request[0]=='*')$friend_posts[$i]->setFriend_id($parameters["friend_id"]);
if(in_array("post_id",$request) || $request[0]=='*')$friend_posts[$i]->setPost_id($parameters["post_id"]);
if(in_array("date",$request) || $request[0]=='*')$friend_posts[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$friend_posts[$i]->setTime($parameters["time"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $friend_posts;
}
} ?>