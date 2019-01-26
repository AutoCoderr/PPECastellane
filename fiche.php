<html>
<?php
$title = "Vos informations";
$onglet = "espaceli";
include("vue/head.php");
echo ("<br><br>");

if (!isset($_SESSION['perm'])) {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder aux diplomes</font>");
} else {
	include("vue/vue_fiche.php");
}

include("vue/foot.php");
?>
</html>