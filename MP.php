<?php
$title = "Vos messages privés";
$onglet = "MPli";
include ("vue/head.php");

if (!isset($_SESSION['prenom'])) {
	echo ("<font color='red' size='5'>Vous devez vous <a href='/login.php'>connecter</a> pour acceder à ceci</font>");
} else {
	include("vue/vue_MP.php");
}

include ("vue/foot.php");
?>