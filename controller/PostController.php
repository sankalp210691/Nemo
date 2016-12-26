<?php  class PostController{
 function insert($post,$persistent_connection){
$user_id = $post->getUser_id();
$set_id = $post->getSet_id();
$type = $post->getType();
$share_id = $post->getShare_id();
$title = $post->getTitle();
$description = $post->getDescription();
$share_text = $post->getShare_text();
$src = $post->getSrc();
$url = $post->getUrl();
$url_content_type = $post->getUrl_content_type();
$width = $post->getWidth();
$height = $post->getHeight();
$privacy = $post->getPrivacy();
$date = $post->getDate();
$time = $post->getTime();
$likes = $post->getLikes();
$shares = $post->getShares();
$score = $post->getScore();
$comments = $post->getComments();
$sharable = $post->getSharable();
$commentable = $post->getCommentable();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into post(";
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
if($set_id!=null){
$query.="set_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $set_id;
$k++;
}
if($type!=null){
$query.="type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $type;
$k++;
}
if($share_id!=null){
$query.="share_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $share_id;
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
if($share_text!=null){
$query.="share_text,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $share_text;
$k++;
}
if($src!=null){
$query.="src,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $src;
$k++;
}
if($url!=null){
$query.="url,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $url;
$k++;
}
if($url_content_type!=null){
$query.="url_content_type,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $url_content_type;
$k++;
}
if($width!=null){
$query.="width,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $width;
$k++;
}
if($height!=null){
$query.="height,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $height;
$k++;
}
if($privacy!=null){
$query.="privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $privacy;
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
if($likes!=null){
$query.="likes,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $likes;
$k++;
}
if($shares!=null){
$query.="shares,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $shares;
$k++;
}
if($score!=null){
$query.="score,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $score;
$k++;
}
if($comments!=null){
$query.="comments,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $comments;
$k++;
}
if($sharable!=null){
$query.="sharable,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $sharable;
$k++;
}
if($commentable!=null){
$query.="commentable,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $commentable;
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
$statement = $con->prepare("select LAST_INSERT_ID() from post");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($post,$persistent_connection){
$id = $post->getId();
$user_id = $post->getUser_id();
$set_id = $post->getSet_id();
$type = $post->getType();
$share_id = $post->getShare_id();
$title = $post->getTitle();
$description = $post->getDescription();
$share_text = $post->getShare_text();
$src = $post->getSrc();
$url = $post->getUrl();
$url_content_type = $post->getUrl_content_type();
$width = $post->getWidth();
$height = $post->getHeight();
$privacy = $post->getPrivacy();
$date = $post->getDate();
$time = $post->getTime();
$likes = $post->getLikes();
$shares = $post->getShares();
$score = $post->getScore();
$comments = $post->getComments();
$sharable = $post->getSharable();
$commentable = $post->getCommentable();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update post set";
if(strlen($user_id)!=0){
$query.=" user_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_id;
$k++;
}
if(strlen($set_id)!=0){
$query.=" set_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $set_id;
$k++;
}
if($type!=null || strlen($type)!=0){
$query.=" type=? ,";
$datatype_list.="s";
$argument_array[$k] = $type;
$k++;
}
if(strlen($share_id)!=0){
$query.=" share_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $share_id;
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
if($share_text!=null || strlen($share_text)!=0){
$query.=" share_text=? ,";
$datatype_list.="s";
$argument_array[$k] = $share_text;
$k++;
}
if($src!=null || strlen($src)!=0){
$query.=" src=? ,";
$datatype_list.="s";
$argument_array[$k] = $src;
$k++;
}
if($url!=null || strlen($url)!=0){
$query.=" url=? ,";
$datatype_list.="s";
$argument_array[$k] = $url;
$k++;
}
if($url_content_type!=null || strlen($url_content_type)!=0){
$query.=" url_content_type=? ,";
$datatype_list.="s";
$argument_array[$k] = $url_content_type;
$k++;
}
if(strlen($width)!=0){
$query.=" width=? ,";
$datatype_list.="i";
$argument_array[$k] = $width;
$k++;
}
if(strlen($height)!=0){
$query.=" height=? ,";
$datatype_list.="i";
$argument_array[$k] = $height;
$k++;
}
if(strlen($privacy)!=0){
$query.=" privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $privacy;
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
if(strlen($likes)!=0){
$query.=" likes=? ,";
$datatype_list.="i";
$argument_array[$k] = $likes;
$k++;
}
if(strlen($shares)!=0){
$query.=" shares=? ,";
$datatype_list.="i";
$argument_array[$k] = $shares;
$k++;
}
if(strlen($score)!=0){
$query.=" score=? ,";
$datatype_list.="i";
$argument_array[$k] = $score;
$k++;
}
if(strlen($comments)!=0){
$query.=" comments=? ,";
$datatype_list.="i";
$argument_array[$k] = $comments;
$k++;
}
if(strlen($sharable)!=0){
$query.=" sharable=? ,";
$datatype_list.="i";
$argument_array[$k] = $sharable;
$k++;
}
if(strlen($commentable)!=0){
$query.=" commentable=? ,";
$datatype_list.="i";
$argument_array[$k] = $commentable;
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
$query="delete from post where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from post where id=?";
else{
$query = "select ".implode(",",$request)." from post where id=?";
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
$post = new Post();
if (in_array('id', $request) || $request[0] == '*')$post->setId($parameters["id"]);
if (in_array('user_id', $request) || $request[0] == '*')$post->setUser_id($parameters["user_id"]);
if (in_array('set_id', $request) || $request[0] == '*')$post->setSet_id($parameters["set_id"]);
if (in_array('type', $request) || $request[0] == '*')$post->setType($parameters["type"]);
if (in_array('share_id', $request) || $request[0] == '*')$post->setShare_id($parameters["share_id"]);
if (in_array('title', $request) || $request[0] == '*')$post->setTitle($parameters["title"]);
if (in_array('description', $request) || $request[0] == '*')$post->setDescription($parameters["description"]);
if (in_array('share_text', $request) || $request[0] == '*')$post->setShare_text($parameters["share_text"]);
if (in_array('src', $request) || $request[0] == '*')$post->setSrc($parameters["src"]);
if (in_array('url', $request) || $request[0] == '*')$post->setUrl($parameters["url"]);
if (in_array('url_content_type', $request) || $request[0] == '*')$post->setUrl_content_type($parameters["url_content_type"]);
if (in_array('width', $request) || $request[0] == '*')$post->setWidth($parameters["width"]);
if (in_array('height', $request) || $request[0] == '*')$post->setHeight($parameters["height"]);
if (in_array('privacy', $request) || $request[0] == '*')$post->setPrivacy($parameters["privacy"]);
if (in_array('date', $request) || $request[0] == '*')$post->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$post->setTime($parameters["time"]);
if (in_array('likes', $request) || $request[0] == '*')$post->setLikes($parameters["likes"]);
if (in_array('shares', $request) || $request[0] == '*')$post->setShares($parameters["shares"]);
if (in_array('score', $request) || $request[0] == '*')$post->setScore($parameters["score"]);
if (in_array('comments', $request) || $request[0] == '*')$post->setComments($parameters["comments"]);
if (in_array('sharable', $request) || $request[0] == '*')$post->setSharable($parameters["sharable"]);
if (in_array('commentable', $request) || $request[0] == '*')$post->setCommentable($parameters["commentable"]);
return $post;

}
 function findByAll($post,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from post where 1=1";
}else{
$query = "select ".implode(",",$request)." from post where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $post->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getUser_id()))!=0){
$query.=" and user_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getSet_id()))!=0){
$query.=" and set_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getType())!=null || strlen(($e = $post->getType()))!=0){
$query.=" and type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getShare_id()))!=0){
$query.=" and share_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getTitle())!=null || strlen(($e = $post->getTitle()))!=0){
$query.=" and title=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getDescription())!=null || strlen(($e = $post->getDescription()))!=0){
$query.=" and description=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getShare_text())!=null || strlen(($e = $post->getShare_text()))!=0){
$query.=" and share_text=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getSrc())!=null || strlen(($e = $post->getSrc()))!=0){
$query.=" and src=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getUrl())!=null || strlen(($e = $post->getUrl()))!=0){
$query.=" and url=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $post->getUrl_content_type())!=null || strlen(($e = $post->getUrl_content_type()))!=0){
$query.=" and url_content_type=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getWidth()))!=0){
$query.=" and width=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getHeight()))!=0){
$query.=" and height=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getPrivacy()))!=0){
$query.=" and privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getTime()))!=0){
$query.=" and time=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getLikes()))!=0){
$query.=" and likes=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getShares()))!=0){
$query.=" and shares=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getScore()))!=0){
$query.=" and score=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getComments()))!=0){
$query.=" and comments=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getSharable()))!=0){
$query.=" and sharable=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $post->getCommentable()))!=0){
$query.=" and commentable=?";
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
$posts = array();
while ($statement->fetch()) {
$posts[$i] = new Post();
if(in_array("id",$request) || $request[0]=='*')$posts[$i]->setId($parameters["id"]);
if(in_array("user_id",$request) || $request[0]=='*')$posts[$i]->setUser_id($parameters["user_id"]);
if(in_array("set_id",$request) || $request[0]=='*')$posts[$i]->setSet_id($parameters["set_id"]);
if(in_array("type",$request) || $request[0]=='*')$posts[$i]->setType($parameters["type"]);
if(in_array("share_id",$request) || $request[0]=='*')$posts[$i]->setShare_id($parameters["share_id"]);
if(in_array("title",$request) || $request[0]=='*')$posts[$i]->setTitle($parameters["title"]);
if(in_array("description",$request) || $request[0]=='*')$posts[$i]->setDescription($parameters["description"]);
if(in_array("share_text",$request) || $request[0]=='*')$posts[$i]->setShare_text($parameters["share_text"]);
if(in_array("src",$request) || $request[0]=='*')$posts[$i]->setSrc($parameters["src"]);
if(in_array("url",$request) || $request[0]=='*')$posts[$i]->setUrl($parameters["url"]);
if(in_array("url_content_type",$request) || $request[0]=='*')$posts[$i]->setUrl_content_type($parameters["url_content_type"]);
if(in_array("width",$request) || $request[0]=='*')$posts[$i]->setWidth($parameters["width"]);
if(in_array("height",$request) || $request[0]=='*')$posts[$i]->setHeight($parameters["height"]);
if(in_array("privacy",$request) || $request[0]=='*')$posts[$i]->setPrivacy($parameters["privacy"]);
if(in_array("date",$request) || $request[0]=='*')$posts[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$posts[$i]->setTime($parameters["time"]);
if(in_array("likes",$request) || $request[0]=='*')$posts[$i]->setLikes($parameters["likes"]);
if(in_array("shares",$request) || $request[0]=='*')$posts[$i]->setShares($parameters["shares"]);
if(in_array("score",$request) || $request[0]=='*')$posts[$i]->setScore($parameters["score"]);
if(in_array("comments",$request) || $request[0]=='*')$posts[$i]->setComments($parameters["comments"]);
if(in_array("sharable",$request) || $request[0]=='*')$posts[$i]->setSharable($parameters["sharable"]);
if(in_array("commentable",$request) || $request[0]=='*')$posts[$i]->setCommentable($parameters["commentable"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $posts;
}
} ?>