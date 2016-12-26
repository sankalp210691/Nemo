<?php  class SharesController{
 function insert($shares,$persistent_connection){
$user_id = $shares->getUser_id();
$ref_id = $shares->getRef_id();
$post_id = $shares->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into shares(";
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
if($ref_id!=null){
$query.="ref_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $ref_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from shares");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($shares,$persistent_connection){
$id = $shares->getId();
$user_id = $shares->getUser_id();
$ref_id = $shares->getRef_id();
$post_id = $shares->getPost_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update shares set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($ref_id)!=0){
$query.=" ref_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $ref_id;
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
$query="delete from shares where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from shares where id=?";
else{
$query = "select ".implode(",",$request)." from shares where id=?";
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
$shares = new Shares();
if (in_array('id', $request) || $request[0] == '*')$shares->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$shares->setUser_id($parameters["user_id"]);
if (in_array('ref_id', $request) || $request[0] == '*')$shares->setRef_id($parameters["ref_id"]);
if (in_array('post_id', $request) || $request[0] == '*')$shares->setPost_id($parameters["post_id"]);
return $shares;

}
 function findByAll($shares,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from shares where 1=1";
}else{
$query = "select ".implode(",",$request)." from shares where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $shares->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $shares->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $shares->getRef_id()))!=0){
$query.=" and ref_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $shares->getPost_id()))!=0){
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
$sharess = array();
while ($statement->fetch()) {
$sharess[$i] = new Shares();
if(in_array("id",$request) || $request[0]=='*')$sharess[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$sharess[$i]->setUser_id($parameters["user_id"]);
if(in_array("ref_id",$request) || $request[0]=='*')$sharess[$i]->setRef_id($parameters["ref_id"]);
if(in_array("post_id",$request) || $request[0]=='*')$sharess[$i]->setPost_id($parameters["post_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $sharess;
}
} ?>