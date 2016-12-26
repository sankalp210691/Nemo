<?php  class TagsController{
 function insert($tags,$persistent_connection){
$name = $tags->getName();
$followers = $tags->getFollowers();
$posts = $tags->getPosts();
$score = $tags->getScore();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into tags(";
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
if($followers!=null){
$query.="followers,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followers;
$k++;
}
if($posts!=null){
$query.="posts,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $posts;
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
$statement = $con->prepare("select LAST_INSERT_ID() from tags");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($tags,$persistent_connection){
$id = $tags->getId();
$name = $tags->getName();
$followers = $tags->getFollowers();
$posts = $tags->getPosts();
$score = $tags->getScore();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update tags set";
if($name!=null || strlen($name)!=0){
$query.=" name=? ,";
$datatype_list.="s";
$argument_array[$k] = $name;
$k++;
}
if(strlen($followers)!=0){
$query.=" followers=? ,";
$datatype_list.="i";
$argument_array[$k] = $followers;
$k++;
}
if(strlen($posts)!=0){
$query.=" posts=? ,";
$datatype_list.="i";
$argument_array[$k] = $posts;
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
$query="delete from tags where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from tags where id=?";
else{
$query = "select ".implode(",",$request)." from tags where id=?";
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
$tags = new Tags();
if (in_array('id', $request) || $request[0] == '*')$tags->setId($parameters["id"]);
if (in_array('name', $request) || $request[0] == '*')$tags->setName($parameters["name"]);
if (in_array('followers', $request) || $request[0] == '*')$tags->setFollowers($parameters["followers"]);
if (in_array('posts', $request) || $request[0] == '*')$tags->setPosts($parameters["posts"]);
if (in_array('score', $request) || $request[0] == '*')$tags->setScore($parameters["score"]);
return $tags;

}
 function findByAll($tags,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from tags where 1=1";
}else{
$query = "select ".implode(",",$request)." from tags where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $tags->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $tags->getName())!=null || strlen(($e = $tags->getName()))!=0){
$query.=" and name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $tags->getFollowers()))!=0){
$query.=" and followers=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $tags->getPosts()))!=0){
$query.=" and posts=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $tags->getScore()))!=0){
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
$tagss = array();
while ($statement->fetch()) {
$tagss[$i] = new Tags();
if(in_array("id",$request) || $request[0]=='*')$tagss[$i]->setId($parameters["id"]);
if(in_array("name",$request) || $request[0]=='*')$tagss[$i]->setName($parameters["name"]);
if(in_array("followers",$request) || $request[0]=='*')$tagss[$i]->setFollowers($parameters["followers"]);
if(in_array("posts",$request) || $request[0]=='*')$tagss[$i]->setPosts($parameters["posts"]);
if(in_array("score",$request) || $request[0]=='*')$tagss[$i]->setScore($parameters["score"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $tagss;
}
} ?>