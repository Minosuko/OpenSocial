<?php
if(!isset($IFI)) die("");
echo '<div class="post">';
echo '<div class="header">';
if($row['post_public'] == 2) {
    echo '<p class="public">';
    echo 'Public';
}elseif($row['post_public'] == 1){
    echo '<p class="public">';
    echo 'Friend';
}else{
    echo '<p class="public">';
    echo 'Private';
}
echo '<br>';
echo '<span class="postedtime">' . $row['post_time'] . '</span>';
echo '</p>';
echo '<div>';
include 'profile_picture.php';
echo '<a class="profilelink" href="#" onclick="changeUrl(\'profile.php?id=' . $row['user_id'] .'\');">' . $row['user_firstname'] . ' ' . $row['user_lastname'] . '</a>';
echo'</div>';
echo'</div>';
echo '<br>';
if($row['post_media'] != 0){
	echo '<p class="caption">' . _about_trim($row['post_caption']) . '</p>';
	echo '<center>'; 
	$target = "data/images.php?t=media&id=" . $row['post_media'] . "&h=" . _get_hash_from_media_id($row['post_media']);
	echo '<img src="' . $target . '" style="max-width:100%;">'; 
	echo '<br><br>';
	echo '</center>';
}else{
	echo '<center>'; 
	echo '<p class="caption" style="font-size: 300%;">' . _about_trim($row['post_caption']) . '</p>';
	echo '</center>';
}
echo '<br>';
if($row['is_share'] != 0){
	$sql = "SELECT * FROM posts WHERE post_id = {$row['is_share']}";
	$query = $conn->query($sql);
	$post_data = $query->fetch_assoc();
	$pflag = false;
	echo '<div class="share-post">'; 
	if($post_data['post_public'] == "0" or $post_data['post_public'] == "1"){
		if($post_data['post_by'] == $data['user_id']){
			$pflag = true;
		}else{
			if($post_data['post_public'] == "1")
				if(is_friend($data['user_id'], $post_data['post_by']))
					$pflag = true;
			if($post_data['post_public'] == "0")
				if($data['user_id'] == $post_data['post_by'])
					$pflag = true;
		}
	}else{
		$pflag = true;
	}
	if($pflag){
		echo '<div class="header">';
		if($post_data['post_public'] == 2) {
			echo '<p class="public">';
			echo 'Public';
		}elseif($post_data['post_public'] == 1){
			echo '<p class="public">';
			echo 'Friend';
		}else{
			echo '<p class="public">';
			echo 'Private';
		}
		echo '<br>';
		echo '<span class="postedtime">' . $row['post_time'] . '</span>';
		echo '</p>';
		echo '<div>';
		$fsp = true;
		$sdata = _get_data_from_id($post_data['post_by']);
		include 'profile_picture.php';
		echo '<a class="profilelink" href="#" onclick="changeUrl(\'profile.php?id=' . $sdata['user_id'] .'\');">' . $sdata['user_firstname'] . ' ' . $sdata['user_lastname'] . '</a>';
		unset($fsp);
		echo'</div>';
		echo'</div>';
		if($post_data['post_media'] != 0){
			echo '<p class="caption">' . _about_trim($post_data['post_caption']) . '</p>';
			echo '<center>'; 
			$target = "data/images.php?t=media&id=" . $post_data['post_media'] . "&h=" . _get_hash_from_media_id($post_data['post_media']);
			echo '<img src="' . $target . '" style="max-width:100%;">'; 
			echo '<br><br>';
			echo '</center>';
		}else{
			echo '<center>'; 
			echo '<p class="caption" style="font-size: 300%;">' . _about_trim($post_data['post_caption']) . '</p>';
			echo '</center>';
		}
		echo '<br>';
		
	}else{
		echo '<p style="font-size: 150%;text-align: center">Cant display this post </p>';
	}
	echo'</div>';
}
echo '<div class="bottom">';
echo '<div class="reaction-bottom">';
if(is_liked($data['user_id'],$row['post_id']))
	$liked = 'p-heart fa-solid';
else
	$liked = 'white-col fa-regular';

echo '<div class="reaction-box likes">';
echo '<i onclick="_like('.$row['post_id'].')" class="'.$liked.' icon-heart fa-heart icon-click" id="post-like-'.$row['post_id'].'"></i>';
echo ' <a z-var="counter call roller" id="post-like-count-'.$row['post_id'].'">'.total_like($row['post_id']).'</a>';
echo '</div>';

echo '<div class="reaction-box comment">';
echo '<i onclick="_open_post('.$row['post_id'].')" class="fa-regular fa-comment icon-click" id="post-comment-'.$row['post_id'].'"></i>';
echo ' <a z-var="counter call roller" id="post-comment-count-'.$row['post_id'].'">'.total_comment($row['post_id']).'</a>';
echo '</div>';

echo '<div class="reaction-box share">';
echo '<i onclick="_share('.$row['post_id'].')" class="fa-regular fa-share icon-click" id="post-share-'.$row['post_id'].'"></i>';
echo ' <a z-var="counter call roller" id="post-share-count-'.$row['post_id'].'">'.total_share($row['post_id']).'</a>';
echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';

?>