<?php

	/*

	Input: username
	Input: email
	Input: password

	Output: returnCode
	Output: extraInfo <returnCode = 0>

	*/

	$response = [];

	if (isset($_POST["username"]) && isset($_POST["password"])) {

		require_once 'dbConnection.php';

		$dbCon = new dbConnection();
		$con = $dbCon->con;

		$result = mysqli_query($con, "INSERT INTO users(Username, Password) VALUES('$_POST[username]', '$_POST[password]')");

		if (!$result)
		{
			$response["returnCode"] = 0;
			$response["extraInfo"] = "QUERY_FAILED";
		} else {
			$response["returnCode"] = 1;
		}
	} else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "FIELDS_MISSING";
	}

	echo json_encode($response);
?>