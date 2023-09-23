<?php 
if (!isset($_COOKIE['token']))
	header("location:index.php");
require_once 'functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
	header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
if(isset($_GET['id']) && $_GET['id'] != $data['user_id']) {
	$current_id = $conn->real_escape_string($_GET['id']);
	$flag = 1;
} else {
	$current_id = $data['user_id'];
	$flag = 0;
}
if(!is_user_exists($current_id)){
	$current_id = $data['user_id'];
	$flag = 0;
}
if(!is_numeric($current_id))
	header("Location:?id=1");
include 'functions/upload.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['request'])){
		if($data['user_id'] != $current_id)
			$check = $conn->query("SELECT * FROM friendship WHERE user1_id = {$data['user_id']} AND user2_id = $current_id");
			if($check->num_rows == 0)
				$sql3 = "INSERT INTO friendship(user1_id, user2_id, friendship_status) VALUES ({$data['user_id']}, $current_id, 0)";
	}elseif(isset($_POST['remove']))
		$sql3 = "DELETE FROM friendship
				 WHERE ((friendship.user1_id = $current_id AND friendship.user2_id = {$data['user_id']})
				 OR (friendship.user1_id = {$data['user_id']} AND friendship.user2_id = $current_id))";
	elseif(isset($_POST['phone']))
		$sql3 = "INSERT INTO user_phone(user_id, user_phone) VALUES ({$data['user_id']},{$_POST['number']})";
	if(isset($sql3))
		$query3 = $conn->query($sql3);
	sleep(4);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Social Network</title>
	<link rel="stylesheet" type="text/css" href="resources/css/main.css">
	<link rel="stylesheet" type="text/css" href="resources/css/font-awesome/all.css">
	<style>
	.post{
		margin-right: 50px;
		float: right;
		margin-bottom: 18px;
	}
	.profile{
		margin-left: 50px;
		background-color: #121212;
		box-shadow: 0 0 5px #4267b2;
		width: 220px;
		padding: 20px;
		color: white;
		border-radius: 15px;
	}
	.profile img{
		object-fit: contain;
		background-color: #ffffff;
		border-radius: 25%;
	}
	input[type="file"]{
		display: none;
	}
	label.upload{
		cursor: pointer;
		color: white;
		background-color: #4267b2;
		padding: 8px 12px;
		display: inline-block;
		max-width: 80px;
		overflow: auto;
	}
	label.upload:hover{
		background-color: #23385f;
	}
	.changeprofile{
		color: #23385f;
	}
	</style>
</head>
<body>
	<div class="container">
		<?php include 'includes/navbar.php'; ?>
		<h1>Profile</h1>
		<?php
		$postsql;
		if($flag == 0) { // Your Own Profile	   
			$postsql = "SELECT posts.post_caption, posts.post_time, users.user_firstname, users.user_lastname,
								posts.post_public, users.user_id, users.user_gender, users.user_nickname,
								users.user_birthdate, users.user_hometown, users.user_status, users.user_about, 
								posts.post_id, users.pfp_media_id, posts.post_media, posts.is_share
						FROM posts
						JOIN users
						ON users.user_id = posts.post_by
						WHERE posts.post_by = $current_id
						ORDER BY posts.post_time DESC";
			$profilesql = "SELECT users.user_id, users.user_gender, users.user_hometown, users.user_status, users.user_birthdate, users.user_firstname, users.user_lastname, users.pfp_media_id, users.user_about
						  FROM users
						  WHERE users.user_id = $current_id";
			$profilequery = $conn->query($profilesql);
		} else { // Another Profile ---> Retrieve User data and friendship status
			$profilesql = "SELECT users.user_id, users.user_gender, users.user_hometown, users.user_status, users.user_birthdate, users.user_firstname, users.user_lastname, userfriends.friendship_status, users.pfp_media_id, users.user_about
							FROM users
							LEFT JOIN (
								SELECT friendship.user1_id AS user_id, friendship.friendship_status
								FROM friendship
								WHERE friendship.user1_id = $current_id AND friendship.user2_id = {$data['user_id']}
								UNION
								SELECT friendship.user2_id AS user_id, friendship.friendship_status
								FROM friendship
								WHERE friendship.user1_id = {$data['user_id']} AND friendship.user2_id = $current_id
							) userfriends
							ON userfriends.user_id = users.user_id
							WHERE users.user_id = $current_id";
			$profilequery = $conn->query($profilesql);
			$row = $profilequery->fetch_assoc();
			$profilequery->data_seek(0);
			if(isset($row['friendship_status'])){ // Either a friend or requested as a friend
				if($row['friendship_status'] == 1){ // Friend
					$postsql = "SELECT posts.post_caption, posts.post_time, users.user_firstname, users.user_lastname,
										posts.post_public, users.user_id, users.user_gender, users.user_nickname,
										users.user_birthdate, users.user_hometown, users.user_status, users.user_about, 
										posts.post_id, users.pfp_media_id, posts.post_media, posts.is_share
								FROM posts
								JOIN users
								ON users.user_id = posts.post_by
								WHERE posts.post_by = $current_id
								ORDER BY posts.post_time DESC";
				}
				else if($row['friendship_status'] == 0){ // Requested as a Friend
					$postsql = "SELECT posts.post_caption, posts.post_time, users.user_firstname, users.user_lastname,
										posts.post_public, users.user_id, users.user_gender, users.user_nickname,
										users.user_birthdate, users.user_hometown, users.user_status, users.user_about, 
										posts.post_id, users.pfp_media_id, posts.post_media, posts.is_share
								FROM posts
								JOIN users
								ON users.user_id = posts.post_by
								WHERE posts.post_by = $current_id AND posts.post_public = 2
								ORDER BY posts.post_time DESC";
				}
			} else { // Not a friend
				$postsql = "SELECT posts.post_caption, posts.post_time, users.user_firstname, users.user_lastname,
									posts.post_public, users.user_id, users.user_gender, users.user_nickname,
									users.user_birthdate, users.user_hometown, users.user_status, users.user_about, 
									posts.post_id, users.pfp_media_id, posts.post_media, posts.is_share
							FROM posts
							JOIN users
							ON users.user_id = posts.post_by
							WHERE posts.post_by = $current_id AND posts.post_public = 2
							ORDER BY posts.post_time DESC";
			}
		}
		$postquery = $conn->query($postsql);	
		if($postquery){
			// Posts
			$width = '40px'; 
			$height = '40px';
			if($postquery->num_rows == 0){ // No Posts
				if($flag == 0){ // Message shown if it's your own profile
					echo '<div class="post">';
					echo 'You don\'t have any posts yet';
					echo '</div>';
				} else { // Message shown if it's another profile other than you.
					echo '<div class="post">';
					echo 'There is no public posts to show.';
					echo '</div>';
				}
				include 'includes/profile.php';
			} else {
				while($row = $postquery->fetch_assoc()){
					include 'includes/post.php';
				}
				// Profile Info
				include 'includes/profile.php';
				if($flag == 0){
				?>
				<br>
				<div class="profile">
					<center class="changeprofile">Change Profile Picture</center>
					<br>
					<form action="" method="post" enctype="multipart/form-data">
						<center>
							<label class="upload" onchange="showPath()">
								<span id="path" style="color: white;">... Browse</span>
								<input type="file" name="fileUpload" id="selectedFile">
							</label>
						</center>
						<br>
						<input type="submit" value="Upload Image" name="profile">
					</form>
				</div>
				<br>
				<div class="profile">
					<center class="changeprofile">Add Phone Number</center>
					<br>
					<form method="post" onsubmit="return validateNumber()">
						<center>
							<input type="text" name="number" id="phonenum">
							<div class="required"></div>
							<br>
							<input type="submit" value="Submit" name="phone">
						</center>
					</form>
				</div>
				<br>
				<?php
				}
			}
		}
		?>
	</div>
</body>
<script>
function showPath(){
	var path = document.getElementById("selectedFile").value;
	path = path.replace(/^.*\\/, "");
	document.getElementById("path").innerHTML = path;
}
function validateNumber(){
	var number = document.getElementById("phonenum").value;
	var required = document.getElementsByClassName("required");
	if(number == ""){
		required[0].innerHTML = "You must type Your Number.";
		return false;
	} else if(isNaN(number)){
		required[0].innerHTML = "Phone Number must contain digits only."
		return false;
	}
	return true;
}
</script>
</html>
