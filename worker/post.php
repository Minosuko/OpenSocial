<?php
// Check whether user is logged on or not
if (!isset($_COOKIE['token']))
    header("location:../index.php");
require_once '../functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
    header("location:../index.php");
$data = _get_data_from_token($_COOKIE['token']);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['private']) && isset($_POST['caption'])){
		$caption = $_POST['caption'];
		if($_POST['private'] == "N")
			$public = 1;
		else
			$public = 2;
		$poster = $data['user_id'];
		$sql = sprintf(
			"INSERT INTO posts (post_caption, post_public, post_time, post_by) VALUES ('%s', '$public', NOW(), $poster)",
			$conn->real_escape_string($caption)
		);
		$query = $conn->query($sql);
		if($query){
			if (isset($_FILES['fileUpload'])) {
				$last_id = $conn->insert_id;
				include '../functions/upload.php';
			}
			echo "success";
		}
	}
}
?>