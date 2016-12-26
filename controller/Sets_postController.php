<?php  class Sets_postController{
 function insert($sets_post,$persistent_connection){
$set_id = $sets_post->getSet_id();
$post_id = $sets_post->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into sets_post(";
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
if($post_id!=null){
$query.="post_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $post_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from sets_post");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($sets_post,$persistent_connection){
$id = $sets_post->getId();
$set_id = $sets_post->getSet_id();
$post_id = $sets_post->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update sets_post set";
if(strlen($set_id)!=0){
$query.=" set_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $set_id;
$k++;
}
if(strlen($post_id)!=0){
$query.=" post_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $post_id;
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
$query="delete from sets_post where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from sets_post where id=?";
else{
$query = "select ".implode(",",$request)." from sets_post where id=?";
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
$sets_post = new Sets_post();
if (in_array('id', $request) || $request[0] == '*')$sets_post->setId($parameters["id"]);
if (in_array('set_id', $request) || $request[0] == '*')$sets_post->setSet_id($parameters["set_id"]);
if (in_array('post_id', $request) || $request[0] == '*')$sets_post->setPost_id($parameters["post_id"]);
return $sets_post;

}
 function findByAll($sets_post,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from sets_post where 1=1";
}else{
$query = "select ".implode(",",$request)." from sets_post where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $sets_post->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets_post->getSet_id()))!=0){
$query.=" and set_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets_post->getPost_id()))!=0){
$query.=" and post_id=?";
$datatype_list.="i";
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
$sets_posts = array();
while ($statement->fetch()) {
$sets_posts[$i] = new Sets_post();
if(in_array("id",$request) || $request[0]=='*')$sets_posts[$i]->setId($parameters["id"]);
if(in_array("set_id",$request) || $request[0]=='*')$sets_posts[$i]->setSet_id($parameters["set_id"]);
if(in_array("post_id",$request) || $request[0]=='*')$sets_posts[$i]->setPost_id($parameters["post_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $sets_posts;
}
} ?>