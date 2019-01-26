<?php
$title = "Vehicules";
$onglet = "espaceli";
include("vue/head.php");

if (isset($_SESSION['perm'])) {
	if ($_SESSION['perm'] == "superadmin") {
		include("vue/vue_vehicules.php");
	} else {
		echo ("<br><br><font color='red' size='4'>Vous n'avez pas les permissions pour accéder aux vehicules</font>");
	}
} else {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder aux vehicules</font>");
}

include("vue/foot.php");
?>