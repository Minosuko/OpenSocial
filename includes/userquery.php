<?php
if(!isset($IFI))
	die("");
$query = $conn->query($sql);
$width = '168px';
$height = '168px';
if($query->num_rows == 0){
    echo '<div class="userquery">';
    echo 'We couldn\'t find any results for these keywords: ' . $key;
    echo '<br><br>';
    echo '</div>';
} else {
    while($row = $query->fetch_assoc()){
        echo '<div class="userquery">';
        include 'includes/profile_picture.php';
        echo '<br>';
        echo '<a class="profilelink" href="profile.php?id=' . $row['user_id'] .'">' . $row['user_firstname'] . ' ' . $row['user_lastname'] . '<a>';
        echo '</div>';
        echo '<br>';
    }
}


?>