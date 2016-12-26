<?php  class PostviewController{
 function insert($postview,$persistent_connection){
$id = $postview->getId();
$title = $postview->getTitle();
$description = $postview->getDescription();
$type = $postview->getType();
$created_at = $postview->getCreated_at();
$tag_name = $postview->getTag_name();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into postview(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($id!=null){
$query.="id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $id;
$k++;
}
if($title!=null){
$query.="title,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $title;
$k++;
}
if($description!=null){
$query.="description,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $description;
$k++;
}
if($type!=null){
$query.="type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $type;
$k++;
}
if($created_at!=null){
$query.="created_at,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $created_at;
$k++;
}
if($tag_name!=null){
$query.="tag_name,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $tag_name;
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
$statement = $con->prepare("select LAST_INSERT_ID() from postview");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($postview,$persistent_connection){
$id = $postview->getId();
$title = $postview->getTitle();
$description = $postview->getDescription();
$type = $postview->getType();
$created_at = $postview->getCreated_at();
$tag_name = $postview->getTag_name();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update postview set";
if(strlen($id)!=0){
$query.=" id=? ,";
$datatype_list.="i";
$argument_array[$k] = $id;
$k++;
}
if($title!=null || strlen($title)!=0){
$query.=" title=? ,";
$datatype_list.="s";
$argument_array[$k] = $title;
$k++;
}
if($description!=null || strlen($description)!=0){
$query.=" description=? ,";
$datatype_list.="s";
$argument_array[$k] = $description;
$k++;
}
if($type!=null || strlen($type)!=0){
$query.=" type=? ,";
$datatype_list.="s";
$argument_array[$k] = $type;
$k++;
}
if($created_at!=null || strlen($created_at)!=0){
$query.=" created_at=? ,";
$datatype_list.="s";
$argument_array[$k] = $created_at;
$k++;
}
if($tag_name!=null || strlen($tag_name)!=0){
$query.=" tag_name=? ,";
$datatype_list.="s";
$argument_array[$k] = $tag_name;
$k++;
}
$query = substr($query,0,-1);

$query.="";
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
 function findByAll($postview,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from postview where 1=1";
}else{
$query = "select ".implode(",",$request)." from postview where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $postview->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $postview->getTitle())!=null || strlen(($e = $postview->getTitle()))!=0){
$query.=" and title=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $postview->getDescription())!=null || strlen(($e = $postview->getDescription()))!=0){
$query.=" and description=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $postview->getType())!=null || strlen(($e = $postview->getType()))!=0){
$query.=" and type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $postview->getCreated_at())!=null || strlen(($e = $postview->getCreated_at()))!=0){
$query.=" and created_at=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $postview->getTag_name())!=null || strlen(($e = $postview->getTag_name()))!=0){
$query.=" and tag_name=?";
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
$postviews = array();
while ($statement->fetch()) {
$postviews[$i] = new Postview();
if(in_array("id",$request) || $request[0]=='*')$postviews[$i]->setId($parameters["id"]);
if(in_array("title",$request) || $request[0]=='*')$postviews[$i]->setTitle($parameters["title"]);
if(in_array("description",$request) || $request[0]=='*')$postviews[$i]->setDescription($parameters["description"]);
if(in_array("type",$request) || $request[0]=='*')$postviews[$i]->setType($parameters["type"]);
if(in_array("created_at",$request) || $request[0]=='*')$postviews[$i]->setCreated_at($parameters["created_at"]);
if(in_array("tag_name",$request) || $request[0]=='*')$postviews[$i]->setTag_name($parameters["tag_name"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $postviews;
}
} ?>