<?php

	/*

	Input: ...

	Output: ...

	*/

	$response = [];

	foreach ($_POST as $key => $value)
		$response[$key] = $value;

		echo json_encode($response);
?>