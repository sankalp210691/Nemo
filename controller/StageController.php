<?php  class StageController{
 function insert($stage,$persistent_connection){
$stage_name = $stage->getStage_name();
$status = $stage->getStatus();
$stage_address = $stage->getStage_address();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into stage(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($stage_name!=null){
$query.="stage_name,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $stage_name;
$k++;
}
if($status!=null){
$query.="status,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $status;
$k++;
}
if($stage_address!=null){
$query.="stage_address,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $stage_address;
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
$statement = $con->prepare("select LAST_INSERT_ID() from stage");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($stage,$persistent_connection){
$id = $stage->getId();
$stage_name = $stage->getStage_name();
$status = $stage->getStatus();
$stage_address = $stage->getStage_address();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update stage set";
if($stage_name!=null || strlen($stage_name)!=0){
$query.=" stage_name=? ,";
$datatype_list.="s";
$argument_array[$k] = $stage_name;
$k++;
}
if(strlen($status)!=0){
$query.=" status=? ,";
$datatype_list.="i";
$argument_array[$k] = $status;
$k++;
}
if($stage_address!=null || strlen($stage_address)!=0){
$query.=" stage_address=? ,";
$datatype_list.="s";
$argument_array[$k] = $stage_address;
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
$query="delete from stage where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from stage where id=?";
else{
$query = "select ".implode(",",$request)." from stage where id=?";
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
$stage = new Stage();
if (in_array('id', $request) || $request[0] == '*')$stage->setId($parameters["id"]);
if (in_array('stage_name', $request) || $request[0] == '*')$stage->setStage_name($parameters["stage_name"]);
if (in_array('status', $request) || $request[0] == '*')$stage->setStatus($parameters["status"]);
if (in_array('stage_address', $request) || $request[0] == '*')$stage->setStage_address($parameters["stage_address"]);
return $stage;

}
 function findByAll($stage,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from stage where 1=1";
}else{
$query = "select ".implode(",",$request)." from stage where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $stage->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $stage->getStage_name())!=null || strlen(($e = $stage->getStage_name()))!=0){
$query.=" and stage_name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $stage->getStatus()))!=0){
$query.=" and status=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $stage->getStage_address())!=null || strlen(($e = $stage->getStage_address()))!=0){
$query.=" and stage_address=?";
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
$stages = array();
while ($statement->fetch()) {
$stages[$i] = new Stage();
if(in_array("id",$request) || $request[0]=='*')$stages[$i]->setId($parameters["id"]);
if(in_array("stage_name",$request) || $request[0]=='*')$stages[$i]->setStage_name($parameters["stage_name"]);
if(in_array("status",$request) || $request[0]=='*')$stages[$i]->setStatus($parameters["status"]);
if(in_array("stage_address",$request) || $request[0]=='*')$stages[$i]->setStage_address($parameters["stage_address"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $stages;
}
} ?>