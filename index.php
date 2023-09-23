<?php 
require 'functions/functions.php';
if (isset($_COOKIE['token']))
	if(_is_session_valid($_COOKIE['token']))
		header("location:home.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['login'])) {
		$userlogin  = $_POST['userlogin'];
		$userpass   = $_POST['userpass'];
		
		$query = $conn->query(
			sprintf(
				"SELECT * FROM users WHERE user_email = '%s' OR  user_nickname = '%s'",
				$conn->real_escape_string($userlogin),
				$conn->real_escape_string($userlogin)
			)
		);
		if($query){
			$p = 0;
			if($query->num_rows == 1) {
				$p = 1;
				$row = $query->fetch_assoc();
				if(password_verify($userpass, $row['user_password'])){
					$p = 2;
					if(isset($_POST['remember_me']))
						$time = 86400*365;
					else
						$time = 86400*30;
					_setcookie("token", $row['user_token'], $time);
					header("Location: home.php");
				}
			}
			header("Location: ?err=invalid_login&p=$p");
		}
	}
	if (isset($_POST['register'])) {
		$userfirstname  = $_POST['userfirstname'];
		$userlastname   = $_POST['userlastname'];
		$usernickname   = $_POST['usernickname'];
		$userpassword   = password_hash($_POST['userpass'], PASSWORD_DEFAULT);
		$useremail      = $_POST['useremail'];
		$userbirthdate  = $_POST['selectyear'] . '-' . $_POST['selectmonth'] . '-' . $_POST['selectday'];
		$usergender     = $_POST['usergender'];
		$userhometown   = $_POST['userhometown'];
		$userabout      = $_POST['userabout'];
		$user_token     = _generate_token();
		
		if (isset($_POST['userstatus']))
			$userstatus = $_POST['userstatus'];
		else
			$userstatus = NULL;
		// Check for Some Unique Constraints
		$query = $conn->query(
			sprintf(
				"SELECT user_nickname, user_email FROM users WHERE user_nickname = '%s' OR user_email = '%s'",
				$conn->real_escape_string($usernickname),
				$conn->real_escape_string($useremail)
			)
		);
		if($query->num_rows > 0){
			$row = $query->fetch_assoc();
			if(strtolower($usernickname) == strtolower($row['user_nickname']) && !empty($usernickname)){
				header("Location:?err=exist_nickname");
			}
			if(strtolower($useremail) == strtolower($row['user_email'])){
				header("Location:?err=exist_email");
			}
		}else{
			$sql = 
			sprintf(
				"INSERT INTO users(user_firstname, user_lastname, user_nickname, user_password, user_email, user_gender, user_birthdate, user_status, user_about, user_hometown, user_token) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
				$conn->real_escape_string($userfirstname),
				$conn->real_escape_string($userlastname),
				$conn->real_escape_string($usernickname),
				$conn->real_escape_string($userpassword),
				$conn->real_escape_string($useremail),
				$conn->real_escape_string($usergender),
				$conn->real_escape_string($userbirthdate),
				$conn->real_escape_string($userstatus),
				$conn->real_escape_string($userabout),
				$conn->real_escape_string($userhometown),
				$conn->real_escape_string($user_token)
			);
			$query = $conn->query($sql);
			if($query){
				_setcookie("token", $user_token, 86400*90);
				header("location:home.php");
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Lunar Freedom Social</title>
	<link rel="stylesheet" type="text/css" href="resources/css/main.css">
	<style>
		.container{
			margin: 40px auto;
			width: 400px;
		}
		.content {
			color: #ffffff;
			padding: 30px;
			background-color: #0f0f0f;
			box-shadow: 0 0 5px #4267b2;
			border-radius: 0px 0px 15px 15px;
		}
		.tab{
			box-shadow: 0 0 5px #4267b2;
			border-radius: 15px 15px 0px 0px;
		}
	</style>
</head>
<body>
	<h1>Welcome to Lunar Freedom Social</h1>
	<div class="container">
		<div class="tab">
			<button class="tablink active" onclick="openTab(event,'signin')" id="link1">Sign In</button>
			<button class="tablink" onclick="openTab(event,'signup')" id="link2">Sign Up</button>
		</div>
		<div class="content">
			<div class="tabcontent" id="signin">
				<form method="post" onsubmit="return validateLogin()">
					<label>Login<span>*</span></label><br>
					<input type="text" name="userlogin" id="loginuseremail">
					<div class="required"></div>
					<br>
					<label>Password<span>*</span></label><br>
					<input type="password" name="userpass" id="loginuserpass">
					<div class="required"></div>
					<br>
					<label>Remember Me? </label>
					<input type="checkbox" name="remember_me" id="remember-me">
					<br><br>
					<input type="submit" value="Login" name="login">
				</form>
			</div>
			<div class="tabcontent" id="signup">
				<form method="post" onsubmit="return validateRegister()">
					<!--Package One-->
					<h2>Highly Required Information</h2>
					<hr>
					<!--First Name-->
					<label>First Name<span>*</span></label><br>
					<input type="text" name="userfirstname" id="userfirstname">
					<div class="required"></div>
					<br>
					<!--Last Name-->
					<label>Last Name<span>*</span></label><br>
					<input type="text" name="userlastname" id="userlastname">
					<div class="required"></div>
					<br>
					<!--Nickname-->
					<label>Nickname</label><br>
					<input type="text" name="usernickname" id="usernickname">
					<div class="required"></div>
					<br>
					<!--Password-->
					<label>Password<span>*</span></label><br>
					<input type="password" name="userpass" id="userpass">
					<div class="required"></div>
					<br>
					<!--Confirm Password-->
					<label>Confirm Password<span>*</span></label><br>
					<input type="password" name="userpassconfirm" id="userpassconfirm">
					<div class="required"></div>
					<br>
					<!--Email-->
					<label>Email<span>*</span></label><br>
					<input type="text" name="useremail" id="useremail">
					<div class="required"></div>
					<br>
					<!--Birth Date-->
					Birth Date<span>*</span><br>
					<select name="selectday">
					<?php
					for($i=1; $i<=31; $i++){
						echo '<option value="'. $i .'">'. $i .'</option>';
					}
					?>
					</select>
					<select name="selectmonth">
					<?php
					echo '<option value="1">January</option>';
					echo '<option value="2">February</option>';
					echo '<option value="3">March</option>';
					echo '<option value="4">April</option>';
					echo '<option value="5">May</option>';
					echo '<option value="6">June</option>';
					echo '<option value="7">July</option>';
					echo '<option value="8">August</option>';
					echo '<option value="9">September</option>';
					echo '<option value="10">October</option>';
					echo '<option value="11">Novemeber</option>';
					echo '<option value="12">December</option>';
					?>
					</select>
					<select name="selectyear">
					<?php
					$y = date("Y", time());
					for($i=$y; $i>=1900; $i--){
						if($i == 2023){
							echo '<option value="'. $i .'" selected>'. $i .'</option>';
						}
						echo '<option value="'. $i .'">'. $i .'</option>';
					}
					?>
					</select>
					<br><br>
					<!--Gender-->
					<input type="radio" name="usergender" value="M" id="malegender" class="usergender">
					<label>Male</label>
					<input type="radio" name="usergender" value="F" id="femalegender" class="usergender">
					<label>Female</label>
					<div class="required"></div>
					<br>
					<!--Hometown-->
					<label>Hometown</label><br>
					<input type="text" name="userhometown" id="userhometown">
					<br>
					<!--Package Two-->
					<h2>Additional Information</h2>
					<hr>
					<!--Marital Status-->
					<input type="radio" name="userstatus" value="S" id="singlestatus">
					<label>Single</label>
					<input type="radio" name="userstatus" value="E" id="engagedstatus">
					<label>Engaged</label>
					<input type="radio" name="userstatus" value="M" id="marriedstatus">
					<label>Married</label>
					<br><br>
					<!--About Me-->
					<label>About Me</label><br>
					<textarea rows="12" name="userabout" id="userabout"></textarea>
					<br><br>
					<input type="submit" value="Create Account" name="register">
				</form>
			</div>
		</div>
	</div>
	<script src="resources/js/main.js"></script>
	<?php
	if(isset($_GET['err'])){
		$err = $_GET['err'];
		if($err == "exist_email"){
			echo '<script>';
			echo 'document.getElementsByClassName("required")[7].innerHTML = "This Email already exists.";';
			echo '</script>';
		}
		if($err == "exist_nickname"){
			echo '<script>';
			echo 'document.getElementsByClassName("required")[4].innerHTML = "This Nickname already exists.";';
			echo '</script>';
		}
		if($err == "invalid_login"){
			echo '<script>';
			echo 'document.getElementsByClassName("required")[0].innerHTML = "Invalid Login Credentials.";';
			echo 'document.getElementsByClassName("required")[1].innerHTML = "Invalid Login Credentials.";';
			echo '</script>';
		}
	}
	?>
</body>
</html>