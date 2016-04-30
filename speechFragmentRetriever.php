<?php
	$file = fopen("speechFragments/".$_GET['id'].".dat", "r") or die();
	echo fgets($file);
	fclose($file);
?>