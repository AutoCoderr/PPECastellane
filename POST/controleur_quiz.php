<?php

$errors = array();

if (!isset($_POST['idQuestion']) | $_POST['idQuestion'] == "") {
	array_push($errors, "Question non specifiée");
}

if (!isset($_POST['idReponse']) | $_POST['idReponse'] == "") {
	array_push($errors, "Reponse non specifiée");
}

if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	include ("../controleur/controleur.php");

	$controleur = new Controleur(null,null,null,null);
	
	if ($controleur != null) {
		session_start();
		$rep = $controleur->verifReponse($_POST['idQuestion'],$_POST['idReponse']);
		if ($rep == "next") {
			echo json_encode(["rep" => "next"]);
		} else if ($rep == "finish") {
			echo json_encode(["rep" => "finish", "note" => $_SESSION['note']]);
		} else {
			echo json_encode(["rep" => "failed", "errors" => [$rep]]);
		}
	} else {
		echo json_encode(["rep" => "failed", "errors" => ["Connexion à la base de données impossible"]]);
	}
}
?>