<?php
if (!isset($_COOKIE['token']))
	header("location:index.php");
require_once __DIR__ . '/functions.php';
if (!_is_session_valid($_COOKIE['token']))
	header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if ((isset($_POST['profile']) || isset($_POST['post']) && isset($_FILES["fileUpload"]))){
		$filename = basename($_FILES["fileUpload"]["name"]);
		$filetype = pathinfo($filename, PATHINFO_EXTENSION);
		if($filetype != "png" && $filetype != "jpg" && $filetype!= "jpeg" && $filetype != "gif")
			echo 'Only JPG, JPEG, PNG & GIF formats are allowed.';
		if(exif_imagetype($_FILES["fileUpload"]["tmp_name"])){
			$media_hash = md5_file($_FILES["fileUpload"]["tmp_name"]);
			$filepath = __DIR__ . "/../data/images/image/$media_hash.bin";
			$media_format = mime_content_type($_FILES["fileUpload"]["tmp_name"]);
			$sql6 = "SELECT * FROM media WHERE media_hash = '$media_hash'";
			$query6 = $conn->query($sql6);
			if($query6->num_rows == 0){
				$sql6 = "INSERT INTO media (media_format, media_hash, media_ext) VALUES ('$media_format','$media_hash', '$filetype')";
				$query6 = $conn->query($sql6);
				$media_id = $conn->insert_id;
			}else{
				$media_id = $query6->fetch_assoc()["media_id"];
			}
			if(isset($_POST['profile'])){
				$success = 0;
				if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $filepath)){
					$sql5 = "INSERT INTO posts (post_caption, post_public, post_time, post_by, post_media)
							VALUES ('" . $data['user_firstname'] . " " . $data['user_lastname'] . " has changed profile picture.', 'Y', NOW(), {$data['user_id']}, $media_id)";
					$query5 = $conn->query($sql5);
					$sql7 = "UPDATE users SET pfp_media_id = $media_id WHERE user_id = {$data['user_id']}";
					$query7 = $conn->query($sql7);
					//$success = 1;

				}
				if($success = 1)
					header("Location:profile.php");
			}
			if(isset($_POST['post'])){
				if(isset($last_id) && $media_id){
					$sql = "UPDATE posts SET post_media = $media_id WHERE post_id = $last_id";
					$query = $conn->query($sql);
				}
				move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $filepath);
			}
		}
	}
}
?>