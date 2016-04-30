<?php
	function validateInput($con, $input)
	{
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		$input = mysqli_real_escape_string($con, $input);
		return $input;
	}

	class dbConnection {
		public $con;

		function __construct() {
			$this->connect();
		}

		function __destruct() {
			$this->close();
		}

		function connect() {
			require_once 'dbConfig.php';

			if (!$this->con = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE))
			{
				$response = [];

				$response["returnCode"] = 0;
				$response["extraInfo"] = "CONNECTION_FAILED";

				echo json_encode($response);
				die(mysqli_error());
			}

			foreach ($_POST as $key => $value)
				$_POST[$key] = validateInput($this->con, $_POST[$key]);
		}

		function close() {
			mysqli_close($this->con);
		}
	}
?>