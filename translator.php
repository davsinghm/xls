<?php
	session_start();

	$_POST['id'] = $_GET['id'];

	ob_start();
	require 'getVideoInfo.php';
	$jsonVariables = json_decode(ob_get_clean());

	//Check for failure..
	if ($jsonVariables->returnCode == 0)
	{
		header('Location: userHome.php');
		die();
	}
?>

<html>
    <head>
        <title>Cross Language Scripting</title>

		<link rel="stylesheet" href="css/materialize.icon.css"/>
		<link rel="stylesheet" href="css/materialize.min.css"/>
		<link rel="stylesheet" href="css/video-js.css"/>
		<link rel="stylesheet" href="css/style.css"/>

		<script src="js/jquery.js"></script>
		<script src="js/materialize.min.js"></script>
		<script src="js/video.js"></script>

		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>

    <body>
		<div id="flexContainer">

			<div class="navbar-fixed">
				<nav class="indigo">
					<div class="container">
						<div style="display: inline;" id="navbarTitle">Cross Language Scripting</div>

						<ul class="right" id="navbarLinks">
							<li>Welcome, <?php echo $_SESSION["username"]; ?></li>
							<li>&nbsp;&nbsp;&nbsp;|</li>
							<li><a href="#">About Us</a></li>
						</ul>
					</div>
				</nav>
			</div>

			<h3>
				<?php echo $jsonVariables->title; ?>
			</h3>

			<video id="my-video" class="video-js" controls preload="auto" width="640" height="264" poster="MY_VIDEO_POSTER.jpg" data-setup="{}">
				<source src="videos/<?php echo $_GET['id'].$jsonVariables->ext; ?>" type='video/mp4'>
				<p class="vjs-no-js">
				To view this video please enable JavaScript, and consider upgrading to a web browser that
				<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
				</p>
			</video>
		</div>

		<script>
			$(document).ready(function(){
				$('#loginLink').leanModal();
				$('#registerLink').leanModal();

				<?php
					if (isset($_POST['login']))
						echo 'Materialize.toast("Login Failed!", 4000)';
					else if (isset($_POST['register']))
					{
						if ($jsonVariables->returnCode == 1)
							echo 'Materialize.toast("Registration Successful!", 4000)';
						else
							echo 'Materialize.toast("Registration Failed!", 4000)';
					}
				?>
			});
		</script>
	</body>
</html>