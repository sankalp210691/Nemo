<?php  class Not_shared_toController{
 function insert($not_shared_to,$persistent_connection){
$user_id = $not_shared_to->getUser_id();
$post_id = $not_shared_to->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into not_shared_to(";
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
$statement = $con->prepare("select LAST_INSERT_ID() from not_shared_to");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($not_shared_to,$persistent_connection){
$id = $not_shared_to->getId();
$user_id = $not_shared_to->getUser_id();
$post_id = $not_shared_to->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update not_shared_to set";
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
$query="delete from not_shared_to where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from not_shared_to where id=?";
else{
$query = "select ".implode(",",$request)." from not_shared_to where id=?";
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
$not_shared_to = new Not_shared_to();
if (in_array('id', $request) || $request[0] == '*')$not_shared_to->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$not_shared_to->setUser_id($parameters["user_id"]);
if (in_array('post_id', $request) || $request[0] == '*')$not_shared_to->setPost_id($parameters["post_id"]);
return $not_shared_to;

}
 function findByAll($not_shared_to,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from not_shared_to where 1=1";
}else{
$query = "select ".implode(",",$request)." from not_shared_to where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $not_shared_to->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $not_shared_to->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $not_shared_to->getPost_id()))!=0){
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
$not_shared_tos = array();
while ($statement->fetch()) {
$not_shared_tos[$i] = new Not_shared_to();
if(in_array("id",$request) || $request[0]=='*')$not_shared_tos[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$not_shared_tos[$i]->setUser_id($parameters["user_id"]);
if(in_array("post_id",$request) || $request[0]=='*')$not_shared_tos[$i]->setPost_id($parameters["post_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $not_shared_tos;
}
} ?>