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

			<div class="container">
				<div class="col s12" style="text-align: center; margin-top: 2.5%">
					<div class="row">
						<video id="video" width="60%" poster="videos/<?php echo $_GET['id']; ?>thumbnail.png">
							<source src="videos/<?php echo $_GET['id'].".".$jsonVariables->ext; ?>" type='video/<?php echo $jsonVariables->ext; ?>'>
							Your browser does not support the video tag.
						</video>
					</div>

					<div class="row" style="margin-top: 2.5%;">
						<canvas id="seekbar" width="1" height="1"></canvas>
					</div>

				</div>
			</div>
		</div>

		<script>
			$(document).ready(function(){
				$('#navbarTitle').html('Cross Language Scripting&nbsp;&nbsp; | &nbsp;&nbsp;<?php echo $jsonVariables->title; ?>');

				w = window.innerWidth, h = window.innerHeight;
				sbw = w * 0.7;
				sbh = sbw / 20;
				$('#seekbar').attr('width', sbw);
				$('#seekbar').attr('height', sbh);

				speechFragments = [];
				samples = [];
				seekerPosition = -1;

				videoElement = document.getElementById('video');

				seekbarClicked = false;

				var ctx = document.getElementById("seekbar").getContext("2d");

				var seekbar = document.getElementById('seekbar');
				var seekbarWidth = seekbar.clientWidth;
				var seekbarHeight = seekbar.clientHeight;

				$("#seekbar").mousedown(function(event) {
					seekbarClicked = true;
					seekToPosition((event.pageX - $(this).offset().left)/seekbarWidth);
				}).mousemove(function(event) {
					if (!seekbarClicked)
						return;
					seekToPosition((event.pageX - $(this).offset().left)/seekbarWidth);
				}).mouseup(function() {
					seekbarClicked = false;
				});

				$(document).keydown(function(event) {
					if (event.keyCode == 32)
						toggleVideoPlayback();
				});

				$('#video').click(function() {
					toggleVideoPlayback();
				});

				samples.push([0, 0]);
				for (var i = 0; i < 500; i++)
					samples.push([i/500, Math.random()]);
				samples.push([1, 0]);

				console.log("ASDF");
				var speechFragmentsJson = "	<?php
												ob_start();
												require 'speechFragmentRetriever.php';
												echo ob_get_clean(); ?>";
				console.log(speechFragmentsJson);

				speechFragments = JSON.parse(speechFragmentsJson);

				videoElement.addEventListener("timeupdate", videoProgressCallback);

				drawCanvas();
			});

			function toggleVideoPlayback() {
				if (videoElement.paused)
					videoElement.play();
				else
					videoElement.pause();
			};

			function seekToPosition(position) {
					seekerPosition = position;

				    videoElement.currentTime = seekerPosition * videoElement.duration;

				    drawCanvas();
			}

			function videoProgressCallback() {
				console.log('zxc');
					seekerPosition = videoElement.currentTime/videoElement.duration;

				    drawCanvas();
			}

			function drawCanvas()
			{
				var ctx = document.getElementById("seekbar").getContext("2d");

				ctx.fillStyle = "#000";
				ctx.fillRect(0, 0, sbw, sbh);

				ctx.fillStyle = "blue";

				for (var i = 0; i < samples.length - 1; i++)
				{
					ctx.beginPath();

					ctx.moveTo(samples[i][0] * sbw, sbh);
					ctx.lineTo(samples[i][0] * sbw, sbh - samples[i][1] * sbh);
					ctx.lineTo(samples[i + 1][0] * sbw + 1, sbh - samples[i + 1][1] * sbh);
					ctx.lineTo(samples[i + 1][0] * sbw + 1, sbh);

					ctx.closePath();
					ctx.fill();
				}

				ctx.fillStyle = "rgba(255, 0, 0, 0.5)";
				for (var i = 0; i < speechFragments.length; i++)
					ctx.fillRect(speechFragments[i][0] * sbw, 0, (speechFragments[i][1] - speechFragments[i][0]) * sbw, sbh);

				ctx.fillStyle = "rgba(255, 255, 255, 0.5)";
				ctx.fillRect((seekerPosition - 0.0025) * sbw, 0, 0.005 * sbw, sbh);
			}
		</script>
	</body>
</html>