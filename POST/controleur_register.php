<html>
<?php
global $title;
$onglet = "registerli";
$title = "Création de compte";
include("../vue/head.php");

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

$error = "";

if (!isset($_POST['prenom'])) {
	$error .= "\n<li>Veuillez spécifier votre prénom</li>";
} else if (strlen($_POST['prenom']) > 30) {
	$error .= "\n<li>Le prenom doit prendre moins de 30 caractères</li>";
} else if (!preg_match("/^\D+$/",$_POST['prenom'])) {
	$error .= "\n<li>Vous ne devez pas mettre de chiffre dans votre prenom</li>";
}

if (!isset($_POST['nom'])) {
	$error .= "\n<li>Veuillez spécifier votre nom</li>";
} else if (strlen($_POST['nom']) > 30) {
	$error .= "\n<li>Le nom doit prendre moins de 30 caractères</li>";
} else if (!preg_match("/^\D+$/",$_POST['nom'])) {
	$error .= "\n<li>Vous ne devez pas mettre de chiffre dans votre nom</li>";
}

if (!isset($_POST['type'])) {
	$error .= "\n<li>Veuillez spécifier le type de client que vous êtes</li>";
} else if ($_POST['type'] == "etudiant") {
	if (!isset($_POST['niveau'])) {
		$error .= "\n<li>Niveau d'étude non spécifié</li>";
	} else if (strlen($_POST['niveau']) > 128) {
		$error .= "\n<li>Le niveau d'étude doit prendre moins de 128 caractères</li>";
	}
	
	if (!isset($_POST['reduc'])) {
		$error .= "\n<li>Réduction non spécifié</li>";
	} else if (strlen($_POST['reduc']) > 3) {
		$error .= "\n<li>La reduction ne doit prendre que 3 caractères (ex : 10%)</li>";
	}
} else if ($_POST['type'] == "salarie") {
	if (!isset($_POST['entreprise'])) {
		$error .= "\n<li>Nom de l'entreprise non spécifié</li>";
	} else if (strlen($_POST['entreprise']) > 128) {
		$error .= "\n<li>Le nom de l'entreprise doit prendre moins de 128 caractères</li>";
	}
} else {
	$error .= "\n<li>Votre type est incorrect</li>";
}
	
if (!isset($_POST['addr'])) {
	$error .= "\n<li>Veuillez spécifier votre adresse</li>";
} else if (strlen($_POST['addr']) > 50) {
	$error .= "\n<li>L'adresse doit prendre moins de 50 caractères</li>";
}
if (!isset($_POST['dateN'])) {
	$error .= "\n<li>Veuillez spécifier votre date de naissance</li>";
} else if (!preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['dateN'])) {
	$error .= "\n<li>Format de date incorrect</li>";
} else if (date("Y").date("m").date("d") <= explode("-",$_POST['dateN'])[0].explode("-",$_POST['dateN'])[1].explode("-",$_POST['dateN'])[2]) {
	$error .= "\n<li>Veuillez rentrer une date antérieure à la date actuelle</li>";
}
if (!isset($_POST['numtel'])) {
	$error .= "\n<li>Veuillez spécifier votre numéro de télephone</li>";
} else if (!preg_match("/^((0|0033)[1-68])([0-9]{8})$/",str_replace("+","00",str_replace(" ","",$_POST['numtel'])))) {
	$error .= "\n<li>Format de numéro de télephone incorrect</li>";
}
if (!isset($_POST['facturation'])) {
	$error .= "\n<li>Veuillez spécifier votre mode de facturation</li>";
} else if (strlen($_POST['facturation']) > 15) {
	$error .= "\n<li>La facturation doit prendre moins de 15 caractères</li>";
}
if (!isset($_POST['passwd'])) {
	$error .= "\n<li>Veuillez spécifier votre mode de passe</li>";
}
if (!isset($_POST['mail'])) {
	$error .= "\n<li>Veuillez spécifier votre adresse mail</li>";
} else if (!preg_match("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/",$_POST['mail'])) {
	$error .= "\n<li>Format du mail incorrect</li>";
} else if (strlen($_POST['mail']) > 70) {
	$error .= "\n<li>La longueur de l'adresse mail ne doit pas dépasser 70 caractères</li>";
}
if ($error != "") {
	include("../vue/vue_register_failed.php");
} else {
	if ($controleur == null) {
		$error = "\n<li>Echec de la connexion à la base de données</li>";
		include("../vue/vue_register_failed.php");
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
				$error = "\n<li>Erreur lors de la création du client : ".$errormsg[$iscreated]."</li>";
			} else {
				$error = "\n<li>Erreur lors de la création du client : ".$iscreated."</li>";
			}
			include("../vue/vue_register_failed.php");
		} else {
			$isconnect = $controleur->connectuser($_POST['prenom'],$_POST['nom'],$_POST['passwd']);
			if ($isconnect == false) {
				$error = "\n<li>Inscription Reussi mais connexion echouée</li>";
				include("../vue/vue_register_failed.php");
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
				include("../vue/vue_register_success.php");
			}
		}
	}
}

include("../vue/foot.php");
?>
</html>
