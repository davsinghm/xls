<?php

	/*

	Input: username
	Input: password

	Output: returnCode
	Output: extraInfo <returnCode = 0>

	*/

	$response = [];

	if (isset($_POST["username"]) && isset($_POST["password"]))
	{
		require_once 'dbConnection.php';

		$dbCon = new dbConnection();
		$con = $dbCon->con;

		$result = mysqli_query($con, "SELECT * FROM users WHERE username='$_POST[username]' AND password='$_POST[password]'");

		if (!$result)
		{
			$response["returnCode"] = 0;
			$response["extraInfo"] = "QUERY_FAILED";
 		} else if (mysqli_num_rows($result) != 0) {
			$response["returnCode"] = 1;
		} else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "INVALID_UN_PW";
		}
	}
	else
	{
		$response["returnCode"] = 0;
		$response["extraInfo"] = "FIELDS_MISSING";
	}

	echo json_encode($response);
?>