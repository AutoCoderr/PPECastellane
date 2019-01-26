<?php

//echo ("SALUT<br/>");

if (!isset($limit)) {
	if (isset($_GET['limit'])) {
		$limit = $_GET['limit'];
	} else {
		$limit = 0;
	}
}
$nb = 0;
while ($nb < $limit) {
	echo ("SALUT<br/>");
	$nb += 1;
}

/*
if (!isset($nb)) {
	$nb = 0;
}
$nb += 1;

if ($nb < $limit) {
	include("rec.php");
}*/
?>