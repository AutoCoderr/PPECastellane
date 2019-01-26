<html>
<?php
global $title;
global $onglet;
$onglet = "espaceli";
$title = "Calendrier";
include("vue/head.php");
?>
<center>
<?php
if (!isset($_SESSION['perm'])) {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder au calendrier</font>");
} else if ($_SESSION['perm'] == "user" | $_SESSION['perm'] == "admin" | $_SESSION['perm'] == "superadmin") {
	include("vue/vue_calendar.php");
}
?>
</center>


<?php
include("vue/foot.php");
?>
</html>