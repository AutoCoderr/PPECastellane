<?php

$errormsg = [
	"CLIENT_SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"CLIENT_INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"COMPTE_INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"ETUDIANT_INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"SALARIE_INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"DELETE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"UPDATE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"COMPTE_ALREADY_EXIST" => "Ce compte existe déjà"
];

$errors = array();

if (!isset($_POST['prenom']) | $_POST['prenom'] == "") {
	$errors[] = "Veuillez spécifier votre prénom";
} else if (strlen($_POST['prenom']) > 30) {
	$errors[] = "Le prenom doit prendre moins de 30 caractères";
} else if (!preg_match("/^\D+$/",$_POST['prenom'])) {
	$errors[] = "Vous ne devez pas mettre de chiffre dans votre prenom";
}

if (!isset($_POST['nom']) | $_POST['nom'] == "") {
	$errors[] = "Veuillez spécifier votre nom";
} else if (strlen($_POST['nom']) > 30) {
	$errors[] = "Le nom doit prendre moins de 30 caractères";
} else if (!preg_match("/^\D+$/",$_POST['nom'])) {
	$errors[] = "Vous ne devez pas mettre de chiffre dans votre nom";
}

if (!isset($_POST['type']) | $_POST['type'] == "") {
	$errors[] = "Veuillez spécifier le type de client que vous êtes";
} else if ($_POST['type'] == "etudiant") {
	if (!isset($_POST['niveau']) | $_POST['niveau'] == "") {
		$errors[] = "Niveau d'étude non spécifié";
	} else if (strlen($_POST['niveau']) > 128) {
		$errors[] = "Le niveau d'étude doit prendre moins de 128 caractères";
	}
	
	if (!isset($_POST['reduc']) | $_POST['reduc'] == "") {
		$errors[] = "Réduction non spécifié";
	} else if (strlen($_POST['reduc']) > 3) {
		$errors[] = "La reduction ne doit prendre que 3 caractères (ex : 10%)";
	}
} else if ($_POST['type'] == "salarie") {
	if (!isset($_POST['entreprise']) | $_POST['entreprise'] == "") {
		$errors[] = "Nom de l'entreprise non spécifié";
	} else if (strlen($_POST['entreprise']) > 128) {
		$errors[] = "Le nom de l'entreprise doit prendre moins de 128 caractères";
	}
} else {
	$errors[] = "Votre type est incorrect";
}
	
if (!isset($_POST['addr']) | $_POST['addr'] == "") {
	$errors[] = "Veuillez spécifier votre adresse";
} else if (strlen($_POST['addr']) > 50) {
	$errors[] = "L'adresse doit prendre moins de 50 caractères";
}
if (!isset($_POST['dateN']) | $_POST['dateN'] == "") {
	$errors[] = "Veuillez spécifier votre date de naissance";
	$errors[] = "Veuillez spécifier votre date de naissance";
} else if (!preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['dateN'])) {
	$errors[] = "Format de date incorrect";
} else if (date("Y").date("m").date("d") <= explode("-",$_POST['dateN'])[0].explode("-",$_POST['dateN'])[1].explode("-",$_POST['dateN'])[2]) {
	$errors[] = "Veuillez rentrer une date antérieure à la date actuelle";
}
if (!isset($_POST['numtel'])) {
	$errors[] = "Veuillez spécifier votre numéro de télephone";
} else if (!preg_match("/^((0|0033)[1-68])([0-9]{8})$/",str_replace("+","00",str_replace(" ","",$_POST['numtel'])))) {
	$errors[] = "Format de numéro de télephone incorrect";
}
if (!isset($_POST['facturation'])) {
	$errors[] = "Veuillez spécifier votre mode de facturation";
} else if (strlen($_POST['facturation']) > 15) {
	$errors[] = "La facturation doit prendre moins de 15 caractères";
}
if (!isset($_POST['passwd'])) {
	$errors[] = "Veuillez spécifier votre mode de passe";
}
if (!isset($_POST['mail'])) {
	$errors[] = "Veuillez spécifier votre adresse mail";
} else if (!preg_match("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/",$_POST['mail'])) {
	$errors[] = "Format du mail incorrect";
} else if (strlen($_POST['mail']) > 70) {
	$errors[] = "La longueur de l'adresse mail ne doit pas dépasser 70 caractères";
}
if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	include("../controleur/controleur.php");
	$controleur = new Controleur();
	if ($controleur == null) {
		echo json_encode(["rep" => "failed", "errors" => ["Echec de la connexion à la base de données"]]);
	} else {
		if ($_POST['type'] == "etudiant") {
			$type_vars = ["niveau" => $_POST['niveau'], "reduc" => $_POST['reduc']];
		} else {
			$type_vars = ["entreprise" => $_POST['entreprise']];
		}
		$iscreated = $controleur->createclient($_POST['prenom'],$_POST['nom'],$_POST['passwd'],$_POST['type'],$type_vars,$_POST['addr'],$_POST['dateN'],
											   $_POST['numtel'],$_POST['facturation'],$_POST['mail']);
		if (gettype($iscreated) == "string") {
			if (array_key_exists($iscreated, $errormsg)) {
				echo json_encode(["rep" => "failed", "errors" => ["Erreur lors de la création du client : ".$errormsg[$iscreated]]]);
			} else {
				echo json_encode(["rep" => "failed", "errors" => ["Erreur lors de la création du client : ".$errormsg[$iscreated]]]);
			}
		} else {
			$isconnect = $controleur->connectuser($_POST['prenom'],$_POST['nom'],$_POST['passwd']);
			if ($isconnect == false) {
				echo json_encode(["rep" => "failed", "errors" => ["Inscription Reussi mais connexion echouée"]]);
			} else {
				session_destroy();
				session_start();
				$_SESSION['prenom'] = $isconnect["prenom"];
				$_SESSION['nom'] = $isconnect["nom"];
				$_SESSION['perm'] = $isconnect["perm"];
				$_SESSION['id'] = $isconnect["id"];
				$_SESSION['type'] = $isconnect["type"];
				$_SESSION['currentQuestion'] = 1;
				$_SESSION['token'] = rand();
				echo json_encode(["rep" => "success", "prenom" => $isconnect["prenom"], "nom" => $isconnect["nom"], "perm" => $isconnect["perm"], "token" => $_SESSION['token']]);
			}
		}
	}
}
?>
