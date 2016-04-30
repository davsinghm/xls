<?php
	require_once 'dbConnection.php';

	$dbCon = new dbConnection();
	$con = $dbCon->con;

	$result = mysqli_query($con, "SELECT * FROM videos");

	if ($result && mysqli_num_rows($result) != 0) {
		$num = 0;
		while($row = mysqli_fetch_assoc($result)) {
			echo "<img width=200 src=\"thumbs/" . $row["Id"] . ".jpg\" />"; echo "<br />";
			echo "<a href=\"translator.php?id=" . $row["Id"] . "\">" . $row["Title"] . "</a>";
			echo "<br />";
			echo $row["OrigLang"] . " | " . $row["Id"] . "." . $row["Ext"] . "<br />";
			echo "langs: ";
			foreach ($row["Languages"] as $value)
    			echo $value . " ";
			echo "<br /><br />";
		}
	}
		
?>