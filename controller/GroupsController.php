<?php  class GroupsController{
 function insert($groups,$persistent_connection){
$name = $groups->getName();
$user_id = $groups->getUser_id();
$group_type = $groups->getGroup_type();
$blocked = $groups->getBlocked();
$private_post_sharing = $groups->getPrivate_post_sharing();
$suggest = $groups->getSuggest();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into groups(";
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
if($user_id!=null){
$query.="user_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_id;
$k++;
}
if($group_type!=null){
$query.="group_type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $group_type;
$k++;
}
if($blocked!=null){
$query.="blocked,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $blocked;
$k++;
}
if($private_post_sharing!=null){
$query.="private_post_sharing,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $private_post_sharing;
$k++;
}
if($suggest!=null){
$query.="suggest,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $suggest;
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
$statement = $con->prepare("select LAST_INSERT_ID() from groups");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($groups,$persistent_connection){
$id = $groups->getId();
$name = $groups->getName();
$user_id = $groups->getUser_id();
$group_type = $groups->getGroup_type();
$blocked = $groups->getBlocked();
$private_post_sharing = $groups->getPrivate_post_sharing();
$suggest = $groups->getSuggest();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update groups set";
if($name!=null || strlen($name)!=0){
$query.=" name=? ,";
$datatype_list.="s";
$argument_array[$k] = $name;
$k++;
}
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if($group_type!=null || strlen($group_type)!=0){
$query.=" group_type=? ,";
$datatype_list.="s";
$argument_array[$k] = $group_type;
$k++;
}
if(strlen($blocked)!=0){
$query.=" blocked=? ,";
$datatype_list.="i";
$argument_array[$k] = $blocked;
$k++;
}
if(strlen($private_post_sharing)!=0){
$query.=" private_post_sharing=? ,";
$datatype_list.="i";
$argument_array[$k] = $private_post_sharing;
$k++;
}
if(strlen($suggest)!=0){
$query.=" suggest=? ,";
$datatype_list.="i";
$argument_array[$k] = $suggest;
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
$query="delete from groups where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from groups where id=?";
else{
$query = "select ".implode(",",$request)." from groups where id=?";
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
$groups = new Groups();
if (in_array('id', $request) || $request[0] == '*')$groups->setId($parameters["id"]);
if (in_array('name', $request) || $request[0] == '*')$groups->setName($parameters["name"]);
if (in_array('user_id', $request) || $request[0] == '*')$groups->setUser_id($parameters["user_id"]);
if (in_array('group_type', $request) || $request[0] == '*')$groups->setGroup_type($parameters["group_type"]);
if (in_array('blocked', $request) || $request[0] == '*')$groups->setBlocked($parameters["blocked"]);
if (in_array('private_post_sharing', $request) || $request[0] == '*')$groups->setPrivate_post_sharing($parameters["private_post_sharing"]);
if (in_array('suggest', $request) || $request[0] == '*')$groups->setSuggest($parameters["suggest"]);
return $groups;

}
 function findByAll($groups,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from groups where 1=1";
}else{
$query = "select ".implode(",",$request)." from groups where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $groups->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $groups->getName())!=null || strlen(($e = $groups->getName()))!=0){
$query.=" and name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $groups->getGroup_type())!=null || strlen(($e = $groups->getGroup_type()))!=0){
$query.=" and group_type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups->getBlocked()))!=0){
$query.=" and blocked=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups->getPrivate_post_sharing()))!=0){
$query.=" and private_post_sharing=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $groups->getSuggest()))!=0){
$query.=" and suggest=?";
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
$groupss = array();
while ($statement->fetch()) {
$groupss[$i] = new Groups();
if(in_array("id",$request) || $request[0]=='*')$groupss[$i]->setId($parameters["id"]);
if(in_array("name",$request) || $request[0]=='*')$groupss[$i]->setName($parameters["name"]);
if(in_array("user_id",$request) || $request[0]=='*')$groupss[$i]->setUser_id($parameters["user_id"]);
if(in_array("group_type",$request) || $request[0]=='*')$groupss[$i]->setGroup_type($parameters["group_type"]);
if(in_array("blocked",$request) || $request[0]=='*')$groupss[$i]->setBlocked($parameters["blocked"]);
if(in_array("private_post_sharing",$request) || $request[0]=='*')$groupss[$i]->setPrivate_post_sharing($parameters["private_post_sharing"]);
if(in_array("suggest",$request) || $request[0]=='*')$groupss[$i]->setSuggest($parameters["suggest"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $groupss;
}
} ?>