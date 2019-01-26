<?php

$errors = array();

if (!isset($_POST['prenom']) | $_POST['prenom'] == "") {
	array_push($errors, "Prenom non spécifié");
} else if (strlen($_POST['prenom']) > 30) {
	array_push($errors, "Le prenom ne doit pas prendre plus de 30 caractères");
}

if (!isset($_POST['nom']) | $_POST['nom'] == "") {
	array_push($errors, "Nom non spécifié");
} else if (strlen($_POST['nom']) > 30) {
	array_push($errors, "Le nom ne doit pas prendre plus de 30 caractères");
}

if (!isset($_POST['passwd']) | $_POST['passwd'] == "") {
	array_push($errors, "Mot de passe non spécifié");
}
if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	include ("../controleur/controleur.php");

	$controleur = new Controleur(null,null,null,null);
	
	if ($controleur != null) {
		$isconnect = $controleur->connectuser($_POST['prenom'],$_POST['nom'],$_POST['passwd']);
		if (gettype($isconnect) == "string") {
			echo json_encode(["rep" => "failed", "errors" => [$isconnect]]);
		} else if ($isconnect == false) {
			echo json_encode(["rep" => "failed", "errors" => ["Prenom et/ou nom et/ou mot de passe incorrect"]]);
		} else {
			session_start();
			$_SESSION['prenom'] = $isconnect["prenom"];
			$_SESSION['nom'] =  $isconnect['nom'];
			$_SESSION['perm'] = $isconnect['perm'];
			$_SESSION['id'] = $isconnect['id'];
			$_SESSION['type'] = $isconnect["type"];
			$_SESSION['currentQuestion'] = 1;
			$_SESSION['token'] = rand();
			echo json_encode(["rep" => "success", "prenom" => $isconnect["prenom"], "nom" => $isconnect["nom"], "perm" => $isconnect["perm"], "token" => $_SESSION['token']]);
		}
	} else {
		echo json_encode(["rep" => "failed", "errors" => ["Connexion à la base de données impossible"]]);
	}
}
?>