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

					<div id="extraInfo" style="position: absolute;">

					</div>
				</div>
			</div>
		</div>

		<img src="images/seeker.png" id="seeker" style="position: absolute;">

		<script>
			$(document).ready(function(){
				$('#navbarTitle').html('Cross Language Scripting&nbsp;&nbsp; | &nbsp;&nbsp;<?php echo $jsonVariables->title; ?>');

				var w = window.innerWidth, h = window.innerHeight;
				sbw = w * 0.7;
				sbh = sbw / 20;
				$('#seekbar').attr('width', sbw);
				$('#seekbar').attr('height', sbh);

				var ctx = document.getElementById("seekbar").getContext("2d");

				drawCanvas();

				var seekbar = document.getElementById('seekbar');
				var seekbarWidth = seekbar.clientWidth;
				var seekbarHeight = seekbar.clientHeight;

				$('#extraInfo').text(seekbarWidth + " " + seekbarHeight);

				$('#seeker').css("height", seekbarHeight);
				$('#seeker').css("top", $("#seekbar").offset().top + 'px');

				$('#seeker').css("top", '-100px');

				$("#seekbar").mousemove(function(event)
				{
					$('#seeker').css("top", $(this).offset().top + 'px');
					$('#seeker').css("left", event.pageX + 'px');

				    var progress = (event.pageX - $(this).offset().left)/seekbarWidth;

				    var videoElement = document.getElementById('video');

				    videoElement.currentTime = progress * videoElement.duration;

				    $('#extraInfo').text(progress);
				});
			});

			function drawCanvas()
			{
				var ctx = document.getElementById("seekbar").getContext("2d");

				ctx.fillStyle = "white";
				ctx.fillRect(0, 0, sbw, sbh);

				ctx.fillStyle = "blue";
				var points = [];
				for (var i = 0; i < 100; i++)
					points.push([i/100, Math.random()]);

				ctx.beginPath();
				ctx.moveTo (0, sbh);

				for (var i = 0; i < points.length - 1; i++)
				{
					ctx.lineTo(points[i][0] * sbw, points[i][1] * sbh);
					ctx.lineTo(points[i + 1][0] * sbw, points[i + 1][1] * sbh);
				}

				ctx.moveTo (sbw, sbh);
				ctx.closePath();

				ctx.stroke();
			}
		</script>
	</body>
</html>