<?php

$errormsg = [
	"SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"DELETE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"UPDATE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"NOT_PERMISSION" => "Vous n'avez pas les bonnes permissions"
];

session_start();

include ("../controleur/controleur.php");

$controleur = new Controleur(null,null,null,null);

if ($controleur == null) {
	echo json_encode(['rep' => 'failed', 'errors' => ["Echec de connexion à la base de donnée"]]);
	exit();
}

if (isset($_POST['token']) & isset($_SESSION['token']) & $_POST['token'] == "anon") {
	echo json_encode(['rep' => 'failed', 'errors' => ["token vaut 'anon' mais vous êtes connecté"]]);
	exit();
}

if (isset($_POST['token']) & (($_POST['token'] == $_SESSION['token']) | (isset($_POST['action']) & $_POST['action'] == "sendAvis" & $_POST['token'] == "anon"))) {
	if (!isset($_POST['action'])) {
		echo json_encode(['rep' => 'failed', 'errors' => ["Les variables nécessaires ne sont pas renseignées"]]);
	} else {
		if ($_POST['action'] == "sendAvis")  {
			if (!isset($_SESSION['perm']) | $_SESSION['perm'] == "user" | $_SESSION['perm'] == "admin") {
				$error = [];
				if (!isset($_POST['content']) | $_POST['content'] == "") {
					array_push($error, "Il n'y a pas de contenu.");
				} else if (strlen($_POST['content']) > 500) {
					array_push($error, "Vous avez mis plus de 500 caractère");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->sendAvis($_POST['content']);
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
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas les permissions necéssaire"]]);
			}
		} else if ($_POST['action'] == "supprAvis")  {
			if ($_SESSION['perm'] == "user" | $_SESSION['perm'] == "admin") {
				$error = [];
				if (!isset($_POST['id']) | $_POST['id'] == "") {
					array_push($error, "Id non spécifié");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->supprAvis($_POST['id']);
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
		} else if ($_POST['action'] == "modifAvis")  {
			if ($_SESSION['perm'] == "user" | $_SESSION['perm'] == "admin") {
				$error = [];
				if (!isset($_POST['id']) | $_POST['id'] == "") {
					array_push($error, "Id non spécifié");
				}
				
				if (!isset($_POST['content']) | $_POST['content'] == "") {
					array_push($error, "Il n'y a pas de contenu.");
				} else if (strlen($_POST['content']) > 500) {
					array_push($error, "Vous avez mis plus de 500 caractère");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->modifAvis($_POST['id'], $_POST['content']);
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