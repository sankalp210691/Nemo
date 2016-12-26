<?php  class FriendController{
 function insert($friend,$persistent_connection){
$sent_by = $friend->getSent_by();
$sent_to = $friend->getSent_to();
$status = $friend->getStatus();
$blocked1 = $friend->getBlocked1();
$blocked2 = $friend->getBlocked2();
$rank_by = $friend->getRank_by();
$rank_to = $friend->getRank_to();
$mailed = $friend->getMailed();
$date = $friend->getDate();
$time = $friend->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into friend(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($sent_by!=null){
$query.="sent_by,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $sent_by;
$k++;
}
if($sent_to!=null){
$query.="sent_to,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $sent_to;
$k++;
}
if($status!=null){
$query.="status,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $status;
$k++;
}
if($blocked1!=null){
$query.="blocked1,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $blocked1;
$k++;
}
if($blocked2!=null){
$query.="blocked2,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $blocked2;
$k++;
}
if($rank_by!=null){
$query.="rank_by,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rank_by;
$k++;
}
if($rank_to!=null){
$query.="rank_to,";
$datatype_list.="d";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rank_to;
$k++;
}
if($mailed!=null){
$query.="mailed,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $mailed;
$k++;
}
if($date!=null){
$query.="date,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $date;
$k++;
}else{
$query.="date,";
$placeholder_list.=" CURDATE() ,";
}
if($time!=null){
$query.="time,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $time;
$k++;
}else{
$query.="time,";
$placeholder_list.=" CURTIME() ,";
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
$statement = $con->prepare("select LAST_INSERT_ID() from friend");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($friend,$persistent_connection){
$id = $friend->getId();
$sent_by = $friend->getSent_by();
$sent_to = $friend->getSent_to();
$status = $friend->getStatus();
$blocked1 = $friend->getBlocked1();
$blocked2 = $friend->getBlocked2();
$rank_by = $friend->getRank_by();
$rank_to = $friend->getRank_to();
$mailed = $friend->getMailed();
$date = $friend->getDate();
$time = $friend->getTime();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update friend set";
if(strlen($sent_by)!=0){
$query.=" sent_by=? ,";
$datatype_list.="i";
$argument_array[$k] = $sent_by;
$k++;
}
if(strlen($sent_to)!=0){
$query.=" sent_to=? ,";
$datatype_list.="i";
$argument_array[$k] = $sent_to;
$k++;
}
if(strlen($status)!=0){
$query.=" status=? ,";
$datatype_list.="i";
$argument_array[$k] = $status;
$k++;
}
if(strlen($blocked1)!=0){
$query.=" blocked1=? ,";
$datatype_list.="i";
$argument_array[$k] = $blocked1;
$k++;
}
if(strlen($blocked2)!=0){
$query.=" blocked2=? ,";
$datatype_list.="i";
$argument_array[$k] = $blocked2;
$k++;
}
if(strlen($rank_by)!=0){
$query.=" rank_by=? ,";
$datatype_list.="d";
$argument_array[$k] = $rank_by;
$k++;
}
if(strlen($rank_to)!=0){
$query.=" rank_to=? ,";
$datatype_list.="d";
$argument_array[$k] = $rank_to;
$k++;
}
if(strlen($mailed)!=0){
$query.=" mailed=? ,";
$datatype_list.="i";
$argument_array[$k] = $mailed;
$k++;
}
if($date!=null){
$query.="date=? ,";
$datatype_list.="s";
$argument_array[$k] = $date;
$k++;
}
if($time!=null){
$query.="time=? ,";
$datatype_list.="s";
$argument_array[$k] = $time;
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
$query="delete from friend where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from friend where id=?";
else{
$query = "select ".implode(",",$request)." from friend where id=?";
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
$friend = new Friend();
if (in_array('id', $request) || $request[0] == '*')$friend->setId($parameters["id"]);
if (in_array('sent_by', $request) || $request[0] == '*')$friend->setSent_by($parameters["sent_by"]);
if (in_array('sent_to', $request) || $request[0] == '*')$friend->setSent_to($parameters["sent_to"]);
if (in_array('status', $request) || $request[0] == '*')$friend->setStatus($parameters["status"]);
if (in_array('blocked1', $request) || $request[0] == '*')$friend->setBlocked1($parameters["blocked1"]);
if (in_array('blocked2', $request) || $request[0] == '*')$friend->setBlocked2($parameters["blocked2"]);
if (in_array('rank_by', $request) || $request[0] == '*')$friend->setRank_by($parameters["rank_by"]);
if (in_array('rank_to', $request) || $request[0] == '*')$friend->setRank_to($parameters["rank_to"]);
if (in_array('mailed', $request) || $request[0] == '*')$friend->setMailed($parameters["mailed"]);
if (in_array('date', $request) || $request[0] == '*')$friend->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$friend->setTime($parameters["time"]);
return $friend;

}
 function findByAll($friend,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from friend where 1=1";
}else{
$query = "select ".implode(",",$request)." from friend where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $friend->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getSent_by()))!=0){
$query.=" and sent_by=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getSent_to()))!=0){
$query.=" and sent_to=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getStatus()))!=0){
$query.=" and status=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getBlocked1()))!=0){
$query.=" and blocked1=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getBlocked2()))!=0){
$query.=" and blocked2=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getRank_by()))!=0){
$query.=" and rank_by=?";
$datatype_list.="d";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getRank_to()))!=0){
$query.=" and rank_to=?";
$datatype_list.="d";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getMailed()))!=0){
$query.=" and mailed=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $friend->getTime()))!=0){
$query.=" and time=?";
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
$friends = array();
while ($statement->fetch()) {
$friends[$i] = new Friend();
if(in_array("id",$request) || $request[0]=='*')$friends[$i]->setId($parameters["id"]);
if(in_array("sent_by",$request) || $request[0]=='*')$friends[$i]->setSent_by($parameters["sent_by"]);
if(in_array("sent_to",$request) || $request[0]=='*')$friends[$i]->setSent_to($parameters["sent_to"]);
if(in_array("status",$request) || $request[0]=='*')$friends[$i]->setStatus($parameters["status"]);
if(in_array("blocked1",$request) || $request[0]=='*')$friends[$i]->setBlocked1($parameters["blocked1"]);
if(in_array("blocked2",$request) || $request[0]=='*')$friends[$i]->setBlocked2($parameters["blocked2"]);
if(in_array("rank_by",$request) || $request[0]=='*')$friends[$i]->setRank_by($parameters["rank_by"]);
if(in_array("rank_to",$request) || $request[0]=='*')$friends[$i]->setRank_to($parameters["rank_to"]);
if(in_array("mailed",$request) || $request[0]=='*')$friends[$i]->setMailed($parameters["mailed"]);
if(in_array("date",$request) || $request[0]=='*')$friends[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$friends[$i]->setTime($parameters["time"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $friends;
}
} ?>