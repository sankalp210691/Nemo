<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['id'])) {
    header("location:homepage.php");
    return;
}
$req = 0;
if (isset($_GET["req"])) {
    $req = $_GET["req"];
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>NEMO</title>
        <style>
            #login_msg{
                display:none;
            }
        </style>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">
        <link href="css/special.css" rel="stylesheet">
        <script type="text/javascript" src="js/jquery-latest.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/special.js"></script>
        <script type="text/javascript">
            var user_id=-1
            $(document).ready(function(){
                $("#le").focus()
                $("input").keyup(function(e){
                    if(e.keyCode==13){
                       if($(this).hasClass("s")){
                           signup()
                       }else if($(this).hasClass("l")){
                           login()
                       }
                    }
                })
            })
            function login(){
                var err=0
                var email_id = $("#le").val().trim()
                var password = $("#lp").val()
                if(validateEmailAddress(email_id)==false){
                    err=1
                    $("#le").addClass("errorInput")
                }else{
                    $("#le").removeClass("errorInput")
                }
                if(password.length==0){
                    err=1
                    $("#lp").addClass("errorInput")
                }else{
                    $("#lp").removeClass("errorInput")
                }
                if(err==1){
                    return
                }
                var data = [["req","login"],["login_email",email_id],["login_password",password]]
                var loginRequest = new AjaxRequest("manager/UserManager.php",false,"post","",data)
                loginRequest.beforeSend = ["loginBeforeSend"]
                loginRequest.success = ["loginSuccess"]
                loginRequest.error = ["loginError"]
                loginRequest.sendRequest()
            }
            
            function signup(){
                var err=0
                var fname = $("#sfn").val().trim()
                var lname = $("#sln").val().trim()
                var email_id = $("#se").val().trim()
                var password = $("#sp").val().trim()
                var dob = $("#sdob").val().trim()
                $(".s").each(function(){
                    if($(this).val().length==0){
                        $(this).addClass("errorInput")
                        err=1
                    }else{
                        $(this).removeClass("errorInput")
                    }
                })
                if(validateEmailAddress(email_id)==false){
                    $("#se").addClass("errorInput")
                    err=1
                }else{
                    $("#se").removeClass("errorInput")
                }
                if(err==1){
                    return
                }
                var date = dob.substring(0,dob.indexOf(" "))
                var month = dob.substring(dob.indexOf(" ")+1,dob.indexOf(","))
                month = getMonthNumber(month.trim())
                var year = dob.substring(dob.indexOf(", ")+2)
                if(checkUserAge(date,month,year)==false){
                    $("#signup_msg").html("You must be 13 year or older.");    //underage
                    return
                }
                var data = [["req","signup"],["first_name",fname],["last_name",lname],["email_id",email_id],["password",password],["dob",dob]]
                var signupRequest = new AjaxRequest("manager/UserManager.php",false,"post","",data)
                signupRequest.beforeSend = ["signupBeforeSend"]
                signupRequest.success = ["signupSuccess"]
                signupRequest.error = ["signupError"]
                signupRequest.sendRequest()
            }
           
            function loginBeforeSend(data){
                $("#login_msg").html("Signing in...")
                $("#login_msg").show()
            }
           
            function loginSuccess(html){
                if(html!=1){
                    $("#login_msg").html(html)
                    $("#login_msg").show()
                }else{
                    location.reload()
                }
            }
           
            function loginError(e,f){
                $("#login_msg").html("Some error occured")
                $("#login_msg").show()
            }
            
            function signupBeforeSend(data){
                $("#signup_msg").html("Signing up...")
                $("#signup_msg").show()
            }
           
            function signupSuccess(html){
                if(html==1){
                    $("#signup_msg").html("Signed up")
                    $("#signup_msg").show()
                }else{
                    $("#signup_msg").html(html)
                    $("#signup_msg").show()
                }
            }
           
            function signupError(e,f){
                $("#signup_msg").html("Some error occured")
                $("#signup_msg").show()
            }
        </script>
    </head>
    <body>
        <h1>Login</h1>
        <br>
        <form method="post" action="manager/UserManager.php">
            <input type="hidden" value="login" name="req">
            Email Address <input type="text" name="login_email" id="le" class="l" value="sankalp@gmail.com">
            <br>
            Password <input type="password" name="login_password" id="lp" class="l" value="impendia">
            <br>
            <input type="button" value="Login" onclick="login()" class="bbutton">
            <br>
        </form>
        <div id="login_msg"></div>
        <h1>Signup</h1>
        <br>
        <form method="post" action="UserManager.php">
            <input type="hidden" value="signup" name="req">
            First Name <input type="text" name="first_name" id="sfn" class="s">
            <br>
            Last Name <input type="text" name="last_name" id="sln" class="s">
            <br>
            Email Address <input type="text" name="signup_email" id="se" class="s">
            <br>
            Password <input type="password" name="signup_password" id="sp" class="s">
            <br>
            Date of Birth <input type="datepicker" name="signup_dob" id="sdob" class="s">
            <br>
            <input type="button" value="Signup" onclick="signup()" class="bbutton">
        </form>
        <div id="signup_msg"></div>
    </body>
</html>
