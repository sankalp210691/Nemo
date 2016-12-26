<?php  class UserController{
 function insert($user,$persistent_connection){
$first_name = $user->getFirst_name();
$last_name = $user->getLast_name();
$email_id = $user->getEmail_id();
$password = $user->getPassword();
$sets = $user->getSets();
$interests = $user->getInterests();
$friends = $user->getFriends();
$followers = $user->getFollowers();
$followee = $user->getFollowee();
$dob = $user->getDob();
$nick = $user->getNick();
$profile_pic = $user->getProfile_pic();
$cover_pic = $user->getCover_pic();
$pending_friend_request = $user->getPending_friend_request();
$sent_friend_request = $user->getSent_friend_request();
$about_me = $user->getAbout_me();
$ph_no = $user->getPh_no();
$gender = $user->getGender();
$online = $user->getOnline();
$signup_stage = $user->getSignup_stage();
$date = $user->getDate();
$time = $user->getTime();
$status_privacy = $user->getStatus_privacy();
$msg_privacy = $user->getMsg_privacy();
$email_id_privacy = $user->getEmail_id_privacy();
$gender_privacy = $user->getGender_privacy();
$rel_status = $user->getRel_status();
$rel_status_privacy = $user->getRel_status_privacy();
$dob_privacy = $user->getDob_privacy();
$nick_privacy = $user->getNick_privacy();
$school_privacy = $user->getSchool_privacy();
$company_privacy = $user->getCompany_privacy();
$address = $user->getAddress();
$address_privacy = $user->getAddress_privacy();
$about_me_privacy = $user->getAbout_me_privacy();
$photo_privacy = $user->getPhoto_privacy();
$video_privacy = $user->getVideo_privacy();
$user_status = $user->getUser_status();
$place_id = $user->getPlace_id();
$languages = $user->getLanguages();
$place_privacy = $user->getPlace_privacy();
$language_privacy = $user->getLanguage_privacy();
$notification_mail = $user->getNotification_mail();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$query="insert into user(";
$placeholder_list = "";
$datatype_list = "";
$argument_array = array();
$k=0;
if($first_name!=null){
$query.="first_name,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $first_name;
$k++;
}
if($last_name!=null){
$query.="last_name,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $last_name;
$k++;
}
if($email_id!=null){
$query.="email_id,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $email_id;
$k++;
}
if($password!=null){
$query.="password,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $password;
$k++;
}
if($sets!=null){
$query.="sets,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $sets;
$k++;
}
if($interests!=null){
$query.="interests,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $interests;
$k++;
}
if($friends!=null){
$query.="friends,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $friends;
$k++;
}
if($followers!=null){
$query.="followers,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followers;
$k++;
}
if($followee!=null){
$query.="followee,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $followee;
$k++;
}
if($dob!=null){
$query.="dob,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $dob;
$k++;
}else{
$query.="dob,";
$placeholder_list.=" CURDATE() ,";
}
if($nick!=null){
$query.="nick,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $nick;
$k++;
}
if($profile_pic!=null){
$query.="profile_pic,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $profile_pic;
$k++;
}
if($cover_pic!=null){
$query.="cover_pic,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $cover_pic;
$k++;
}
if($pending_friend_request!=null){
$query.="pending_friend_request,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $pending_friend_request;
$k++;
}
if($sent_friend_request!=null){
$query.="sent_friend_request,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $sent_friend_request;
$k++;
}
if($about_me!=null){
$query.="about_me,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $about_me;
$k++;
}
if($ph_no!=null){
$query.="ph_no,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $ph_no;
$k++;
}
if($gender!=null){
$query.="gender,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $gender;
$k++;
}
if($online!=null){
$query.="online,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $online;
$k++;
}
if($signup_stage!=null){
$query.="signup_stage,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $signup_stage;
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
if($status_privacy!=null){
$query.="status_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $status_privacy;
$k++;
}
if($msg_privacy!=null){
$query.="msg_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $msg_privacy;
$k++;
}
if($email_id_privacy!=null){
$query.="email_id_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $email_id_privacy;
$k++;
}
if($gender_privacy!=null){
$query.="gender_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $gender_privacy;
$k++;
}
if($rel_status!=null){
$query.="rel_status,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rel_status;
$k++;
}
if($rel_status_privacy!=null){
$query.="rel_status_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $rel_status_privacy;
$k++;
}
if($dob_privacy!=null){
$query.="dob_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $dob_privacy;
$k++;
}
if($nick_privacy!=null){
$query.="nick_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $nick_privacy;
$k++;
}
if($school_privacy!=null){
$query.="school_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $school_privacy;
$k++;
}
if($company_privacy!=null){
$query.="company_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $company_privacy;
$k++;
}
if($address!=null){
$query.="address,";
$datatype_list.="s";
$placeholder_list.=" ? ,";
$argument_array[$k] = $address;
$k++;
}
if($address_privacy!=null){
$query.="address_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $address_privacy;
$k++;
}
if($about_me_privacy!=null){
$query.="about_me_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $about_me_privacy;
$k++;
}
if($photo_privacy!=null){
$query.="photo_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $photo_privacy;
$k++;
}
if($video_privacy!=null){
$query.="video_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $video_privacy;
$k++;
}
if($user_status!=null){
$query.="user_status,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $user_status;
$k++;
}
if($place_id!=null){
$query.="place_id,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $place_id;
$k++;
}
if($languages!=null){
$query.="languages,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $languages;
$k++;
}
if($place_privacy!=null){
$query.="place_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $place_privacy;
$k++;
}
if($language_privacy!=null){
$query.="language_privacy,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $language_privacy;
$k++;
}
if($notification_mail!=null){
$query.="notification_mail,";
$datatype_list.="i";
$placeholder_list.=" ? ,";
$argument_array[$k] = $notification_mail;
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
$statement = $con->prepare("select LAST_INSERT_ID() from user");
$statement->execute();
$statement->bind_result($last_id);
$statement->fetch();
$statement->close();
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $last_id;
}
 function update($user,$persistent_connection){
$id = $user->getId();
$first_name = $user->getFirst_name();
$last_name = $user->getLast_name();
$email_id = $user->getEmail_id();
$password = $user->getPassword();
$sets = $user->getSets();
$interests = $user->getInterests();
$friends = $user->getFriends();
$followers = $user->getFollowers();
$followee = $user->getFollowee();
$dob = $user->getDob();
$nick = $user->getNick();
$profile_pic = $user->getProfile_pic();
$cover_pic = $user->getCover_pic();
$pending_friend_request = $user->getPending_friend_request();
$sent_friend_request = $user->getSent_friend_request();
$about_me = $user->getAbout_me();
$ph_no = $user->getPh_no();
$gender = $user->getGender();
$online = $user->getOnline();
$signup_stage = $user->getSignup_stage();
$date = $user->getDate();
$time = $user->getTime();
$status_privacy = $user->getStatus_privacy();
$msg_privacy = $user->getMsg_privacy();
$email_id_privacy = $user->getEmail_id_privacy();
$gender_privacy = $user->getGender_privacy();
$rel_status = $user->getRel_status();
$rel_status_privacy = $user->getRel_status_privacy();
$dob_privacy = $user->getDob_privacy();
$nick_privacy = $user->getNick_privacy();
$school_privacy = $user->getSchool_privacy();
$company_privacy = $user->getCompany_privacy();
$address = $user->getAddress();
$address_privacy = $user->getAddress_privacy();
$about_me_privacy = $user->getAbout_me_privacy();
$photo_privacy = $user->getPhoto_privacy();
$video_privacy = $user->getVideo_privacy();
$user_status = $user->getUser_status();
$place_id = $user->getPlace_id();
$languages = $user->getLanguages();
$place_privacy = $user->getPlace_privacy();
$language_privacy = $user->getLanguage_privacy();
$notification_mail = $user->getNotification_mail();

if($persistent_connection==null){
$db_connection = new DBConnect("mysqli", "nemo", "", "", "");
$con = $db_connection->getCon();
}else{
$con = $persistent_connection;
}
$argument_array = array();
$k=0;
$datatype_list="";
$query="update user set";
if($first_name!=null || strlen($first_name)!=0){
$query.=" first_name=? ,";
$datatype_list.="s";
$argument_array[$k] = $first_name;
$k++;
}
if($last_name!=null || strlen($last_name)!=0){
$query.=" last_name=? ,";
$datatype_list.="s";
$argument_array[$k] = $last_name;
$k++;
}
if($email_id!=null || strlen($email_id)!=0){
$query.=" email_id=? ,";
$datatype_list.="s";
$argument_array[$k] = $email_id;
$k++;
}
if($password!=null || strlen($password)!=0){
$query.=" password=? ,";
$datatype_list.="s";
$argument_array[$k] = $password;
$k++;
}
if(strlen($sets)!=0){
$query.=" sets=? ,";
$datatype_list.="i";
$argument_array[$k] = $sets;
$k++;
}
if(strlen($interests)!=0){
$query.=" interests=? ,";
$datatype_list.="i";
$argument_array[$k] = $interests;
$k++;
}
if(strlen($friends)!=0){
$query.=" friends=? ,";
$datatype_list.="i";
$argument_array[$k] = $friends;
$k++;
}
if(strlen($followers)!=0){
$query.=" followers=? ,";
$datatype_list.="i";
$argument_array[$k] = $followers;
$k++;
}
if(strlen($followee)!=0){
$query.=" followee=? ,";
$datatype_list.="i";
$argument_array[$k] = $followee;
$k++;
}
if($dob!=null){
$query.="dob=? ,";
$datatype_list.="s";
$argument_array[$k] = $dob;
$k++;
}
if($nick!=null || strlen($nick)!=0){
$query.=" nick=? ,";
$datatype_list.="s";
$argument_array[$k] = $nick;
$k++;
}
if($profile_pic!=null || strlen($profile_pic)!=0){
$query.=" profile_pic=? ,";
$datatype_list.="s";
$argument_array[$k] = $profile_pic;
$k++;
}
if($cover_pic!=null || strlen($cover_pic)!=0){
$query.=" cover_pic=? ,";
$datatype_list.="s";
$argument_array[$k] = $cover_pic;
$k++;
}
if(strlen($pending_friend_request)!=0){
$query.=" pending_friend_request=? ,";
$datatype_list.="i";
$argument_array[$k] = $pending_friend_request;
$k++;
}
if(strlen($sent_friend_request)!=0){
$query.=" sent_friend_request=? ,";
$datatype_list.="i";
$argument_array[$k] = $sent_friend_request;
$k++;
}
if($about_me!=null || strlen($about_me)!=0){
$query.=" about_me=? ,";
$datatype_list.="s";
$argument_array[$k] = $about_me;
$k++;
}
if(strlen($ph_no)!=0){
$query.=" ph_no=? ,";
$datatype_list.="i";
$argument_array[$k] = $ph_no;
$k++;
}
if(strlen($gender)!=0){
$query.=" gender=? ,";
$datatype_list.="s";
$argument_array[$k] = $gender;
$k++;
}
if(strlen($online)!=0){
$query.=" online=? ,";
$datatype_list.="i";
$argument_array[$k] = $online;
$k++;
}
if(strlen($signup_stage)!=0){
$query.=" signup_stage=? ,";
$datatype_list.="i";
$argument_array[$k] = $signup_stage;
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
if(strlen($status_privacy)!=0){
$query.=" status_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $status_privacy;
$k++;
}
if(strlen($msg_privacy)!=0){
$query.=" msg_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $msg_privacy;
$k++;
}
if(strlen($email_id_privacy)!=0){
$query.=" email_id_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $email_id_privacy;
$k++;
}
if(strlen($gender_privacy)!=0){
$query.=" gender_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $gender_privacy;
$k++;
}
if(strlen($rel_status)!=0){
$query.=" rel_status=? ,";
$datatype_list.="i";
$argument_array[$k] = $rel_status;
$k++;
}
if(strlen($rel_status_privacy)!=0){
$query.=" rel_status_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $rel_status_privacy;
$k++;
}
if(strlen($dob_privacy)!=0){
$query.=" dob_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $dob_privacy;
$k++;
}
if(strlen($nick_privacy)!=0){
$query.=" nick_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $nick_privacy;
$k++;
}
if(strlen($school_privacy)!=0){
$query.=" school_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $school_privacy;
$k++;
}
if(strlen($company_privacy)!=0){
$query.=" company_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $company_privacy;
$k++;
}
if($address!=null || strlen($address)!=0){
$query.=" address=? ,";
$datatype_list.="s";
$argument_array[$k] = $address;
$k++;
}
if(strlen($address_privacy)!=0){
$query.=" address_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $address_privacy;
$k++;
}
if(strlen($about_me_privacy)!=0){
$query.=" about_me_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $about_me_privacy;
$k++;
}
if(strlen($photo_privacy)!=0){
$query.=" photo_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $photo_privacy;
$k++;
}
if(strlen($video_privacy)!=0){
$query.=" video_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $video_privacy;
$k++;
}
if(strlen($user_status)!=0){
$query.=" user_status=? ,";
$datatype_list.="i";
$argument_array[$k] = $user_status;
$k++;
}
if(strlen($place_id)!=0){
$query.=" place_id=? ,";
$datatype_list.="i";
$argument_array[$k] = $place_id;
$k++;
}
if(strlen($languages)!=0){
$query.=" languages=? ,";
$datatype_list.="i";
$argument_array[$k] = $languages;
$k++;
}
if(strlen($place_privacy)!=0){
$query.=" place_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $place_privacy;
$k++;
}
if(strlen($language_privacy)!=0){
$query.=" language_privacy=? ,";
$datatype_list.="i";
$argument_array[$k] = $language_privacy;
$k++;
}
if(strlen($notification_mail)!=0){
$query.=" notification_mail=? ,";
$datatype_list.="i";
$argument_array[$k] = $notification_mail;
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
$query="delete from user where id=?";$statement = $con->prepare($query);
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
if(sizeof($request)==1 && $request[0]=="*")$query="select * from user where id=?";
else{
$query = "select ".implode(",",$request)." from user where id=?";
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
$user = new User();
if (in_array('id', $request) || $request[0] == '*')$user->setId($parameters["id"]);
if (in_array('first_name', $request) || $request[0] == '*')$user->setFirst_name($parameters["first_name"]);
if (in_array('last_name', $request) || $request[0] == '*')$user->setLast_name($parameters["last_name"]);
if (in_array('email_id', $request) || $request[0] == '*')$user->setEmail_id($parameters["email_id"]);
if (in_array('password', $request) || $request[0] == '*')$user->setPassword($parameters["password"]);
if (in_array('sets', $request) || $request[0] == '*')$user->setSets($parameters["sets"]);
if (in_array('interests', $request) || $request[0] == '*')$user->setInterests($parameters["interests"]);
if (in_array('friends', $request) || $request[0] == '*')$user->setFriends($parameters["friends"]);
if (in_array('followers', $request) || $request[0] == '*')$user->setFollowers($parameters["followers"]);
if (in_array('followee', $request) || $request[0] == '*')$user->setFollowee($parameters["followee"]);
if (in_array('dob', $request) || $request[0] == '*')$user->setDob($parameters["dob"]);
if (in_array('nick', $request) || $request[0] == '*')$user->setNick($parameters["nick"]);
if (in_array('profile_pic', $request) || $request[0] == '*')$user->setProfile_pic($parameters["profile_pic"]);
if (in_array('cover_pic', $request) || $request[0] == '*')$user->setCover_pic($parameters["cover_pic"]);
if (in_array('pending_friend_request', $request) || $request[0] == '*')$user->setPending_friend_request($parameters["pending_friend_request"]);
if (in_array('sent_friend_request', $request) || $request[0] == '*')$user->setSent_friend_request($parameters["sent_friend_request"]);
if (in_array('about_me', $request) || $request[0] == '*')$user->setAbout_me($parameters["about_me"]);
if (in_array('ph_no', $request) || $request[0] == '*')$user->setPh_no($parameters["ph_no"]);
if (in_array('gender', $request) || $request[0] == '*')$user->setGender($parameters["gender"]);
if (in_array('online', $request) || $request[0] == '*')$user->setOnline($parameters["online"]);
if (in_array('signup_stage', $request) || $request[0] == '*')$user->setSignup_stage($parameters["signup_stage"]);
if (in_array('date', $request) || $request[0] == '*')$user->setDate($parameters["date"]);
if (in_array('time', $request) || $request[0] == '*')$user->setTime($parameters["time"]);
if (in_array('status_privacy', $request) || $request[0] == '*')$user->setStatus_privacy($parameters["status_privacy"]);
if (in_array('msg_privacy', $request) || $request[0] == '*')$user->setMsg_privacy($parameters["msg_privacy"]);
if (in_array('email_id_privacy', $request) || $request[0] == '*')$user->setEmail_id_privacy($parameters["email_id_privacy"]);
if (in_array('gender_privacy', $request) || $request[0] == '*')$user->setGender_privacy($parameters["gender_privacy"]);
if (in_array('rel_status', $request) || $request[0] == '*')$user->setRel_status($parameters["rel_status"]);
if (in_array('rel_status_privacy', $request) || $request[0] == '*')$user->setRel_status_privacy($parameters["rel_status_privacy"]);
if (in_array('dob_privacy', $request) || $request[0] == '*')$user->setDob_privacy($parameters["dob_privacy"]);
if (in_array('nick_privacy', $request) || $request[0] == '*')$user->setNick_privacy($parameters["nick_privacy"]);
if (in_array('school_privacy', $request) || $request[0] == '*')$user->setSchool_privacy($parameters["school_privacy"]);
if (in_array('company_privacy', $request) || $request[0] == '*')$user->setCompany_privacy($parameters["company_privacy"]);
if (in_array('address', $request) || $request[0] == '*')$user->setAddress($parameters["address"]);
if (in_array('address_privacy', $request) || $request[0] == '*')$user->setAddress_privacy($parameters["address_privacy"]);
if (in_array('about_me_privacy', $request) || $request[0] == '*')$user->setAbout_me_privacy($parameters["about_me_privacy"]);
if (in_array('photo_privacy', $request) || $request[0] == '*')$user->setPhoto_privacy($parameters["photo_privacy"]);
if (in_array('video_privacy', $request) || $request[0] == '*')$user->setVideo_privacy($parameters["video_privacy"]);
if (in_array('user_status', $request) || $request[0] == '*')$user->setUser_status($parameters["user_status"]);
if (in_array('place_id', $request) || $request[0] == '*')$user->setPlace_id($parameters["place_id"]);
if (in_array('languages', $request) || $request[0] == '*')$user->setLanguages($parameters["languages"]);
if (in_array('place_privacy', $request) || $request[0] == '*')$user->setPlace_privacy($parameters["place_privacy"]);
if (in_array('language_privacy', $request) || $request[0] == '*')$user->setLanguage_privacy($parameters["language_privacy"]);
if (in_array('notification_mail', $request) || $request[0] == '*')$user->setNotification_mail($parameters["notification_mail"]);
return $user;

}
 function findByAll($user,$request,$clause,$persistent_connection){
if(sizeof($request)==1 && $request[0]=="*"){
$query="select * from user where 1=1";
}else{
$query = "select ".implode(",",$request)." from user where 1=1";
}
$argument_array = array();
$k=0;
$datatype_list="";
if(strlen(($e = $user->getId()))!=0){
$query.=" and id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getFirst_name())!=null || strlen(($e = $user->getFirst_name()))!=0){
$query.=" and first_name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getLast_name())!=null || strlen(($e = $user->getLast_name()))!=0){
$query.=" and last_name=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getEmail_id())!=null || strlen(($e = $user->getEmail_id()))!=0){
$query.=" and email_id=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getPassword())!=null || strlen(($e = $user->getPassword()))!=0){
$query.=" and password=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getSets()))!=0){
$query.=" and sets=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getInterests()))!=0){
$query.=" and interests=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getFriends()))!=0){
$query.=" and friends=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getFollowers()))!=0){
$query.=" and followers=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getFollowee()))!=0){
$query.=" and followee=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getDob()))!=0){
$query.=" and dob=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getNick())!=null || strlen(($e = $user->getNick()))!=0){
$query.=" and nick=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getProfile_pic())!=null || strlen(($e = $user->getProfile_pic()))!=0){
$query.=" and profile_pic=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getCover_pic())!=null || strlen(($e = $user->getCover_pic()))!=0){
$query.=" and cover_pic=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getPending_friend_request()))!=0){
$query.=" and pending_friend_request=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getSent_friend_request()))!=0){
$query.=" and sent_friend_request=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getAbout_me())!=null || strlen(($e = $user->getAbout_me()))!=0){
$query.=" and about_me=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getPh_no()))!=0){
$query.=" and ph_no=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getGender()))!=0){
$query.=" and gender=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getOnline()))!=0){
$query.=" and online=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getSignup_stage()))!=0){
$query.=" and signup_stage=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getDate()))!=0){
$query.=" and date=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getTime()))!=0){
$query.=" and time=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getStatus_privacy()))!=0){
$query.=" and status_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getMsg_privacy()))!=0){
$query.=" and msg_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getEmail_id_privacy()))!=0){
$query.=" and email_id_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getGender_privacy()))!=0){
$query.=" and gender_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getRel_status()))!=0){
$query.=" and rel_status=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getRel_status_privacy()))!=0){
$query.=" and rel_status_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getDob_privacy()))!=0){
$query.=" and dob_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getNick_privacy()))!=0){
$query.=" and nick_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getSchool_privacy()))!=0){
$query.=" and school_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getCompany_privacy()))!=0){
$query.=" and company_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(($e = $user->getAddress())!=null || strlen(($e = $user->getAddress()))!=0){
$query.=" and address=?";
$datatype_list.="s";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getAddress_privacy()))!=0){
$query.=" and address_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getAbout_me_privacy()))!=0){
$query.=" and about_me_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getPhoto_privacy()))!=0){
$query.=" and photo_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getVideo_privacy()))!=0){
$query.=" and video_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getUser_status()))!=0){
$query.=" and user_status=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getPlace_id()))!=0){
$query.=" and place_id=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getLanguages()))!=0){
$query.=" and languages=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getPlace_privacy()))!=0){
$query.=" and place_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getLanguage_privacy()))!=0){
$query.=" and language_privacy=?";
$datatype_list.="i";
$argument_array[$k] = $e;
$k++;
}
if(strlen(($e = $user->getNotification_mail()))!=0){
$query.=" and notification_mail=?";
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
$users = array();
while ($statement->fetch()) {
$users[$i] = new User();
if(in_array("id",$request) || $request[0]=='*')$users[$i]->setId($parameters["id"]);
if(in_array("first_name",$request) || $request[0]=='*')$users[$i]->setFirst_name($parameters["first_name"]);
if(in_array("last_name",$request) || $request[0]=='*')$users[$i]->setLast_name($parameters["last_name"]);
if(in_array("email_id",$request) || $request[0]=='*')$users[$i]->setEmail_id($parameters["email_id"]);
if(in_array("password",$request) || $request[0]=='*')$users[$i]->setPassword($parameters["password"]);
if(in_array("sets",$request) || $request[0]=='*')$users[$i]->setSets($parameters["sets"]);
if(in_array("interests",$request) || $request[0]=='*')$users[$i]->setInterests($parameters["interests"]);
if(in_array("friends",$request) || $request[0]=='*')$users[$i]->setFriends($parameters["friends"]);
if(in_array("followers",$request) || $request[0]=='*')$users[$i]->setFollowers($parameters["followers"]);
if(in_array("followee",$request) || $request[0]=='*')$users[$i]->setFollowee($parameters["followee"]);
if(in_array("dob",$request) || $request[0]=='*')$users[$i]->setDob($parameters["dob"]);
if(in_array("nick",$request) || $request[0]=='*')$users[$i]->setNick($parameters["nick"]);
if(in_array("profile_pic",$request) || $request[0]=='*')$users[$i]->setProfile_pic($parameters["profile_pic"]);
if(in_array("cover_pic",$request) || $request[0]=='*')$users[$i]->setCover_pic($parameters["cover_pic"]);
if(in_array("pending_friend_request",$request) || $request[0]=='*')$users[$i]->setPending_friend_request($parameters["pending_friend_request"]);
if(in_array("sent_friend_request",$request) || $request[0]=='*')$users[$i]->setSent_friend_request($parameters["sent_friend_request"]);
if(in_array("about_me",$request) || $request[0]=='*')$users[$i]->setAbout_me($parameters["about_me"]);
if(in_array("ph_no",$request) || $request[0]=='*')$users[$i]->setPh_no($parameters["ph_no"]);
if(in_array("gender",$request) || $request[0]=='*')$users[$i]->setGender($parameters["gender"]);
if(in_array("online",$request) || $request[0]=='*')$users[$i]->setOnline($parameters["online"]);
if(in_array("signup_stage",$request) || $request[0]=='*')$users[$i]->setSignup_stage($parameters["signup_stage"]);
if(in_array("date",$request) || $request[0]=='*')$users[$i]->setDate($parameters["date"]);
if(in_array("time",$request) || $request[0]=='*')$users[$i]->setTime($parameters["time"]);
if(in_array("status_privacy",$request) || $request[0]=='*')$users[$i]->setStatus_privacy($parameters["status_privacy"]);
if(in_array("msg_privacy",$request) || $request[0]=='*')$users[$i]->setMsg_privacy($parameters["msg_privacy"]);
if(in_array("email_id_privacy",$request) || $request[0]=='*')$users[$i]->setEmail_id_privacy($parameters["email_id_privacy"]);
if(in_array("gender_privacy",$request) || $request[0]=='*')$users[$i]->setGender_privacy($parameters["gender_privacy"]);
if(in_array("rel_status",$request) || $request[0]=='*')$users[$i]->setRel_status($parameters["rel_status"]);
if(in_array("rel_status_privacy",$request) || $request[0]=='*')$users[$i]->setRel_status_privacy($parameters["rel_status_privacy"]);
if(in_array("dob_privacy",$request) || $request[0]=='*')$users[$i]->setDob_privacy($parameters["dob_privacy"]);
if(in_array("nick_privacy",$request) || $request[0]=='*')$users[$i]->setNick_privacy($parameters["nick_privacy"]);
if(in_array("school_privacy",$request) || $request[0]=='*')$users[$i]->setSchool_privacy($parameters["school_privacy"]);
if(in_array("company_privacy",$request) || $request[0]=='*')$users[$i]->setCompany_privacy($parameters["company_privacy"]);
if(in_array("address",$request) || $request[0]=='*')$users[$i]->setAddress($parameters["address"]);
if(in_array("address_privacy",$request) || $request[0]=='*')$users[$i]->setAddress_privacy($parameters["address_privacy"]);
if(in_array("about_me_privacy",$request) || $request[0]=='*')$users[$i]->setAbout_me_privacy($parameters["about_me_privacy"]);
if(in_array("photo_privacy",$request) || $request[0]=='*')$users[$i]->setPhoto_privacy($parameters["photo_privacy"]);
if(in_array("video_privacy",$request) || $request[0]=='*')$users[$i]->setVideo_privacy($parameters["video_privacy"]);
if(in_array("user_status",$request) || $request[0]=='*')$users[$i]->setUser_status($parameters["user_status"]);
if(in_array("place_id",$request) || $request[0]=='*')$users[$i]->setPlace_id($parameters["place_id"]);
if(in_array("languages",$request) || $request[0]=='*')$users[$i]->setLanguages($parameters["languages"]);
if(in_array("place_privacy",$request) || $request[0]=='*')$users[$i]->setPlace_privacy($parameters["place_privacy"]);
if(in_array("language_privacy",$request) || $request[0]=='*')$users[$i]->setLanguage_privacy($parameters["language_privacy"]);
if(in_array("notification_mail",$request) || $request[0]=='*')$users[$i]->setNotification_mail($parameters["notification_mail"]);
$i++;
}
if($persistent_connection==null){
$db_connection->mysqli_connect_close();
}
return $users;
}
} ?>