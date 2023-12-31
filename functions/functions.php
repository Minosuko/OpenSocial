<?php
require_once __DIR__ . "/../config/database.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$conn = new mysqli($host, $username, $dbpassword, $dbdata);
$GLOBALS['conn'] = $conn;
function _setcookie($name, $value, $time){
	$time = time() + $time;
	setcookie($name, $value, $time);
}
function _get_data_from_token($token){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM users WHERE user_token = '%s'",
		$conn->real_escape_string($token)
	);
	$query = $conn->query($sql);
	$fetch = $query->fetch_assoc();
	return $fetch;
}
function _get_data_from_id($id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM users WHERE user_id = %d",
		$conn->real_escape_string($id)
	);
	$query = $conn->query($sql);
	$fetch = $query->fetch_assoc();
	return $fetch;
}
function is_user_exists($id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM users WHERE user_id = %d",
		$conn->real_escape_string($id)
	);
	$query = $conn->query($sql);
	if($query->num_rows > 0)
		return true;
	return false;
}
function is_post_exists($id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM posts WHERE post_id = %d",
		$conn->real_escape_string($id)
	);
	$query = $conn->query($sql);
	if($query->num_rows > 0)
		return true;
	return false;
}
function _get_hash_from_media_id($id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM media WHERE media_id = '%d'",
		$conn->real_escape_string($id)
	);
	$query = $conn->query($sql);
	$fetch = $query->fetch_assoc();
	return $fetch['media_hash'];
}
function _is_session_valid($token){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM users WHERE user_token = '%s'",
		$conn->real_escape_string($token)
	);
	$query = $conn->query($sql);
	if($query->num_rows > 0)
		return true;
	return false;
}
function caesarShift($str, $amount) {
	if ($amount < 0) {
		return caesarShift($str, $amount + 26);
	}
	$output = [];
	for ($i = 0; $i < strlen($str); $i++) {
		$c = $str[$i];
		if (preg_match("/[a-z]/i", $c)) {
			$code = ord($str[$i]);
			if ($code >= 65 && $code <= 90) {
				$c = chr((($code - 65 + $amount) % 26) + 65);
			} elseif ($code >= 97 && $code <= 122) {
				$c = chr((($code - 97 + $amount) % 26) + 97);
			}
		}
		$output[] = $c;
	}
	return implode('', $output);
}
function _generate_token(){
	$gen_str = "QWERTYUIOPASDFGHJKLZXCVBNM0123456789qwertyuiopasdfghjklzxcvbnm";
	$token = "Auth_";
	$caesar = "CaesarAuth";
	$token .= str_replace("=",'',base64_encode(time()));
	$token .= '.';
	for($i = 0;$i < 16 ;$i++)
		$token .= $gen_str[rand(0, 61)];
	for($i = 0;$i < 32 ;$i++)
		$caesar .= $gen_str[rand(0, 61)];
	$caesar = caesarShift($caesar, rand(1, 26));
	$token .= '.';
	$token .= $caesar;
	return $token;
}
function _about_trim($about){
	$html = htmlspecialchars($about);
	$html = str_replace("\n","<br>",$html);
	$html = preg_replace('/\[color=#(\w+|\d+)\](.+)\[\/color\]/', "<a style=\"color: #$1;\">$2</a>", $html);
	$html = preg_replace('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', '<a class="post-link" href="$0" target="_blank">$0</a>', $html);
	return $html;
}
function is_friend($user_id, $target_id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM friendship WHERE user1_id = %d AND user12_id = %d AND friendship_status = 1",
		$conn->real_escape_string($user_id),
		$conn->real_escape_string($target_id)
	);
	$query = $conn->query($sql);
	if($query->num_rows > 0)
		return true;
	return false;
}
function is_liked($user_id, $post_id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM likes WHERE user_id = %d AND post_id = %d",
		$conn->real_escape_string($user_id),
		$conn->real_escape_string($post_id)
	);
	$query = $conn->query($sql);
	if($query->num_rows > 0)
		return true;
	return false;
}
function total_like($post_id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM likes WHERE post_id = %d",
		$conn->real_escape_string($post_id)
	);
	$query = $conn->query($sql);
	return $query->num_rows;
}
function total_share($post_id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM posts WHERE is_share = %d",
		$conn->real_escape_string($post_id)
	);
	$query = $conn->query($sql);
	return $query->num_rows;
}
function total_comment($post_id){
	$conn = $GLOBALS['conn'];
	$sql = sprintf(
		"SELECT * FROM comments WHERE post_id = %d",
		$conn->real_escape_string($post_id)
	);
	$query = $conn->query($sql);
	return $query->num_rows;
}
?>