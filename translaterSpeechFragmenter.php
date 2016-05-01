<?php
	session_start();

	if (isset($_POST['fragments']))
	{
		$file = fopen("speechFragments/".$_GET['id'].".dat", "w") or die();
		fwrite($file, $_POST['fragments']);
		fclose($file);

		header('Location: translater.php?id='.$_GET['id']);
		die();
	}

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

			<div class="row">
				<div class="col s7 push-s3">
					<video id="video" width="90%" poster="thumbs/<?php echo $_GET['id']; ?>.jpg" style="padding-top: 3.5%;">
						<source src="videos/<?php echo $_GET['id'].".".$jsonVariables->ext; ?>" type='video/<?php echo $jsonVariables->ext; ?>'>
						Your browser does not support the video tag.
					</video>
				</div>

				<div class="col s2 push-s3" style="margin-top: 36%">
					<a onclick="sendData()" style="color: black" class="btn">Save Fragments</a>
				</div>
			</div>
		</div>

		<canvas id="seekbar" width="1" height="1" style="position: fixed; bottom: 0px;"></canvas>

		<script>
			$(document).ready(function(){
				$('#navbarTitle').html('Cross Language Scripting&nbsp;&nbsp; | &nbsp;&nbsp;<?php echo $jsonVariables->title; ?>');

				w = window.innerWidth, h = window.innerHeight;
				sbw = w;
				sbh = sbw / 20;
				$('#seekbar').attr('width', sbw);
				$('#seekbar').attr('height', sbh);

				speechFragments = [];
				samples = [];
				seekerPosition = -1;

				videoElement = document.getElementById('video');

				seekbarClicked = false;

				newFragmentStart = -1, newFragmentEnd = -1;

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
					if (event.keyCode == 32) // space
						toggleVideoPlayback();
					else if (event.keyCode == 37) // left
						seekToPosition(seekerPosition - 0.01);
					else if (event.keyCode == 39) // right
						seekToPosition(seekerPosition + 0.01);
					else if (event.keyCode == 219) { // [
						if (newFragmentStart == -1)
							newFragmentStart = videoProgressN();
						else {
							newFragmentStart = newFragmentEnd = -1;
							drawCanvas();
						}
					}
					else if (event.keyCode == 221) { // ]
						if (newFragmentStart != -1 && newFragmentEnd > newFragmentStart)
							speechFragments.push([newFragmentStart, newFragmentEnd]);

						newFragmentStart = newFragmentEnd = -1;
						drawCanvas();
					}
				});

				$('#video').click(function() {
					toggleVideoPlayback();
				});

				samples.push([0, 0]);
				for (var i = 0; i < 500; i++)
					samples.push([i/500, Math.random()]);
				samples.push([1, 0]);

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

				    if (seekerPosition > newFragmentStart)
				    	newFragmentEnd = seekerPosition;

				    drawCanvas();
			}

			function videoProgressCallback() {
					seekerPosition = videoProgressN();

				    if (seekerPosition > newFragmentStart)
				    	newFragmentEnd = seekerPosition;

				    drawCanvas();
			}

			function videoProgressN() {
				return videoElement.currentTime/videoElement.duration;
			}

			function drawCanvas()
			{
				var ctx = document.getElementById("seekbar").getContext("2d");

				ctx.clearRect(0, 0, sbw, sbh);

				ctx.fillStyle = "#2196F3";

				var nextSpeechFragment = 0, insideSpeechFragment = false;
				var seekerDrwaing = false;

				for (var i = 0; i < samples.length - 1; i++)
				{
					ctx.beginPath();
					if (insideSpeechFragment == false) {
						if (nextSpeechFragment < speechFragments.length)
							if (speechFragments[nextSpeechFragment][0] < samples[i][0]) {
								insideSpeechFragment = true;

								ctx.fillStyle = "#EF6C00";
							}
					}
					else if (samples[i][0] >= speechFragments[nextSpeechFragment][1]) {
						nextSpeechFragment++;
						ctx.fillStyle = "#2196F3";
						insideSpeechFragment = false;
					}

					seekerDrawing = false;
					if (newFragmentStart != -1 && newFragmentEnd > newFragmentStart && samples[i][0] >= (newFragmentStart) && samples[i][0] < (newFragmentEnd)) {
						ctx.fillStyle = "red";
						seekerDrawing = true;
					}

					if (samples[i][0] >= (seekerPosition - 0.0025) && samples[i][0] < (seekerPosition + 0.0025)) {
						ctx.fillStyle = "green";
						seekerDrawing = true;
					}

					ctx.beginPath();

					ctx.moveTo(samples[i][0] * sbw, sbh);
					ctx.lineTo(samples[i][0] * sbw, sbh - samples[i][1] * sbh);
					ctx.lineTo(samples[i + 1][0] * sbw + 1, sbh - samples[i + 1][1] * sbh);
					ctx.lineTo(samples[i + 1][0] * sbw + 1, sbh);

					ctx.closePath();
					ctx.fill();

					if (seekerDrawing) {
						if (insideSpeechFragment)
							ctx.fillStyle = "#EF6C00";
						else
							ctx.fillStyle = "#2196F3";
					}
				}
			}

			function sendData()
			{
				var form = document.createElement("form");
			    form.setAttribute("method", "POST");
			    form.setAttribute("action", "translaterSpeechFragmenter.php?id=<?php echo $_GET['id']; ?>");

	            var hiddenField = document.createElement("input");
	            hiddenField.setAttribute("type", "hidden");
	            hiddenField.setAttribute("name", "fragments");

				speechFragments.sort(function(a, b) { return a[0] > b[0]; });
	            hiddenField.setAttribute("value", JSON.stringify(speechFragments));

	            form.appendChild(hiddenField);
			    document.body.appendChild(form);
			    form.submit();
			}
		</script>
	</body>
</html>