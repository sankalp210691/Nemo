<?php  class CommentsController{
 function insert($comments,$persistent_connection){
$user_id = $comments->getUser_id();
$post_id = $comments->getPost_id();
$type = $comments->getType();
$comment = $comments->getComment();
$likes = $comments->getLikes();
$date = $comments->getDate();
$time = $comments->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into comments(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($user_id!=null){
$query.="user_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_id;
$k++;
}
if($post_id!=null){
$query.="post_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $post_id;
$k++;
}
if($type!=null){
$query.="type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $type;
$k++;
}
if($comment!=null){
$query.="comment,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $comment;
$k++;
}
if($likes!=null){
$query.="likes,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $likes;
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
$statement = $con->prepare("select LAST_INSERT_ID() from comments");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($comments,$persistent_connection){
$id = $comments->getId();
$user_id = $comments->getUser_id();
$post_id = $comments->getPost_id();
$type = $comments->getType();
$comment = $comments->getComment();
$likes = $comments->getLikes();
$date = $comments->getDate();
$time = $comments->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update comments set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($post_id)!=0){
$query.=" post_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $post_id;
$k++;
}
if($type!=null || strlen($type)!=0){
$query.=" type=? ,";
$datatype_list.="s";
$argument_array[$k] = $type;
$k++;
}
if($comment!=null || strlen($comment)!=0){
$query.=" comment=? ,";
$datatype_list.="s";
$argument_array[$k] = $comment;
$k++;
}
if(strlen($likes)!=0){
$query.=" likes=? ,";
$datatype_list.="i";
$argument_array[$k] = $likes;
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
$query="delete from comments where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from comments where id=?";
else{
$query = "select ".implode(",",$request)." from comments where id=?";
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
$comments = new Comments();
if (in_array('id', $request) || $request[0] == '*')$comments->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$comments->setUser_id($parameters["user_id"]);
if (in_array('post_id', $request) || $request[0] == '*')$comments->setPost_id($parameters["post_id"]);
if (in_array('type', $request) || $request[0] == '*')$comments->setType($parameters["type"]);
if (in_array('comment', $request) || $request[0] == '*')$comments->setComment($parameters["comment"]);
if (in_array('likes', $request) || $request[0] == '*')$comments->setLikes($parameters["likes"]);
if (in_array('date', $request) || $request[0] == '*')$comments->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$comments->setTime($parameters["time"]);
return $comments;

}
 function findByAll($comments,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from comments where 1=1";
}else{
$query = "select ".implode(",",$request)." from comments where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $comments->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $comments->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $comments->getPost_id()))!=0){
$query.=" and post_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $comments->getType())!=null || strlen(($e = $comments->getType()))!=0){
$query.=" and type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $comments->getComment())!=null || strlen(($e = $comments->getComment()))!=0){
$query.=" and comment=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $comments->getLikes()))!=0){
$query.=" and likes=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $comments->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $comments->getTime()))!=0){
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
$commentss = array();
while ($statement->fetch()) {
$commentss[$i] = new Comments();
if(in_array("id",$request) || $request[0]=='*')$commentss[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$commentss[$i]->setUser_id($parameters["user_id"]);
if(in_array("post_id",$request) || $request[0]=='*')$commentss[$i]->setPost_id($parameters["post_id"]);
if(in_array("type",$request) || $request[0]=='*')$commentss[$i]->setType($parameters["type"]);
if(in_array("comment",$request) || $request[0]=='*')$commentss[$i]->setComment($parameters["comment"]);
if(in_array("likes",$request) || $request[0]=='*')$commentss[$i]->setLikes($parameters["likes"]);
if(in_array("date",$request) || $request[0]=='*')$commentss[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$commentss[$i]->setTime($parameters["time"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $commentss;
}
} ?>