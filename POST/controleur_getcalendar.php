<?php
session_start();

include ("../controleur/controleur.php");

$controleur = new Controleur(null,null,null,null);

if ($controleur == null) {
	echo "<font size='5' color='red'>Echec de connexion à la base de donnée</font>";
	exit();
}

if (isset($_POST['token']) & $_POST['token'] == $_SESSION['token']) {
	if (!isset($_POST['type'])) {
		echo "<font size='5' color='red'>Type de calendrier non specifié ('year' ou 'month')</font>";
	} else {
		if ($_POST['type'] == "year") {
			if (!isset($_POST['year'])) {
				echo "<font size='5' color='red'>Année non specifiée</font>";
			} else if (!preg_match("/^[0-9]+$/",$_POST['year'])) {
				echo "<font size='5' color='red'>format de l'année incorrect</font>";
			} else {
				$rep = $controleur->getyear($_POST['year']);
				echo $rep;
			}
		} else if ($_POST['type'] == "month") {
			if (!isset($_POST['year'])) {
				echo "<font size='5' color='red'>Année non specifiée</font>";
			} else if (!preg_match("/^[0-9]+$/",$_POST['year'])) {
				echo "<font size='5' color='red'>format de l'année incorrect</font>";
			} else {
				if (!isset($_POST['month'])) {
					echo "<font size='5' color='red'>Mois non specifiée</font>";
				} else if (!preg_match("/^([0-9])|(1[0-2])$/",$_POST['month'])) {
					echo "<font size='5' color='red'>format du mois incorrect</font>";
				} else {
					$daynb = 0;
					$i = 1;
					while ($i<$_POST['month']) {
						$daynb += $controleur->mois_infos[complete($i,2)]['nbj'];
						$i += 1;
					}
					if ($_POST['year']%4 == 0 & $_POST['month'] > 2) {
						$daynb += 1;
					}
					
					if ($_POST['year'] >= 2017) {
						$firstday = 0+($_POST['year']-2017);
						$firstday += floor(($_POST['year']-2017)/4);
					} else {
						$firstday = 7-(2017-$_POST['year']);
						$firstday -= floor((2020-$_POST['year'])/4);
					}
					$firstday = -1 * ((floor($firstday/7))*7-($firstday)); // equation pour retourner le modulo positif en cas de dividende negatif
					
					/*a /d
					  a = qd+r
					 -r = qd-a
					  r = -1 * (qd-a) */
					
					$rep = $controleur->getmonth($_POST['year'],$_POST['month'],$daynb,$firstday,"month");
					echo $rep['html'];
				}
			}
		} else {
			echo "<font size='5' color='red'>Type specifié non reconnu</font>";
		}
	}
} else {
	echo "<font size='5' color='red'>Le token ne correspond pas</font>";
}
?>