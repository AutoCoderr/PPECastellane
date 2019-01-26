<?php
$title = "Administration";
$onglet = "espaceli";
include("vue/head.php");

if (isset($_SESSION['perm'])) {
	if ($_SESSION['perm'] == "superadmin") {
		include("vue/vue_admin.php");
	} else {
		echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder à l'administration</font>");
	}
} else {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder à l'administration</font>");
}

include("vue/foot.php");
?>