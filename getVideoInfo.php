<?php

	/*

	Input: id

	Output: returnCode
	Output: extraInfo <returnCode = 0>
	Output: ext <returnCode = 1>
	Output: title <returnCode = 1>

	*/

	$response = [];

	if (isset($_POST["id"])) {

		require_once 'dbConnection.php';

		$dbCon = new dbConnection();
		$con = $dbCon->con;

		$result = mysqli_query($con, "SELECT * FROM videos WHERE Id='$_POST[id]'");

		if (!$result)
		{
			$response["returnCode"] = 0;
			$response["extraInfo"] = "QUERY_FAILED";
 		} else if (mysqli_num_rows($result) != 0) {
			$response["returnCode"] = 1;

			while($row = mysqli_fetch_assoc($result)) {
				$response["ext"] = $row["Ext"];
				$response["title"] = $row["Title"];
			}
		}
	} else {
			$response["returnCode"] = 0;
			$response["extraInfo"] = "FIELDS_MISSING";
	}

	echo json_encode($response);
?>