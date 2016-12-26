<?php  class InterestsController{
 function insert($interests,$persistent_connection){
$name = $interests->getName();
$category_id = $interests->getCategory_id();
$image_src = $interests->getImage_src();
$followers = $interests->getFollowers();
$score = $interests->getScore();
$description = $interests->getDescription();
$maintainer = $interests->getMaintainer();
$added_date = $interests->getAdded_date();
$shares = $interests->getShares();
$comments = $interests->getComments();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into interests(";
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
if($category_id!=null){
$query.="category_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $category_id;
$k++;
}
if($image_src!=null){
$query.="image_src,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $image_src;
$k++;
}
if($followers!=null){
$query.="followers,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followers;
$k++;
}
if($score!=null){
$query.="score,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $score;
$k++;
}
if($description!=null){
$query.="description,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $description;
$k++;
}
if($maintainer!=null){
$query.="maintainer,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $maintainer;
$k++;
}
if($added_date!=null){
$query.="added_date,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $added_date;
$k++;
}else{
$query.="added_date,";
$placeholder_list.=" CURDATE() ,";
}
if($shares!=null){
$query.="shares,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $shares;
$k++;
}
if($comments!=null){
$query.="comments,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $comments;
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
$statement = $con->prepare("select LAST_INSERT_ID() from interests");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($interests,$persistent_connection){
$id = $interests->getId();
$name = $interests->getName();
$category_id = $interests->getCategory_id();
$image_src = $interests->getImage_src();
$followers = $interests->getFollowers();
$score = $interests->getScore();
$description = $interests->getDescription();
$maintainer = $interests->getMaintainer();
$added_date = $interests->getAdded_date();
$shares = $interests->getShares();
$comments = $interests->getComments();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update interests set";
if($name!=null || strlen($name)!=0){
$query.=" name=? ,";
$datatype_list.="s";
$argument_array[$k] = $name;
$k++;
}
if(strlen($category_id)!=0){
$query.=" category_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $category_id;
$k++;
}
if($image_src!=null || strlen($image_src)!=0){
$query.=" image_src=? ,";
$datatype_list.="s";
$argument_array[$k] = $image_src;
$k++;
}
if(strlen($followers)!=0){
$query.=" followers=? ,";
$datatype_list.="i";
$argument_array[$k] = $followers;
$k++;
}
if(strlen($score)!=0){
$query.=" score=? ,";
$datatype_list.="d";
$argument_array[$k] = $score;
$k++;
}
if($description!=null || strlen($description)!=0){
$query.=" description=? ,";
$datatype_list.="s";
$argument_array[$k] = $description;
$k++;
}
if(strlen($maintainer)!=0){
$query.=" maintainer=? ,";
$datatype_list.="i";
$argument_array[$k] = $maintainer;
$k++;
}
if($added_date!=null){
$query.="added_date=? ,";
$datatype_list.="s";
$argument_array[$k] = $added_date;
$k++;
}
if(strlen($shares)!=0){
$query.=" shares=? ,";
$datatype_list.="i";
$argument_array[$k] = $shares;
$k++;
}
if(strlen($comments)!=0){
$query.=" comments=? ,";
$datatype_list.="i";
$argument_array[$k] = $comments;
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
$query="delete from interests where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from interests where id=?";
else{
$query = "select ".implode(",",$request)." from interests where id=?";
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
$interests = new Interests();
if (in_array('id', $request) || $request[0] == '*')$interests->setId($parameters["id"]);
if (in_array('name', $request) || $request[0] == '*')$interests->setName($parameters["name"]);
if (in_array('category_id', $request) || $request[0] == '*')$interests->setCategory_id($parameters["category_id"]);
if (in_array('image_src', $request) || $request[0] == '*')$interests->setImage_src($parameters["image_src"]);
if (in_array('followers', $request) || $request[0] == '*')$interests->setFollowers($parameters["followers"]);
if (in_array('score', $request) || $request[0] == '*')$interests->setScore($parameters["score"]);
if (in_array('description', $request) || $request[0] == '*')$interests->setDescription($parameters["description"]);
if (in_array('maintainer', $request) || $request[0] == '*')$interests->setMaintainer($parameters["maintainer"]);
if (in_array('added_date', $request) || $request[0] == '*')$interests->setAdded_date($parameters["added_date"]);
if (in_array('shares', $request) || $request[0] == '*')$interests->setShares($parameters["shares"]);
if (in_array('comments', $request) || $request[0] == '*')$interests->setComments($parameters["comments"]);
return $interests;

}
 function findByAll($interests,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from interests where 1=1";
}else{
$query = "select ".implode(",",$request)." from interests where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $interests->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $interests->getName())!=null || strlen(($e = $interests->getName()))!=0){
$query.=" and name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getCategory_id()))!=0){
$query.=" and category_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $interests->getImage_src())!=null || strlen(($e = $interests->getImage_src()))!=0){
$query.=" and image_src=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getFollowers()))!=0){
$query.=" and followers=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getScore()))!=0){
$query.=" and score=?";
$datatype_list.="d";
$argument_array[$k] = $e;
$k++;
}
if(($e = $interests->getDescription())!=null || strlen(($e = $interests->getDescription()))!=0){
$query.=" and description=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getMaintainer()))!=0){
$query.=" and maintainer=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getAdded_date()))!=0){
$query.=" and added_date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getShares()))!=0){
$query.=" and shares=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $interests->getComments()))!=0){
$query.=" and comments=?";
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
$interestss = array();
while ($statement->fetch()) {
$interestss[$i] = new Interests();
if(in_array("id",$request) || $request[0]=='*')$interestss[$i]->setId($parameters["id"]);
if(in_array("name",$request) || $request[0]=='*')$interestss[$i]->setName($parameters["name"]);
if(in_array("category_id",$request) || $request[0]=='*')$interestss[$i]->setCategory_id($parameters["category_id"]);
if(in_array("image_src",$request) || $request[0]=='*')$interestss[$i]->setImage_src($parameters["image_src"]);
if(in_array("followers",$request) || $request[0]=='*')$interestss[$i]->setFollowers($parameters["followers"]);
if(in_array("score",$request) || $request[0]=='*')$interestss[$i]->setScore($parameters["score"]);
if(in_array("description",$request) || $request[0]=='*')$interestss[$i]->setDescription($parameters["description"]);
if(in_array("maintainer",$request) || $request[0]=='*')$interestss[$i]->setMaintainer($parameters["maintainer"]);
if(in_array("added_date",$request) || $request[0]=='*')$interestss[$i]->setAdded_date($parameters["added_date"]);
if(in_array("shares",$request) || $request[0]=='*')$interestss[$i]->setShares($parameters["shares"]);
if(in_array("comments",$request) || $request[0]=='*')$interestss[$i]->setComments($parameters["comments"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $interestss;
}
} ?>