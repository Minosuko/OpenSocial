<?php
// Check whether user is logged on or not
if (!isset($_COOKIE['token']))
    header("location:index.php");
require_once '../functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
    header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
$off = 0;
$esql = '';
if(isset($_POST['page']))
	if(is_numeric($_POST['page']))
		$off = 30*$_POST['page'];
if($off != 0)
	$esql = " LIMIT 30 OFFSET $off";
$sql = "SELECT posts.post_caption, posts.post_time, posts.post_public, users.user_firstname, users.user_lastname, users.user_id, users.user_gender, posts.post_id, posts.post_media, posts.is_share, users.pfp_media_id FROM posts JOIN users ON posts.post_by = users.user_id WHERE posts.post_public = 2 OR users.user_id = {$data['user_id']} UNION SELECT posts.post_caption, posts.post_time, posts.post_public, users.user_firstname, users.user_lastname, users.user_id, users.user_gender, posts.post_id, posts.post_media, posts.is_share, users.pfp_media_id FROM posts JOIN users ON posts.post_by = users.user_id JOIN (SELECT friendship.user1_id AS user_id FROM friendship WHERE friendship.user2_id = {$data['user_id']} AND friendship.friendship_status = 1 UNION SELECT friendship.user2_id AS user_id FROM friendship WHERE friendship.user1_id = {$data['user_id']} AND friendship.friendship_status = 1) userfriends ON userfriends.user_id = posts.post_by WHERE posts.post_public = 1 ORDER BY post_time DESC$esql";
$query = $conn->query($sql);
$total_rows = $query->num_rows;
if($total_rows == 0){
	echo '<div class="post">';
	echo 'There are no posts yet to show.';
	echo '</div>';
}else{
	$width = '40px'; // Profile Image Dimensions
	$height = '40px';
	$r = 30;
	if($total_rows < 30)
		$r = $total_rows;
	$rows = $query->fetch_all(MYSQLI_ASSOC);
	for($i = 0; $i < $r; $i++){
		$row = $rows[$i];
		include '../includes/post.php';
		echo '<br>';
	}
}
?>