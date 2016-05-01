<?php


	//ffmpeg -i infile.mp4 -f srt -i infile.srt -c:v copy -c:a copy -c:s mov_text outfile.mp4

	//the one with srt
	//$cmd = sprintf('/usr/local/bin/ffmpeg -i ./videos/%1$s.mp4 -i ./audioTracks/%1$s_%2$s.m4a -f srt -i ./subtitles/%1$s_%2$s.srt -c:v copy -c:a copy -c:s mov_text ./downloads/%1$s_%2$s.mp4 -y', $_POST["id"], $_POST["audioLanguage"]);

	$cmd = sprintf('/usr/local/bin/ffmpeg -i ./videos/%1$s.mp4 -i ./audioTracks/%1$s_%2$s.m4a -c:v copy -c:a copy ./downloads/%1$s_%2$s.mp4 -y', $_POST["id"], $_POST["audioLanguage"]);

	exec($cmd, $output, $return_var);

	//download file, rather opening it
	if (!$return_var) {
		header('Content-Type: application/mp4');
		header('Content-Disposition: attachment; filename=' .$_POST["id"] . "_" . $_POST["audioLanguage"] . ".mp4");
		header('Pragma: no-cache');
		readfile("./downloads/".  $_POST["id"] . "_" . $_POST["audioLanguage"] . ".mp4");

		//header('Location: ./downloads/'.  $_POST["id"] . "_" . $_POST["audioLanguage"] . ".mp4");
		
		die();
	} else {
		echo "Cannot mux file. No space left?";
	}
