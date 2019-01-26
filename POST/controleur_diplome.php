<?php

$errormsg = [
	"SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"DELETE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"UPDATE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"PLANNING_NOT_EXIST" => "Ce planning n'existe pas",
	"NOT_PERMISSION" => "Vous n'avez pas les bonnes permissions"
];

session_start();

include ("../controleur/controleur.php");

$controleur = new Controleur(null,null,null,null);

if ($controleur == null) {
	echo json_encode(['rep' => 'failed', 'errors' => ["Echec de connexion à la base de donnée"]]);
	exit();
}

if (isset($_POST['token']) & $_POST['token'] == $_SESSION['token']) {
	if (!isset($_POST['action'])) {
		echo json_encode(['rep' => 'failed', 'errors' => ["Les variables nécessaires ne sont pas renseignées"]]);
	} else {
		if ($_POST['action'] == "code")  {
			if ($_SESSION['perm'] == "admin" | $_SESSION['perm'] == "superadmin") {
				$error = [];
				if (!isset($_POST['code']) | ($_POST['code'] != 1 & $_POST['code'] != 0)) {
					array_push($error, "Champ 'code' incorrect");
				}
				if (!isset($_POST['id'])) {
					array_push($error, "Champ 'id' non spécifié");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->altercode($_POST['id'],$_POST['code']);
					if ($rep == true & gettype($rep) != "string") {
						echo json_encode(['rep' => 'success', 'id' => $rep]);
					} else {
						if (array_key_exists($rep, $errormsg)) {
							echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
						} else {
							echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
						}
					}
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else if ($_POST['action'] == "setnote")  {
			if ($_SESSION['perm'] == "admin" | $_SESSION['perm'] == "superadmin") {
				$error = [];
				if (!isset($_POST['note'])) {
					array_push($error, "Champ 'note' incorrect");
				} else if (!preg_match("/^[0-9]$|^0[0-9]$|^1[0-9]$|^20$/",$_POST['note']) & $_POST['note'] != "") {
					array_push($error, "Format de la note incorrect");
				}
				if (!isset($_POST['id'])) {
					array_push($error, "Champ 'id' non spécifié");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->setnote($_POST['id'],$_POST['note']);
					if ($rep == true & gettype($rep) != "string") {
						echo json_encode(['rep' => 'success', 'id' => $rep]);
					} else {
						if (array_key_exists($rep, $errormsg)) {
							echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
						} else {
							echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
						}
					}
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else {
			echo json_encode(['rep' => 'failed', 'errors' => ['Action non reconnu']]);
		}
	}
} else {
	echo json_encode(['rep' => 'failed', 'errors' => ["Le token ne correspond pas"]]);
}

?>