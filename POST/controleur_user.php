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

if (isset($_POST['token']) & ($_POST['token'] == $_SESSION['token'])) {
	if (!isset($_POST['action'])) {
		echo json_encode(['rep' => 'failed', 'errors' => ["Les variables nécessaires ne sont pas renseignées"]]);
	} else {
		if ($_POST['action'] == "changePasswd")  {
			if (isset($_SESSION['perm'])) {
				$error = [];
				if (!isset($_POST['passwd']) | $_POST['passwd'] == "") {
					array_push($error, "Il n'y a pas de contenu.");
				}
			
				if (sizeof($error) != 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					$rep = $controleur->changePasswd($_POST['passwd']);
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
		} else {
			echo json_encode(['rep' => 'failed', 'errors' => ['Action non reconnu']]);
		}
	}
} else {
	echo json_encode(['rep' => 'failed', 'errors' => ["Le token ne correspond pas"]]);
}

?>