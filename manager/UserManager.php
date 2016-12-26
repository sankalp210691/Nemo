<?php

require "../db/DBConnect.php";
include "../model/UserModel.php";
include "../controller/UserController.php";
include "../model/Email_tokenModel.php";
include "../controller/Email_tokenController.php";
include "../model/StageModel.php";
include "../controller/StageController.php";
include "../model/User_stageModel.php";
include "../controller/User_stageController.php";
include "../model/CategoryModel.php";
include "../controller/CategoryController.php";
include "../req/SpecialFunctions.php";

$req = $_POST['req'];
if ($req == "login") {
    $email_id = trim($_POST["login_email"]);
    $password = trim($_POST["login_password"]);

    if (strlen($email_id) == 0 || strlen($password) == 0) {
        echo "<span style='color:red'>Invalid Email ID or Password</span>";
    } else if (strpos($email_id, "@") === false) {
        echo "<span style='color:red'>Invalid Email ID</span>";
    } else {
        $user = new User();
        $user->setEmail_id($email_id);
        $usercon = new UserController();
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();
        $users = $usercon->findByAll($user, array("id", "date", "time", "password", "signup_stage"), null, $persistent_connection);
        if (sizeof($users) == 1) {
            $user_id = $users[0]->getId();
            $date = $users[0]->getDate();
            $time = $users[0]->getTime();
            if ($users[0]->getPassword() == securePassword($password, $user_id, $date, $time) && $users[0]->getSignup_stage() > 0) {
                $users[0]->setOnline(1);
                $usercon->update($users[0], $persistent_connection);

                $query = "select id,name,image_src from category";
                $statement = $persistent_connection->prepare($query);
                $statement->execute();
                $statement->bind_result($category_id, $category_name, $image_src);
                $i = 0;
                $categories = array();
                while ($statement->fetch()) {
                    $categories[$i] = array(
                        "id" => $category_id,
                        "name" => $category_name,
                        "image_src" => $image_src
                    );
                    $i++;
                }

                $db_connection->mysqli_connect_close();
                session_start();
                $_SESSION["id"] = $user_id;
                $_SESSION["categories"] = $categories;
                echo "1";
            } else {
                $db_connection->mysqli_connect_close();
                echo "<span style='color:red'>Invalid Email ID or Password</span>";
            }
        } else {
            $db_connection->mysqli_connect_close();
            echo "<span style='color:red'>Invalid Email ID or Password</span>";
        }
        return;
    }
} else if ($req == "signup") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email_id = trim($_POST["email_id"]);
    $password = trim($_POST["password"]);
    $dob = trim($_POST["dob"]);

    if (strlen($first_name) == 0 || strlen($last_name) == 0 || strlen($email_id) == 0 || strlen($password) == 0 || strlen($dob) == 0) {
        echo "<span style='color:red'>One or more fields is empty.</span>";
        return;
    }
    if (validateEmailAddress($email_id) == false) {
        echo "<span style='color:red'>Invalid Email address</span>";
        return;
    }
    $date = substr($dob, 0, strpos($dob, " "));
    if (strlen($date) == 0) {
        echo "<span style='color:red'>Invalid date format</span>";
        return;
    } else {
        if (strlen($date) == 1)
            $date = "0" . $date;
        $month = getMonthNumber(substr($dob, strpos($dob, " ") + 1, strpos($dob, ",") - (strpos($dob, " ") + 1)));
        if ($month == 0) {
            echo "<span style='color:red'>Invalid date format</span>";
            return;
        } else {
            $year = substr($dob, strpos($dob, ", ") + 2);
            if (strlen($year) != 4) {
                echo "<span style='color:red'>Invalid date format</span>";
                return;
            } else {
                $dob = $year . "/" . $month . "/" . $date;
                if (checkUserAge($date, $month, $year) == false) {
                    echo "<span style='color:red'>You must be 13 year or older.</span>";    //underage
                    return;
                } else {
                    $user = new User();
                    $usercon = new UserController();
                    $user->setEmail_id($email_id);

                    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
                    $persistent_connection = $db_connection->getCon();
                    $users = $usercon->findByAll($user, array("id"), null, $persistent_connection);
                    if (sizeof($users) > 0) {
                        $db_connection->mysqli_connect_close();
                        echo "<span style='color:red'>This email address is already in use.</span>";    //user already exists
                        return;
                    } else {
                        $token = sha1($email_id . date("U") . rand(0, 1000));

                        $user->setFirst_name($first_name);
                        $user->setLast_name($last_name);
                        $user->setEmail_id($email_id);
                        $user->setPassword($password);
                        $user->setDob($dob);
                        $id = $usercon->insert($user, $persistent_connection);

                        if ($id == null) {
                            $db_connection->mysqli_connect_close();
                            echo "<span style='color:red'>Some error occured while signing you up. Please try again later.</span>";
                            return;
                        } else {
                            $user = $usercon->getByPrimaryKey($id, array("date", "time"), null, $persistent_connection);
                            $user->setPassword(securePassword($password, $id, $user->getDate(), $user->getTime()));
                            $user->setId($id);
                            $usercon->update($user, $persistent_connection);

                            $stage = new Stage();
                            $stagecon = new StageController();
                            $stage->setStatus(1);
                            $stages = $stagecon->findByAll($stage, array("id"), null, $persistent_connection);
                            $stages_size = sizeof($stages);

                            $user_stage = new User_stage();
                            $user_stagecon = new User_stageController();
                            for ($i = 0; $i < $stages_size; $i++) {
                                $user_stage->setUser_id($id);
                                $user_stage->setStage_id($stages[$i]->getId());
                                $user_stagecon->insert($user_stage, $persistent_connection);
                            }

                            $email_token = new Email_token();
                            $email_tokencon = new Email_tokenController();
                            $email_token->setPurpose("signup");
                            $email_token->setUser_id($id);
                            $email_token->setToken($token);
                            $email_tokencon->insert($email_token, $persistent_connection);
                            $db_connection->mysqli_connect_close();
                            //sendSignupEmail($email_id,$first_name,$last_name,$token);
                            echo "An email has been sent to you. Please click the link in the email to activate your account...<a href='http://127.0.0.1/NEMO/activateaccount.php?token=" . $token . "'>CLICK ME</a>";
                        }
                    }
                }
            }
        }
    }
} else if ($req == "ss1over") {
    $user_id = $_POST["user_id"];
    if ($user_id == null || $user_id <= 0) {
        echo "Invalid action.";
        return;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $user_stage = new User_stage();
        $user_stagecon = new User_stageController();
        $user_stage->setUser_id($user_id);
        $user_stage->setStage_id(1);
        $user_stage = $user_stagecon->findByAll($user_stage, array("id"), null, $persistent_connection)[0];
        $user_stage->setStatus(1);
        $user_stagecon->update($user_stage, $persistent_connection);

        $user = new User();
        $usercon = new UserController();
        $user->setId($user_id);
        $user->setSignup_stage(2);
        $usercon->update($user, $persistent_connection);
        $db_connection->mysqli_connect_close();
    }
} else if ($req == "change_profile_pic") {
    $real_address = $_POST["radd"];
    $cords = json_decode($_POST["coords"]);
    $user_id = $_POST["user_id"];
    if (strlen($real_address) == 0 || sizeof($cords) != 6 || strlen($user_id) == 0 || $user_id < 1) {
        return -1;
    }
    $new_address = cropNSave($real_address, $cords, 1, "profile_pic");

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $usercon = new UserController();
    $user = $usercon->getByPrimaryKey($user_id, array("profile_pic"), null, $persistent_connection);
    unlink($user->getProfile_pic());
    $user->setId($user_id);
    $user->setProfile_pic($new_address);
    $usercon->update($user, $persistent_connection);
    $db_connection->mysqli_connect_close();
    echo $new_address;
} else if ($req == "change_cover_pic") {
    $real_address = $_POST["radd"];
    $cords = json_decode($_POST["coords"]);
    $user_id = $_POST["user_id"];
    if (strlen($real_address) == 0 || sizeof($cords) != 6 || strlen($user_id) == 0 || $user_id < 1) {
        return -1;
    }
    $new_address = cropNSave($real_address, $cords, 1, "cover_pic");
    if ($new_address == -2 || $new_address == -1) {
        echo $new_address;
        return;
    }

    $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
    $persistent_connection = $db_connection->getCon();
    $usercon = new UserController();
    $user = $usercon->getByPrimaryKey($user_id, array("cover_pic"), null, $persistent_connection);
    unlink($user->getCover_pic());
    $user->setId($user_id);
    $user->setCover_pic($new_address);
    $usercon->update($user, $persistent_connection);
    $db_connection->mysqli_connect_close();
    echo $new_address;
} else if ($req == "edit_profile") {
    $user_id = $_POST["user_id"];
    $about_me = trim($_POST["about_me"]);
    $nick = trim($_POST["nick"]);
    $gender = trim($_POST["gender"]);
    $dob = trim($_POST["dob"]);
    $rel = trim($_POST["rel"]);
    $email_id = trim($_POST["email_id"]);

    if ($user_id == null || strlen($user_id) == 0) {
        echo -1;
        return;
    }
    if ($about_me == null || strlen($about_me) == 0) {
        echo -1;
        return;
    }
    if ($gender == null || strlen($gender) == 0 || ($gender != 'm' && $gender != 'f' && $gender != 't')) {
        echo -1;
        return;
    }
    if ($dob == null || strlen($dob) == 0) {
        echo -1;
        return;
    }
    if ($rel == null || strlen($rel) == 0 || $rel < 1 || $rel > 5) {
        echo -1;
        return;
    }
    if ($email_id == null || strlen($email_id) == 0 || validateEmailAddress($email_id) == false) {
        echo -1;
        return;
    }

    $dob_array = explode(" ", $dob);
    $dob = substr($dob_array[2], 1) . "-" . getMonthNumber($dob_array[1]) . "-" . $dob_array[0];
    echo $dob;

    $user = new User();
    $usercon = new UserController();
    $user->setId($user_id);
    $user->setAbout_me($about_me);
    $user->setNick($nick);
    $user->setGender($gender);
    $user->setDob($dob);
    $user->setRel_status($rel);
    $user->setEmail_id($email_id);
    return $usercon->update($user, null);
} else if ($req == "change_password") {
    $old_password = $_POST["op"];
    $new_password = $_POST["np"];
    $renew_password = $_POST["rnp"];
    $user_id = $_POST["user_id"];

    if (strlen($user_id == 0)) {
        echo 0;
        return;
    }
    if (strpos($new_password, ' ') != false) {
        echo -1;
        return;
    } else if (strlen(trim($old_password)) == 0 || strlen(trim($new_password)) == 0 || strlen(trim($renew_password)) == 0) {
        echo -2;
        return;
    } else if ($renew_password != $new_password) {
        echo -3;
        return;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $user = new User();
        $usercon = new UserController();
        $user = $usercon->getByPrimaryKey($user_id, array("password", "date", "time"), null, $persistent_connection);

        if ($user->getPassword() != securePassword($old_password, $user_id, $user->getDate(), $user->getTime())) {
            echo 2;
            return;
        } else {
            $user->setId($user_id);
            $user->setPassword(securePassword($new_password, $user_id, $user->getDate(), $user->getTime()));
            $usercon->update($user, $persistent_connection);
        }
        $db_connection->mysqli_connect_close();
        echo 1;
    }
} else if ($req == "accsett") {
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    $email = trim($_POST["email"]);
    $his = trim($_POST["his"]);
    $user_id = trim($_POST["user_id"]);

    if (strlen($user_id == 0)) {
        echo -1;
        return;
    }
    if ($his != 0 && $his != 1) {
        echo -1;
        return;
    }
    if (strlen($fname) == 0 || strlen($lname) == 0 || strlen($email) == 0 || validateEmailAddress($email) == false) {
        echo -1;
        return;
    } else {
        $db_connection = new DBConnect("mysqli", "nemo", "", "", "");
        $persistent_connection = $db_connection->getCon();

        $user = new User();
        $usercon = new UserController();
        $user->setId($user_id);
        $user->setFirst_name($fname);
        $user->setLast_name($lname);
        $user->setEmail_id($email);
        $usercon->update($user, $persistent_connection);
        
        $db_connection->mysqli_connect_close();
        echo 1;
    }
} else {
    header("location:badpage.html");
}
?>