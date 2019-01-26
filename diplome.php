<html>
<?php
global $title;
global $onglet;
$onglet = "espaceli";
$title = "Diplômes";
include("vue/head.php");
?>
<center>
<br>
<br>
<?php
if (!isset($_SESSION['perm'])) {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder aux diplomes</font>");
} else {
	include("vue/vue_diplome_".$_SESSION['perm'].".php");
}
?>
</center>


<?php
include("vue/foot.php");
?>
</html>