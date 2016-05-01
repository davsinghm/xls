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

			<div class="row">
				<div class="col s7">
					<video id="video" width="90%" poster="thumbs/<?php echo $_GET['id']; ?>.jpg" style="padding-left: 3.5%; padding-top: 4%;">
						<source src="videos/<?php echo $_GET['id'].".".$jsonVariables->ext; ?>" type='video/<?php echo $jsonVariables->ext; ?>'>
						Your browser does not support the video tag.
					</video>
				</div>

				<div class="col s5" style="text-align: center; margin-top: 2.5%">
					<input type="text" id="subtitlesInputBox" name="subtitlesInputBox">

					<select name="activeLanguageInputBox" id="activeLanguageInputBox" style="display: block">
						<option value="EN" selected="selected">EN</option>
						<option value="HI">HI</option>
						<option value="PA">PA</option>
						<option value="TA">TA</option>
					</select>

					<a class="btn" onclick="saveSubtitles()">Save</a>
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

				selectedFragment = 0, activeFragment = -1;

				subtitles = {};
				subtitles["EN"] = [];
				subtitles["HI"] = [];
				subtitles["PA"] = [];
				subtitles["TA"] = [];

				activeLanguage = "EN";

				var ctx = document.getElementById("seekbar").getContext("2d");

				var seekbar = document.getElementById('seekbar');
				var seekbarWidth = seekbar.clientWidth;
				var seekbarHeight = seekbar.clientHeight;

				$("#seekbar").mousedown(function(event) {
					seekbarClicked = true;
					seekToPosition((event.pageX - $(this).offset().left)/seekbarWidth);

					if (activeFragment != -1){
						selectedFragment = activeFragment;
						$('#subtitlesInputBox').val(subtitles[activeLanguage][activeFragment]);
						console.log(selectedFragment);
					}
				}).mousemove(function(event) {
					var normalized = (event.pageX - $(this).offset().left)/seekbarWidth;

					if (!seekbarClicked) {
						activeFragment = -1;
						for (var i = 0; i < speechFragments.length; i++)
							if (speechFragments[i][0] <= normalized && speechFragments[i][1] > normalized)
								activeFragment = i;
						return;
					}

					seekToPosition(normalized);
				}).mouseup(function() {
					seekbarClicked = false;
				}).hover(function() {}, function() { activeFragment = -1; });

				$(document).keydown(function(event) {
					if (event.keyCode == 32) // space
						toggleVideoPlayback();
					else if (event.keyCode == 37) // left
						seekToPosition(seekerPosition - 0.01);
					else if (event.keyCode == 39) // right
						seekToPosition(seekerPosition + 0.01);
				});

				$('#video').click(function() {
					toggleVideoPlayback();
				});

				samples.push([0, 0]);
				for (var i = 0; i < 500; i++)
					samples.push([i/500, Math.random()]);
				samples.push([1, 0]);

				var speechFragmentsJson = "	<?php
												ob_start();
												require 'speechFragmentRetriever.php';
												echo ob_get_clean(); ?>";

				speechFragments = JSON.parse(speechFragmentsJson);

				for (var i = 0; i < speechFragments.length; i++) {
					subtitles["EN"].push("");
					subtitles["HI"].push("");
					subtitles["PA"].push("");
					subtitles["TA"].push("");
				}

				videoElement.addEventListener("timeupdate", videoProgressCallback);

				$("#activeLanguageInputBox").change(function() {
					activeLanguage = $('select[name="activeLanguageInputBox"]').val();
				});

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
				seekerPosition = videoElement.currentTime/videoElement.duration;

			    drawCanvas();
			}

			function drawCanvas()
			{
				var ctx = document.getElementById("seekbar").getContext("2d");

        		ctx.clearRect(0, 0, sbw, sbh);

				ctx.shadowColor   = '#999999';
				ctx.shadowOffsetX = 0;
				ctx.shadowOffsetY = 0;
				ctx.shadowBlur    = 8;

				ctx.fillStyle = "blue";

				var nextSpeechFragment = 0, insideSpeechFragment = false;
				var seekerDrwaing = false;

				for (var i = 0; i < samples.length - 1; i++)
				{
					if (insideSpeechFragment == false) {
						if (nextSpeechFragment < speechFragments.length)
							if (speechFragments[nextSpeechFragment][0] < samples[i][0]) {
								insideSpeechFragment = true;
								ctx.fillStyle = "red";
							}
					}
					else if (samples[i][0] >= speechFragments[nextSpeechFragment][1]) {
						nextSpeechFragment++;
						ctx.fillStyle = "blue";
						insideSpeechFragment = false;
					}

					seekerDrawing = false;
					if (samples[i][0] >= (seekerPosition - 0.0025) && samples[i][0] < (seekerPosition + 0.0025)) {
						ctx.fillStyle = "white";
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
							ctx.fillStyle = "red";
						else
							ctx.fillStyle = "blue";
					}
				}
			}

			function saveSubtitles()
			{
				subtitles[activeLanguage][selectedFragment] = $('#subtitlesInputBox').val();
				console.log(activeLanguage);
				console.log(subtitles[activeLanguage]);
			}
		</script>
	</body>
</html>