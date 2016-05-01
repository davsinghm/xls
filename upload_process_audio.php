<?php
	$response = [];

var_dump($_GET);
var_dump($_POST);
var_dump($_FILES);
	if (isset($_FILES["audioFile"]) && isset($_POST["activeLanguageInputBox"]) && isset($_GET["id"])) {

		$ext = pathinfo(basename($_FILES["audioFile"]["name"]), PATHINFO_EXTENSION);
		$target_file = "audioTracks/" . $_GET['id'] . "_" . $_POST["activeLanguageInputBox"] . "." . $ext;
		$uploadOk = 1;

		// Check if file already exists
		if (file_exists($target_file)) {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "Sorry, file already exists.";
		    $uploadOk = 0;
		} else if($ext != "m4a" ) {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "Sorry, only m4a files are allowed.";
		    $uploadOk = 0;
		}

		if ($uploadOk != 0 && move_uploaded_file($_FILES["audioFile"]["tmp_name"], $target_file)) {
				$response["returnCode"] = 1;
	    } else if ($uploadOk != 0) {
	    	$response["returnCode"] = 0;
	    	$response["extraInfo"] =  "Sorry, there was an error uploading your file.";
	    	$uploadOk = 0;
	    }
	}  else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "FIELDS_MISSING";
	}

	header('Location: translaterRedirector.php?id='.$_GET['id']);
?>