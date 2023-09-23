<?php 
// Check whether user is logged on or not
if (!isset($_COOKIE['token']))
    header("location:index.php");
require_once 'functions/functions.php';
if (!_is_session_valid($_COOKIE['token']))
    header("location:index.php");
$data = _get_data_from_token($_COOKIE['token']);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caption = $_POST['caption'];
    if(isset($_POST['private']))
        $public = "N";
    else
        $public = "Y";
    $poster = $data['user_id'];
    $sql = sprintf(
		"INSERT INTO posts (post_caption, post_public, post_time, post_by) VALUES ('%s', '$public', NOW(), $poster)",
		$conn->real_escape_string($caption)
	);
    $query = $conn->query($sql);
    if($query){
        // Upload Post Image If a file was choosen
        if (!empty($_FILES['fileUpload']['name'])) {
            $last_id = $conn->insert_id;
            include 'functions/upload.php';
        }
        header("location: home.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Social Network</title>
    <link rel="stylesheet" type="text/css" href="resources/css/main.css">
	<link rel="stylesheet" type="text/css" href="resources/css/font-awesome/all.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/navbar.php'; ?>
        <br>
		<input type="hidden" id="page" value="0">
        <div class="createpost">
            <div>
                <h2>Make Post</h2>
                <hr>
                <span style="float:right; color:black">
                <input type="checkbox" id="private" name="private">
                <label for="private">Private</label>
                </span>
                Caption <span class="required" style="display:none;"> *You can't Leave the Caption Empty.</span><br>
                <textarea rows="6" name="caption"></textarea>
                <center><img src="" id="preview" style="max-width:580px; display:none;"></center>
                <div class="createpostbuttons">
                    <!--<form action="" method="post" enctype="multipart/form-data" id="imageform">-->
                    <label>
                        <img src="images/photo.png">
                        <input type="file" name="fileUpload" id="imagefile">
                        <!--<input type="submit" style="display:none;">-->
                    </label>
                    <input type="button" value="Post" name="post" onclick="return validatePost()">
                    <!--</form>-->
                </div>
            </div>
        </div>
		<h1>News Feed</h1>
		<div id="feed">
			<h1>Loading...</h1>
		</div>
        <br><br><br>
    </div>
    <script>
		fetch_post();
        // Invoke preview when an image file is choosen.
        $(document).ready(function(){
            $('#imagefile').change(function(){
                preview(this);
            });
        });
        // Preview function
        function preview(input){
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (event){
                    $('#preview').attr('src', event.target.result);
                    $('#preview').css('display', 'initial');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
		function _post_feed(){
			var file_data = document.getElementById("imagefile");
			var is_private = "N";
			if(document.getElementById('private').checked)
				is_private = "Y";
			else
				is_private = "N";
			var form_data = new FormData();
			form_data.append("post", 'post');
			form_data.append("private", is_private);
			form_data.append("caption", document.getElementsByTagName("textarea")[0].value);
			if(form_data.files.length > 0)
				form_data.append("fileUpload", file_data.files[0]);
			$.ajax({
				type: "POST",
				url: "/worker/post.php",
				processData: false,
				mimeType: "multipart/form-data",
				contentType: false,
				data: form_data,
				success: function (response) {
					fetch_post();
				}
			});
		}
        // Form Validation
        function validatePost(){
            var required = document.getElementsByClassName("required");
            var caption = document.getElementsByTagName("textarea")[0].value;
            required[0].style.display = "none";
            if(caption == ""){
                required[0].style.display = "initial";
                return false;
            }
			_post_feed();
			$("#imagefile").prop("value") = null;
			caption.value = '';
			$('#preview').css('display', 'none');
            return false;
        }
    </script>
</body>
</html>