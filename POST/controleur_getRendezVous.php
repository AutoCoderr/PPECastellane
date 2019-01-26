<?php
session_start();
$errors = array();

if (!isset($_POST['token']) | $_POST['token'] == "") {
	array_push($errors, "Token non spécifié");
} else if ($_POST['token'] != $_SESSION['token']) {
	array_push($errors, "Le token ne correspond pas");
}

if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	include ("../controleur/controleur.php");

	$controleur = new Controleur(null,null,null,null);
	
	if ($controleur != null) {
		$rep = $controleur->getRendezVousUserAndroid();
		if (gettype($rep) == "string") {
			echo json_encode(["rep" => "failed", "errors" => [$rep]]);
		} else {
			echo json_encode(["rep" => "success", "rdvs" => $rep]);
		}
	} else {
		echo json_encode(["rep" => "failed", "errors" => ["Connexion à la base de données impossible"]]);
	}
}
?>