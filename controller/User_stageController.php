<?php  class User_stageController{
 function insert($user_stage,$persistent_connection){
$user_id = $user_stage->getUser_id();
$stage_id = $user_stage->getStage_id();
$status = $user_stage->getStatus();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into user_stage(";
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
if($stage_id!=null){
$query.="stage_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $stage_id;
$k++;
}
if($status!=null){
$query.="status,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $status;
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
$statement = $con->prepare("select LAST_INSERT_ID() from user_stage");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($user_stage,$persistent_connection){
$id = $user_stage->getId();
$user_id = $user_stage->getUser_id();
$stage_id = $user_stage->getStage_id();
$status = $user_stage->getStatus();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update user_stage set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($stage_id)!=0){
$query.=" stage_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $stage_id;
$k++;
}
if(strlen($status)!=0){
$query.=" status=? ,";
$datatype_list.="i";
$argument_array[$k] = $status;
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
$query="delete from user_stage where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from user_stage where id=?";
else{
$query = "select ".implode(",",$request)." from user_stage where id=?";
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
$user_stage = new User_stage();
if (in_array('id', $request) || $request[0] == '*')$user_stage->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$user_stage->setUser_id($parameters["user_id"]);
if (in_array('stage_id', $request) || $request[0] == '*')$user_stage->setStage_id($parameters["stage_id"]);
if (in_array('status', $request) || $request[0] == '*')$user_stage->setStatus($parameters["status"]);
return $user_stage;

}
 function findByAll($user_stage,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from user_stage where 1=1";
}else{
$query = "select ".implode(",",$request)." from user_stage where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $user_stage->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user_stage->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user_stage->getStage_id()))!=0){
$query.=" and stage_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user_stage->getStatus()))!=0){
$query.=" and status=?";
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
$user_stages = array();
while ($statement->fetch()) {
$user_stages[$i] = new User_stage();
if(in_array("id",$request) || $request[0]=='*')$user_stages[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$user_stages[$i]->setUser_id($parameters["user_id"]);
if(in_array("stage_id",$request) || $request[0]=='*')$user_stages[$i]->setStage_id($parameters["stage_id"]);
if(in_array("status",$request) || $request[0]=='*')$user_stages[$i]->setStatus($parameters["status"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $user_stages;
}
} ?>