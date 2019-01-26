<html>
<?php
global $title;
global $onglet;
$onglet = "loginli";
$title = "Connexion/Deconnexion";
include("vue/head.php");
?>
<?php
if (!isset($_SESSION['prenom'])) {
	include("vue/vue_connect.php");
} else {
	include("vue/vue_disconnect.php");
}
?>
<?php
include("vue/foot.php");
?>
</html>