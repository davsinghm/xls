<?php
	session_start();

	if (isset($_POST['title']))
	{
		ob_start();
		require 'upload_process.php';
		$jsonVariables = json_decode(ob_get_clean());

		//Check for failure..
		if ($jsonVariables->returnCode == 0)
		{
			$_SESSION["username"] = $_POST["username"];
			header('Location: home.php');
			die();
		}
	}

?>

<html>
    
    <?php include_once "header.php" ?>

	<div style="padding-left: 15%">
		<form method="post" enctype="multipart/form-data" style="margin-top: 2%">

			<div>
			<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label" >
		    <input class="mdl-textfield__input" type="text" id="title">
		    <label class="mdl-textfield__label" for="title">Title</label>
		  	</div>
		  </div>
			Language: <select name="orig_lang" style="margin-top: 0.5%">
				<option value="en" selected>English</option>
				<option value="pa">Punjabi</option>
				<option value="hi">Hindi</option>
				s<option value="ta">Tamil</option>
			</select>

		<br>	
		<input class="md-raised md-primary" type="file"  name="videoFile" id="videoFile"  style="margin-top: 1.5%"/><br/>
			<button class ="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit"  style="margin-top: 2.5%">Upload</button>
		</form>
	</div>

    <?php include_once "footer.php" ?>
