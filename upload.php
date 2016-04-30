<?php

	$response = [];

	if (isset($_FILES["videoFile"]) && isset($_POST["title"]) && isset($_POST["orig_lang"])) {

		require_once 'dbConnection.php';

		$dbCon = new dbConnection();
		$con = $dbCon->con;

		$result = mysqli_query($con, "SELECT MAX(Id) AS maxID FROM videos");

		if (mysqli_num_rows($result) == 0)
			$id = 1;
		else
			while($row = mysqli_fetch_assoc($result))
				$id = $row["maxID"] + 1;

		$ext = pathinfo(basename($_FILES["videoFile"]["name"]), PATHINFO_EXTENSION);
		$target_file = "videos/" . $id . "." . $ext;
		$thumb_file = "thumbs/" . $id . ".jpg";
		$uploadOk = 1;
		
		// Check if file already exists
		if (file_exists($target_file)) {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "Sorry, file already exists.";
		    $uploadOk = 0;
		} elseif($ext != "mp4" && $ext != "webm" && $ext != "m4v" ) {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "Sorry, only videos are allowed.";
		    $uploadOk = 0;
		}

		$pwd = trim(shell_exec("pwd"));
		if ($uploadOk != 0 && move_uploaded_file($_FILES["videoFile"]["tmp_name"], $target_file)) {
			
			shell_exec("$pwd/ffmpeg -i $pwd/$target_file -vf  \"thumbnail\" -frames:v 1 $pwd/$thumb_file -y");

			$result = mysqli_query($con, "INSERT INTO videos(Title, Ext, OrigLang, Status, Languages) 
				VALUES('$_POST[title]', '$ext', '$_POST[orig_lang]', '0', '')");

			if (!$result) {
				$response["returnCode"] = 0;
				$response["extraInfo"] = "QUERY_FAILED";
				$uploadOk = 0;
			} else {
				$response["returnCode"] = 1;
			}

	    } else {
	    	$response["returnCode"] = 0;
	    	$response["extraInfo"] =  "Sorry, there was an error uploading your file.";
	    	$uploadOk = 0;
	    }

	    if ($uploadOk === 0) {
	    	unlink($target_file);
	    	unlink($thumb_file);
	    }
		

	}  else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "FIELDS_MISSING";
	}

	echo json_encode($response);
?>