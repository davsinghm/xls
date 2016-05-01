<?php
	session_start();

	if (isset($_POST['subtitles']))
	{
		$file = fopen("subtitles/".$_GET['id'].".dat", "w") or die();
		fwrite($file, $_POST['subtitles']);
		fclose($file);
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

    	<script src="js/jsapi.js"></script>
    	<script>
    		google.load("elements", "1", {
				packages: "transliteration"
			});
    	</script>
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
					<video id="video" width="90%" poster="thumbs/<?php echo $_GET['id']; ?>.jpg" style="padding-left: 3.5%; padding-top: 3.5%;">
						<source src="videos/<?php echo $_GET['id'].".".$jsonVariables->ext; ?>" type='video/<?php echo $jsonVariables->ext; ?>'>
						Your browser does not support the video tag.
					</video>
				</div>

				<div class="col s4" style="margin-top: 4%; margin-left: 1.5%">
					<div class="row">
						<div class="input-field col s10">
							<input type="text" id="subtitlesInputBox" name="subtitlesInputBox">
							<label for="subtitlesInputBox">Translation</label>
						</div>
						<form method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col s2" style="margin-top: 2.5%">
									<select name="activeLanguageInputBox" id="activeLanguageInputBox" style="display: block">
										<option value="EN" selected="selected">English</option>
										<option value="HI" id="HI"></option>
										<option value="PA" id="PA"></option>
										<option value="TA" id="TA"></option>
									</select>
								</div>
							</div>

							<div class="row" style="margin-top: 20%; text-align: center;">
								Audio Track: &nbsp;&nbsp;&nbsp;<input type="file"  name="audioFile" id="audioFile" /><br>
								<p id="audioTrackInfo" style="margin-top: 4%; text-align: center;"></p>
								<a class="btn" style="margin-top: 4%" onclick="$(this).closest('form').attr('action', 'upload_process_audio.php?id=<?php echo $_GET["id"]; ?>'); $(this).closest('form').submit()">Upload</a>
							</div>
						</form>
					</div>


					<div class="row" style="margin-top: 30%; text-align: center;">
						<a class="btn" onclick="sendData()">Save</a>
						<a class="btn" href="#downloadModal" id="downloadModalLink">Download</a>
					</div>
				</div>
			</div>
		</div>

		<canvas id="seekbar" width="1" height="1" style="position: fixed; bottom: 0px;"></canvas>

		<div id="downloadModal" class="modal container" style="width: 25%">
			<div class="modal-content">
				<form method="post" class="col s4 pull-s2" action="download.php">
					<div class="row">
						<div class="input-field col s12" style="text-align: left;">
							Subtitle Language:
							<select name="subtitleLanguage" id="subtitleLanguage" style="display: block">
								<option value="EN" selected="selected">EN</option>
								<option value="HI" id="HI">HI</option>
								<option value="PA" id="PA">PA</option>
								<option value="TA" id="TA">TA</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="input-field col s12" style="text-align: left;">
							Audio Language:
							<select name="audioLanguage" id="audioLanguage" style="display: block">
								<option value="EN" selected="selected">EN</option>
								<option value="HI" id="HI">HI</option>
								<option value="PA" id="PA">PA</option>
								<option value="TA" id="TA">TA</option>
							</select>
						</div>
					</div>
					<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
					<div>
						<button class="btn waves-effect transparentButton" type="submit">
							Download <i class="material-icons right">send</i>
						</button>
					</div>
				</form>
			</div>
		</div>

		<script>
			$(document).ready(function(){
				$('#navbarTitle').html('Cross Language Scripting&nbsp;&nbsp; | &nbsp;&nbsp;<?php echo $jsonVariables->title; ?>');

				$('#downloadModalLink').leanModal();

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

				selectedFragment = 0;

				subtitles = {};

				activeLanguage = "EN";

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

					var normalized = (event.pageX - $(this).offset().left)/seekbarWidth;
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
												echo ob_get_clean();
											?>";
				speechFragments = JSON.parse(speechFragmentsJson);

				<?php
					if (!file_exists("subtitles/".$_GET['id'].".dat")) {
						echo '	subtitles["EN"] = [];
								subtitles["HI"] = [];
								subtitles["PA"] = [];
								subtitles["TA"] = [];

								for (var i = 0; i < speechFragments.length; i++) {
									subtitles["EN"].push("");
									subtitles["HI"].push("");
									subtitles["PA"].push("");
									subtitles["TA"].push("");
								}';
					} else {
						echo ' var subtitlesJson = \'';

						ob_start();
						require 'subtitlesRetriever.php';
						echo ob_get_clean();

						echo '\';
						subtitles = JSON.parse(subtitlesJson);';
					}
				?>

				$('#subtitlesInputBox').val(subtitles[activeLanguage][selectedFragment]);

				videoElement.addEventListener("timeupdate", videoProgressCallback);

				$("#activeLanguageInputBox").change(function() {
					saveSubtitles();
					activeLanguage = $('select[name="activeLanguageInputBox"]').val();
					$('#subtitlesInputBox').val(subtitles[activeLanguage][selectedFragment]);

					$('#audioTrackInfo').text("(" + activeLanguage + " audio track " + (audioTracks[activeLanguage]? "already exists.)": "does not exist.)"))

					setTransliteralKeyboard();
				});

				$('#HI').text(unescape(JSON.parse('"%u0939%u093F%u0902%u0926%u0940"')));
				$('#PA').text(unescape(JSON.parse('"%u0A2A%u0A70%u0A1C%u0A3E%u0A2C%u0A40"')));
				$('#TA').text(unescape(JSON.parse('"%u0BA4%u0BAE%u0BBF%u0BB4%u0BCD"')));

				$('#subtitlesInputBox').blur(saveSubtitles);

				var audioTracks = {};
				<?php
					echo 'audioTracks["EN"] = '.(file_exists('audioTracks/'.$_GET['id'].'_EN.m4a')? "true": "false").';';
					echo 'audioTracks["HI"] = '.(file_exists('audioTracks/'.$_GET['id'].'_HI.m4a')? "true": "false").';';
					echo 'audioTracks["PA"] = '.(file_exists('audioTracks/'.$_GET['id'].'_PA.m4a')? "true": "false").';';
					echo 'audioTracks["TA"] = '.(file_exists('audioTracks/'.$_GET['id'].'_TA.m4a')? "true": "false").';';
				?>

				$('#audioTrackInfo').text("(" + activeLanguage + " audio track " + (audioTracks[activeLanguage]? "already exists.)": "does not exist.)"))

				drawCanvas();
			});

			function setTransliteralKeyboard() {
				var transliteralEnabled = (activeLanguage == "EN"? false: true);
				var destLanguage = (activeLanguage == "EN"? google.elements.transliteration.LanguageCode.HINDI: activeLanguage == "HI"? google.elements.transliteration.LanguageCode.HINDI: activeLanguage == "PA"? google.elements.transliteration.LanguageCode.PUNJABI: google.elements.transliteration.LanguageCode.TAMIL);

				var options = {
					sourceLanguage:
					google.elements.transliteration.LanguageCode.ENGLISH,
					destinationLanguage:
					[destLanguage],
					transliterationEnabled: transliteralEnabled,
				};

				var control = new google.elements.transliteration.TransliterationControl(options);

				control.makeTransliteratable(['subtitlesInputBox']);
			}

			function toggleVideoPlayback() {
				if (videoElement.paused)
					videoElement.play();
				else
					videoElement.pause();
			};

			function seekToPosition(position) {
					seekerPosition = position;

					var oldSelectedFragment = selectedFragment;

					for (var i = 0; i < speechFragments.length; i++)
						if (speechFragments[i][0] <= position && speechFragments[i][1] > position)
							selectedFragment = i;

					if (oldSelectedFragment != selectedFragment) {
						subtitles[activeLanguage][oldSelectedFragment] = $('#subtitlesInputBox').val();
						$('#subtitlesInputBox').val(subtitles[activeLanguage][selectedFragment]);
					}

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

				ctx.fillStyle = "#2196F3";
				//ctx.shadowColor   = '#999999';
				ctx.shadowOffsetX = 0;
				ctx.shadowOffsetY = 0;
				ctx.shadowBlur    = 8;

				ctx.fillStyle = '#EF6C00';

				var nextSpeechFragment = 0, insideSpeechFragment = false;
				var seekerDrwaing = false;

				for (var i = 0; i < samples.length - 1; i++)
				{
					if (insideSpeechFragment == false) {
						if (nextSpeechFragment < speechFragments.length)
							if (speechFragments[nextSpeechFragment][0] < samples[i][0]) {
								insideSpeechFragment = true;

								if (nextSpeechFragment == selectedFragment)
									ctx.fillStyle = "red";
								else
									ctx.fillStyle = "#EF6C00";
							}
					}
					else if (samples[i][0] >= speechFragments[nextSpeechFragment][1]) {
						nextSpeechFragment++;
						ctx.fillStyle = "#2196F3";
						ctx.fillStyle = '#2196F3';
						insideSpeechFragment = false;
					}

					seekerDrawing = false;
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
						if (insideSpeechFragment) {
							if (nextSpeechFragment == selectedFragment)
								ctx.fillStyle = "red";
							else
								ctx.fillStyle = "#EF6C00";
						}
						else
							ctx.fillStyle = "#2196F3";
					}
				}
			}

			function saveSubtitles()
			{
				subtitles[activeLanguage][selectedFragment] = $('#subtitlesInputBox').val();
			}

			function sendData()
			{
				var form = document.createElement("form");
			    form.setAttribute("method", "POST");
			    form.setAttribute("action", "translater.php?id=<?php echo $_GET['id']; ?>");
			    form.setAttribute("accept-charset", "UTF-8");

	            var hiddenField = document.createElement("input");
	            hiddenField.setAttribute("type", "hidden");
	            hiddenField.setAttribute("name", "subtitles");

	            hiddenField.setAttribute("value", JSON.stringify(subtitles));

	            form.appendChild(hiddenField);
			    document.body.appendChild(form);
			    form.submit();
			}
		</script>
	</body>
</html>