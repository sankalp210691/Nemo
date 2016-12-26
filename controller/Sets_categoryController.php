<?php  class Sets_categoryController{
 function insert($sets_category,$persistent_connection){
$set_id = $sets_category->getSet_id();
$category_id = $sets_category->getCategory_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into sets_category(";
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
if($category_id!=null){
$query.="category_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $category_id;
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
$statement = $con->prepare("select LAST_INSERT_ID() from sets_category");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($sets_category,$persistent_connection){
$id = $sets_category->getId();
$set_id = $sets_category->getSet_id();
$category_id = $sets_category->getCategory_id();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update sets_category set";
if(strlen($set_id)!=0){
$query.=" set_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $set_id;
$k++;
}
if(strlen($category_id)!=0){
$query.=" category_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $category_id;
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
$query="delete from sets_category where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from sets_category where id=?";
else{
$query = "select ".implode(",",$request)." from sets_category where id=?";
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
$sets_category = new Sets_category();
if (in_array('id', $request) || $request[0] == '*')$sets_category->setId($parameters["id"]);
if (in_array('set_id', $request) || $request[0] == '*')$sets_category->setSet_id($parameters["set_id"]);
if (in_array('category_id', $request) || $request[0] == '*')$sets_category->setCategory_id($parameters["category_id"]);
return $sets_category;

}
 function findByAll($sets_category,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from sets_category where 1=1";
}else{
$query = "select ".implode(",",$request)." from sets_category where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $sets_category->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets_category->getSet_id()))!=0){
$query.=" and set_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $sets_category->getCategory_id()))!=0){
$query.=" and category_id=?";
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
$sets_categorys = array();
while ($statement->fetch()) {
$sets_categorys[$i] = new Sets_category();
if(in_array("id",$request) || $request[0]=='*')$sets_categorys[$i]->setId($parameters["id"]);
if(in_array("set_id",$request) || $request[0]=='*')$sets_categorys[$i]->setSet_id($parameters["set_id"]);
if(in_array("category_id",$request) || $request[0]=='*')$sets_categorys[$i]->setCategory_id($parameters["category_id"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $sets_categorys;
}
} ?>