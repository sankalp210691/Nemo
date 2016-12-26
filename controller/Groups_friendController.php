<?php  class Groups_friendController{
 function insert($groups_friend,$persistent_connection){
$user_id = $groups_friend->getUser_id();
$group_id = $groups_friend->getGroup_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into groups_friend(";
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
if($group_id!=null){
$query.="group_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $group_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from groups_friend");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($groups_friend,$persistent_connection){
$id = $groups_friend->getId();
$user_id = $groups_friend->getUser_id();
$group_id = $groups_friend->getGroup_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update groups_friend set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($group_id)!=0){
$query.=" group_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $group_id;
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
$query="delete from groups_friend where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from groups_friend where id=?";
else{
$query = "select ".implode(",",$request)." from groups_friend where id=?";
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
$groups_friend = new Groups_friend();
if (in_array('id', $request) || $request[0] == '*')$groups_friend->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$groups_friend->setUser_id($parameters["user_id"]);
if (in_array('group_id', $request) || $request[0] == '*')$groups_friend->setGroup_id($parameters["group_id"]);
return $groups_friend;

}
 function findByAll($groups_friend,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from groups_friend where 1=1";
}else{
$query = "select ".implode(",",$request)." from groups_friend where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $groups_friend->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups_friend->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups_friend->getGroup_id()))!=0){
$query.=" and group_id=?";
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
$groups_friends = array();
while ($statement->fetch()) {
$groups_friends[$i] = new Groups_friend();
if(in_array("id",$request) || $request[0]=='*')$groups_friends[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$groups_friends[$i]->setUser_id($parameters["user_id"]);
if(in_array("group_id",$request) || $request[0]=='*')$groups_friends[$i]->setGroup_id($parameters["group_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $groups_friends;
}
} ?>