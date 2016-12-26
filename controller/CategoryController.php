<?php  class CategoryController{
 function insert($category,$persistent_connection){
$name = $category->getName();
$image_src = $category->getImage_src();
$rank = $category->getRank();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into category(";
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
if($image_src!=null){
$query.="image_src,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $image_src;
$k++;
}
if($rank!=null){
$query.="rank,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rank;
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
$statement = $con->prepare("select LAST_INSERT_ID() from category");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($category,$persistent_connection){
$id = $category->getId();
$name = $category->getName();
$image_src = $category->getImage_src();
$rank = $category->getRank();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update category set";
if($name!=null || strlen($name)!=0){
$query.=" name=? ,";
$datatype_list.="s";
$argument_array[$k] = $name;
$k++;
}
if($image_src!=null || strlen($image_src)!=0){
$query.=" image_src=? ,";
$datatype_list.="s";
$argument_array[$k] = $image_src;
$k++;
}
if(strlen($rank)!=0){
$query.=" rank=? ,";
$datatype_list.="d";
$argument_array[$k] = $rank;
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
$query="delete from category where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from category where id=?";
else{
$query = "select ".implode(",",$request)." from category where id=?";
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
$category = new Category();
if (in_array('id', $request) || $request[0] == '*')$category->setId($parameters["id"]);
if (in_array('name', $request) || $request[0] == '*')$category->setName($parameters["name"]);
if (in_array('image_src', $request) || $request[0] == '*')$category->setImage_src($parameters["image_src"]);
if (in_array('rank', $request) || $request[0] == '*')$category->setRank($parameters["rank"]);
return $category;

}
 function findByAll($category,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from category where 1=1";
}else{
$query = "select ".implode(",",$request)." from category where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $category->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $category->getName())!=null || strlen(($e = $category->getName()))!=0){
$query.=" and name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $category->getImage_src())!=null || strlen(($e = $category->getImage_src()))!=0){
$query.=" and image_src=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $category->getRank()))!=0){
$query.=" and rank=?";
$datatype_list.="d";
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
$categorys = array();
while ($statement->fetch()) {
$categorys[$i] = new Category();
if(in_array("id",$request) || $request[0]=='*')$categorys[$i]->setId($parameters["id"]);
if(in_array("name",$request) || $request[0]=='*')$categorys[$i]->setName($parameters["name"]);
if(in_array("image_src",$request) || $request[0]=='*')$categorys[$i]->setImage_src($parameters["image_src"]);
if(in_array("rank",$request) || $request[0]=='*')$categorys[$i]->setRank($parameters["rank"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $categorys;
}
} ?>