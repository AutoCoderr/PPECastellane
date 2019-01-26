<html>
<?php
global $title;
global $onglet;
$onglet = "espaceli";
$title = "Code en ligne";
include("vue/head.php");
?>
<center>
<br>
<br>
<?php
if (!isset($_SESSION['perm']) | $_SESSION['perm'] != "user") {
	echo ("<br><br><font color='red' size='4'>Vous devez vous <a href='/login.php' >connecter</a> pour accéder aux diplomes</font>");
} else {
	include("vue/vue_quiz.php");
}
?>
</center>


<?php
include("vue/foot.php");
?>
</html>