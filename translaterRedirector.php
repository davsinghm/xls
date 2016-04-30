<?php
	if (file_exists('speechFragments/'.$_GET['id'].'.dat'))
	{
		header('Location: translater.php?id='.$_GET['id']);
		die();
	}

	header('Location: translaterSpeechFragmenter.php?id='.$_GET['id']);
?>