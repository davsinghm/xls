<?php
	session_start();

	if (isset($_POST['login']))
	{
		ob_start();
		require 'login.php';
		$jsonVariables = json_decode(ob_get_clean());

		//Check for failure..
		if ($jsonVariables->returnCode == 1)
		{
			$_SESSION["username"] = $_POST["username"];
			header('Location: videos.php');
			die();
		}
	}
	else if (isset($_POST['register']))
	{
		ob_start();
		require 'register.php';
		$jsonVariables = json_decode(ob_get_clean());
	}

?>

<html>
    <head>
        <title>Cross Language Scripting</title>

		<link rel="stylesheet" href="css/materialize.icon.css"/>
		<link rel="stylesheet" href="css/materialize.min.css"/>
		<link rel="stylesheet" href="css/style.css"/>
		<link rel="stylesheet" href="css/home.css"/>

		<script src="js/jquery.js"></script>
		<script src="js/materialize.min.js"></script>

		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>

    <body>
		<div id="flexContainer">

			<div class="navbar-fixed">
				<nav class="indigo">
					<div class="container">
						<div style="display: inline;" id="navbarTitle">Cross Language Scripting</div>

						<ul class="right" id="navbarLinks">
							<li><a href="#">About Us</a></li>
						</ul>
					</div>
				</nav>
			</div>

			<div class="container" style="color: white;">
				<div class="row">
					<div class="col s4 push-s4" style="padding-top: 15%;">
						<h3 style="text-align: center;">Translate, Transcribe and Transform the World!</h3>
						<h5>Millions of translators and teachers use Translators' Haven to educate the world, without the barriers of language.</h5>
					</div>
					<div class="col s3 push-s4" style="padding-top: 24%; text-align: center;">
						<div class="row">
							<a href="#loginModal" id="loginLink" class="waves-effect waves-light btn indigo">Login</a>
						</div style="margin-top: 2%;">
							<div class="row">
						<a href="#registerModal" id="registerLink" class="waves-effect waves-light btn indigo">Register</a>
						</div>
					</div>
				</div>
			</div>

			<div id="loginModal" class="modal container" style="width: 25%">
				<div class="modal-content">
					<form method="post" class="col s4 pull-s2">
						<div class="row">
							<div class="input-field col s12">
								<input name="username" id="username" type="text" class="validate" required="">
								<label for="username">Username</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input name="password" id="password" type="password" class="validate" required="">
								<label for="password">Password</label>
							</div>
						</div>
						<input type="hidden" name="login">
						<div>
							<button class="btn waves-effect transparentButton" type="submit">
								Login <i class="material-icons right">send</i>
							</button>
						</div>
					</form>
				</div>
			</div>

			<div id="registerModal" class="modal container" style="width: 25%">
				<div class="modal-content">
					<form method="post" class="col s4 pull-s2">
						<div class="row">
							<div class="input-field col s12">
								<input name="username" id="username" type="text" class="validate" required="">
								<label for="username">Username</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input name="password" id="password" type="password" class="validate" required="">
								<label for="password">Password</label>
							</div>
						</div>
						<input type="hidden" name="register">
						<div>
							<button class="btn waves-effect transparentButton" type="submit">
								Register <i class="material-icons right">send</i>
							</button>
						</div>
					</form>
				</div>
			</div>
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