<?php
	$file = fopen("subtitles/".$_GET['id'].".dat", "r") or die();
	echo fgets($file);
	fclose($file);
?>