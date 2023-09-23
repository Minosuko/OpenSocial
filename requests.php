<?php 
if (!isset($_COOKIE['token']))
    header("location:index.php");
require_once 'functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
    header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network</title>
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <h1>Friend Requests</h1>
        <?php
        // Responding to Request
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (isset($_GET['accept'])) {
                $sql = "UPDATE friendship
                        SET friendship.friendship_status = 1
                        WHERE friendship.user1_id = {$_GET['id']} AND friendship.user2_id = {$data['user_id']}";
                $query = $conn->query($sql);
                if($query){
                    echo '<div class="userquery">';
                    echo 'You have accepted ' . $_GET['name'];
                    echo '<br><br>';
                    echo 'Redirecting in 5 seconds';
                    echo '<br><br>';
                    echo '</div>';
                    echo '<br>';
                    header("refresh:5; url=requests.php" );
                }
            } else if(isset($_GET['ignore'])) {
                $sql6 = "DELETE FROM friendship
                        WHERE friendship.user1_id = {$_GET['id']} AND friendship.user2_id = {$data['user_id']}";
                $query6 = $conn->query($sql6);
                if($query){
                    echo '<div class="userquery">';
                    echo 'You have Ignored ' . $_GET['name'];
                    echo '<br><br>';
                    echo 'Redirecting in 5 seconds';
                    echo '<br><br>';
                    echo '</div>';
                    echo '<br>';
                    header("refresh:5; url=requests.php" );
                }
            }
        }
        //
        ?>
        <?php
        $sql = "SELECT users.user_gender, users.user_id, users.user_firstname, users.user_lastname, users.pfp_media_id
                FROM users
                JOIN friendship
                ON friendship.user2_id = {$data['user_id']} AND friendship.friendship_status = 0 AND friendship.user1_id = users.user_id";
        $query = $conn->query($sql);
        $width = '168px';
        $height = '168px';
        if($query){
            if($query->num_rows == 0){
                echo '<div class="userquery">';
                echo 'You have no pending friend requests.';
                echo '<br><br>';
                echo '</div>';
            }
            while($row = $query->fetch_assoc()){
                echo '<div class="userquery">';
                include 'includes/profile_picture.php';
                echo '<br>';
                echo '<a class="profilelink" href="profile.php?id=' . $row['user_id'] .'">' . $row['user_firstname'] . ' ' . $row['user_lastname'] . '<a>';
                echo '<form method="get" action="requests.php">';
                echo '<input type="hidden" name="id" value="' . $row['user_id'] . '">';
                echo '<input type="hidden" name="name" value="' . $row['user_firstname'] . '">';
                echo '<input type="submit" value="Accept" name="accept">';
                echo '<br><br>';
                echo '<input type="submit" value="Ignore" name="ignore">';
                echo '<br><br>';
                echo'</form>';
                echo '</div>';
                echo '<br>';
            }
        }
        ?>
    </div>
</body>
</html>