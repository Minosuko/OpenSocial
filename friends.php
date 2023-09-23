<?php 
require 'functions/functions.php';
if (!isset($_COOKIE['token']))
    header("location:index.php");
if (!_is_session_valid($_COOKIE['token']))
    header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network</title>
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
    <style>
    .frame a{
        text-decoration: none;
        color: #4267b2;
    }
    .frame a:hover{
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <h1>Friends</h1>
        <?php
            echo '<center>'; 
            $sql = "SELECT users.user_id, users.user_firstname, users.user_lastname, users.user_gender, users.pfp_media_id
                    FROM users
                    JOIN (
                        SELECT friendship.user1_id AS user_id
                        FROM friendship
                        WHERE friendship.user2_id = {$data['user_id']} AND friendship.friendship_status = 1
                        UNION
                        SELECT friendship.user2_id AS user_id
                        FROM friendship
                        WHERE friendship.user1_id = {$data['user_id']} AND friendship.friendship_status = 1
                    ) userfriends
                    ON userfriends.user_id = users.user_id";
            $query = $conn->query($sql);
            $width = '168px';
            $height = '168px';
            if($query){
                if($query->num_rows == 0){
                    echo '<div class="post">';
                    echo 'You don\'t yet have any friends.';
                    echo '</div>';
                } else {
                    while($row = $query->fetch_assoc()){
						echo '<div class="frame">';
						echo '<center>';
						include 'includes/profile_picture.php';
						echo '<br>';
						echo '<a href="#" onclick="changeUrl(\'profile.php?id=' . $row['user_id'] . '\');">' . $row['user_firstname'] . ' ' . $row['user_lastname'] . '</a>';
						echo '</center>';
						echo '</div>';
                    }
                }
            }
            echo '</center>';
        ?>
    </div>
</body>
</html>