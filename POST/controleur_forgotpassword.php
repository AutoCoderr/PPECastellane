<?php

$errors = array();

if (!isset($_POST['prenom']) | $_POST['prenom'] == "") {
	array_push($errors, "Veuillez spécifier votre prenom");
}

if (!isset($_POST['nom']) | $_POST['nom'] == "") {
	array_push($errors, "Veuillez spécifier votre nom");
}

if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	include ("../controleur/controleur.php");

	$controleur = new Controleur(null,null,null,null);
	
	if ($controleur != null) {
		$rep = $controleur->forgotPassword($_POST['prenom'],$_POST['nom']);
		if ($rep == true & gettype($rep)!= "string") {
			echo json_encode(["rep" => "success"]);
		} else if (gettype($rep) == "string") {
			echo json_encode(["rep" => "failed", "errors" => [$rep]]);
		}
	} else {
		echo json_encode(["rep" => "failed", "errors" => ["Connexion à la base de données impossible"]]);
	}
}
?>