<?php
$title = "Mot de passe oublié";
$onglet = "loginli";
include("vue/head.php");

if (isset($_SESSION['perm'])) {
	echo ("<br><br><font size='3'>Vous ne pouvez pas utiliser cette option en étant connecté</font>");
} else {
	include("vue/vue_forgotpassword.php");
}

include("vue/foot.php");
?>