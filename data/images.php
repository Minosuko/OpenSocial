<?php
if (!isset($_COOKIE['token']))
    header("location:index.php");
require_once '../functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
    header("location:index.php");
if(isset($_GET['t'])){
	$type = $_GET['t'];
	if($type == "default_M"){
		header("Content-Type: image/jpeg");
		readfile("images/M.jpg");
	}
	if($type == "default_F"){
		header("Content-Type: image/jpeg");
		readfile("images/F.jpg");
	}
	if($type == 'profile')
		if(isset($_GET['id']))
			if(is_numeric($_GET['id']))
				if(isset($_GET['h']))
					if(file_exists("images/image/{$_GET['h']}.bin")){
						$md5 = $_GET['h'];
						$query = $conn->query(
							sprintf(
								"SELECT * FROM media WHERE media_hash = '%s' AND media_id = %d",
								$conn->real_escape_string($md5),
								$conn->real_escape_string($_GET['id'])
							)
						);
						if($query->num_rows > 0){
							$fetch = $query->fetch_assoc();
							header('Content-Disposition: filename="'.$md5.'.'.$fetch['media_ext'].'"');
							header("Content-Type: {$fetch['media_format']}");
							readfile("images/image/$md5.bin");
						}
					}
	if($type == "media")
		if(isset($_GET['id']))
			if(is_numeric($_GET['id']))
				if(isset($_GET['h']))
					if(file_exists("images/image/{$_GET['h']}.bin")){
						$md5 = $_GET['h'];
						$query = $conn->query(
							sprintf(
								"SELECT * FROM media WHERE media_hash = '%s' AND media_id = %d",
								$conn->real_escape_string($md5),
								$conn->real_escape_string($_GET['id'])
							)
						);
						if($query->num_rows > 0){
							$fetch = $query->fetch_assoc();
							header('Content-Disposition: filename="'.$md5.'.'.$fetch['media_ext'].'"');
							header("Content-Type: {$fetch['media_format']}");
							readfile("images/image/$md5.bin");
						}
					}
}
?>