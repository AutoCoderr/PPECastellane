<?php
if ($controleur->aLeCode()) {
	echo ("<strong>Vous avez le code</strong>");
} else {
	echo ("<strong>Vous n'avez pas le code</strong>");
}
?>
<br><br>
<?php

echo $controleur->displayExam();

?>
<script>
var token = <?php echo $_SESSION['token'];?>;
</script>