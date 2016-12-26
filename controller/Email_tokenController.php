<?php  class Email_tokenController{
 function insert($email_token,$persistent_connection){
$user_id = $email_token->getUser_id();
$token = $email_token->getToken();
$purpose = $email_token->getPurpose();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into email_token(";
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
if($token!=null){
$query.="token,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $token;
$k++;
}
if($purpose!=null){
$query.="purpose,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $purpose;
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
$statement = $con->prepare("select LAST_INSERT_ID() from email_token");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($email_token,$persistent_connection){
$id = $email_token->getId();
$user_id = $email_token->getUser_id();
$token = $email_token->getToken();
$purpose = $email_token->getPurpose();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update email_token set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if($token!=null || strlen($token)!=0){
$query.=" token=? ,";
$datatype_list.="s";
$argument_array[$k] = $token;
$k++;
}
if($purpose!=null || strlen($purpose)!=0){
$query.=" purpose=? ,";
$datatype_list.="s";
$argument_array[$k] = $purpose;
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
$query="delete from email_token where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from email_token where id=?";
else{
$query = "select ".implode(",",$request)." from email_token where id=?";
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
$email_token = new Email_token();
if (in_array('id', $request) || $request[0] == '*')$email_token->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$email_token->setUser_id($parameters["user_id"]);
if (in_array('token', $request) || $request[0] == '*')$email_token->setToken($parameters["token"]);
if (in_array('purpose', $request) || $request[0] == '*')$email_token->setPurpose($parameters["purpose"]);
return $email_token;

}
 function findByAll($email_token,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from email_token where 1=1";
}else{
$query = "select ".implode(",",$request)." from email_token where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $email_token->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $email_token->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $email_token->getToken())!=null || strlen(($e = $email_token->getToken()))!=0){
$query.=" and token=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $email_token->getPurpose())!=null || strlen(($e = $email_token->getPurpose()))!=0){
$query.=" and purpose=?";
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
$email_tokens = array();
while ($statement->fetch()) {
$email_tokens[$i] = new Email_token();
if(in_array("id",$request) || $request[0]=='*')$email_tokens[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$email_tokens[$i]->setUser_id($parameters["user_id"]);
if(in_array("token",$request) || $request[0]=='*')$email_tokens[$i]->setToken($parameters["token"]);
if(in_array("purpose",$request) || $request[0]=='*')$email_tokens[$i]->setPurpose($parameters["purpose"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $email_tokens;
}
} ?>