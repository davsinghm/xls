<?php

	function generateSamples($target_file) {
    	$myfile = fopen("wave.dat", "r") or die();
    	$dat = fopen("$target_file", "w") or die();

    	$num = 1;
		while(!feof($myfile)) {
			$line = fgets($myfile);
			if ($num > 2) {
				$line = trim($line);
				$line = explode("  ", $line);
				if (count($line) == 2)
					$a = trim($line[1]) - 0;
				else
					$a = 0;
				if ($a < 0)
					$a = 0;
				$a *= 10;			
				if ($a < 0)
					$a = 0;
				fwrite($dat, $a . " ");
				
			}
			$num++;
		}
		fclose($myfile);
		fclose($dat);

		return 0;
	}

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
		$samples_file = "samples/" . 88 . ".dat";
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

		if ($uploadOk != 0 && move_uploaded_file($_FILES["videoFile"]["tmp_name"], $target_file)) {
			
			exec("/usr/local/bin/ffmpeg -i ./$target_file -vf  \"thumbnail\" -frames:v 1 ./$thumb_file -y", $output, $return_var1);
			//shell_exec("/usr/local/bin/sox ./wave.wav  -c 1 -r 100 ./wave.dat");

			exec("/usr/local/bin/sox ./wave.wav -c 1 -r 10 ./wave.dat", $output, $return_var);
			if (!$return_var1 && !$return_var && !generateSamples($samples_file)) {

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
				$response["extraInfo"] = "Wave gen failed";
				$uploadOk = 0;
			}

	    } else {
	    	$response["returnCode"] = 0;
	    	$response["extraInfo"] =  "Sorry, there was an error uploading your file.";
	    	$uploadOk = 0;
	    }

	    if ($uploadOk === 0) {
	    	unlink($target_file);
	    	unlink($thumb_file);
	    	unlink("wave.dat");

	    }
		

	}  else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "FIELDS_MISSING";
	}

	echo json_encode($response);
?>