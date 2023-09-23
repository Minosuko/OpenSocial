<?php
if(!isset($IFI)) die("");
if(isset($fsp)){
	if($sdata['pfp_media_id'] > 0) {
		$target = "data/images.php?t=profile&id={$sdata['pfp_media_id']}&h="._get_hash_from_media_id($sdata['pfp_media_id']);
		echo '<img class="pfp" src="' . $target . '" width="' . $width . '" height="' . $height .'">'; 
	} else {
		if($sdata['user_gender'] == 'M')
			echo '<img class="pfp" src="data/images.php?t=default_M" width="' . $width . '" height="' . $height .'">';
		else if ($sdata['user_gender'] == 'F')
			echo '<img class="pfp" src="data/images.php?t=default_F" width="' . $width . '" height="' . $height .'">';
	}
}else{
	if($row['pfp_media_id'] > 0) {
		$target = "data/images.php?t=profile&id={$row['pfp_media_id']}&h="._get_hash_from_media_id($row['pfp_media_id']);
		echo '<img class="pfp" src="' . $target . '" width="' . $width . '" height="' . $height .'">'; 
	} else {
		if($row['user_gender'] == 'M')
			echo '<img class="pfp" src="data/images.php?t=default_M" width="' . $width . '" height="' . $height .'">';
		else if ($row['user_gender'] == 'F')
			echo '<img class="pfp" src="data/images.php?t=default_F" width="' . $width . '" height="' . $height .'">';
	}
}
?>