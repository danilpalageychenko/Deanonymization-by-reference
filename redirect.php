<?php
	$file = fopen("redirect.txt", "a");
	fwrite($file, '"' . htmlspecialchars($_GET[""]) . '"' );
	fclose($file);	
?>
