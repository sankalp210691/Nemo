<?php  class SearchController{
 function insert($search,$persistent_connection){
$query = $search->getQuery();
$type = $search->getType();
$user_id = $search->getUser_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into search(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($query!=null){
$query.="query,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $query;
$k++;
}
if($type!=null){
$query.="type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $type;
$k++;
}
if($user_id!=null){
$query.="user_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from search");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($search,$persistent_connection){
$id = $search->getId();
$query = $search->getQuery();
$type = $search->getType();
$user_id = $search->getUser_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update search set";
if($query!=null || strlen($query)!=0){
$query.=" query=? ,";
$datatype_list.="s";
$argument_array[$k] = $query;
$k++;
}
if($type!=null || strlen($type)!=0){
$query.=" type=? ,";
$datatype_list.="s";
$argument_array[$k] = $type;
$k++;
}
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
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
$query="delete from search where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from search where id=?";
else{
$query = "select ".implode(",",$request)." from search where id=?";
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
$search = new Search();
if (in_array('id', $request) || $request[0] == '*')$search->setId($parameters["id"]);
if (in_array('query', $request) || $request[0] == '*')$search->setQuery($parameters["query"]);
if (in_array('type', $request) || $request[0] == '*')$search->setType($parameters["type"]);
if (in_array('user_id', $request) || $request[0] == '*')$search->setUser_id($parameters["user_id"]);
return $search;

}
 function findByAll($search,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from search where 1=1";
}else{
$query = "select ".implode(",",$request)." from search where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $search->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $search->getQuery())!=null || strlen(($e = $search->getQuery()))!=0){
$query.=" and query=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $search->getType())!=null || strlen(($e = $search->getType()))!=0){
$query.=" and type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $search->getUser_id()))!=0){
$query.=" and user_id=?";
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
$searchs = array();
while ($statement->fetch()) {
$searchs[$i] = new Search();
if(in_array("id",$request) || $request[0]=='*')$searchs[$i]->setId($parameters["id"]);
if(in_array("query",$request) || $request[0]=='*')$searchs[$i]->setQuery($parameters["query"]);
if(in_array("type",$request) || $request[0]=='*')$searchs[$i]->setType($parameters["type"]);
if(in_array("user_id",$request) || $request[0]=='*')$searchs[$i]->setUser_id($parameters["user_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $searchs;
}
} ?>