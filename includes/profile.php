<?php
if(!isset($IFI)) die("");
echo '<div class="profile">';
echo '<center>';
$row = $profilequery->fetch_assoc();
// Name and Nickname
if(!empty($row['user_nickname']))
    echo $row['user_firstname'] . ' ' . $row['user_lastname'] . ' (' . $row['user_nickname'] . ')';
else
    echo $row['user_firstname'] . ' ' . $row['user_lastname'];
echo '<br>';
// Profile Info & View
$width = '168px';
$height = '168px';
include 'includes/profile_picture.php';
echo '<br>';
// Gender
if($row['user_gender'] == "M")
    echo 'Male';
else if($row['user_gender'] == "F")
    echo 'Female';
echo '<br>';
// Status
if(!empty($row['user_status'])){
    if($row['user_status'] == "S")
        echo 'Single';
    else if($row['user_status'] == "E")
        echo 'Engaged';
    else if($row['user_status'] == "M")
        echo 'Married';
    echo '<br>';
}
// Birthdate
echo $row['user_birthdate'];
// Additional Information
if(!empty($row['user_hometown'])){
    echo '<br>';
    echo $row['user_hometown'];
}
if(!empty($row['user_about'])){
    echo '<br>';
    echo '<br>';
    echo '<h2>About me:</h2>';
    echo _about_trim($row['user_about']);
}
// Friendship Status
if($flag == 1){
    echo '<br>';
    if(isset($row['friendship_status'])) {
        if($row['friendship_status'] == 1){
            echo '<form method="post">';
            echo '<input type="submit" value="Friends" name="remove" id="special">';
            echo '</form>';
        } else if ($row['friendship_status'] == 0){
            echo '<form method="post">';
            echo '<input type="submit" value="Request Pending" name="remove" id="special">';
            echo '</form>';
        }
    } else {
        echo '<form method="post">';
        echo '<input type="submit" value="Send Friend Request" name="request">';
        echo'</form>';
    }
}

echo '<center>'; 
echo'</div>';

$query4 = $conn->query("SELECT * FROM user_phone WHERE user_id = {$row['user_id']}");
if($query4->num_rows > 0){
    echo '<br>';
    echo '<div class="profile">';
    echo '<center class="changeprofile">'; 
    echo 'Phones:';
    echo '<br>';
    while($row4 = $query4->fetch_assoc()){
        echo $row4['user_phone'];
        echo '<br>';
    }
    echo '</center>';
    echo '</div>';
}

?>