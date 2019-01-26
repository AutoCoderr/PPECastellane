<?php

$errormsg = [
	"SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"DELETE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"UPDATE_ERROR" => "Erreur d'interaction avec la base de donnée",
];

session_start();

include ("../controleur/controleur.php");

$controleur = new Controleur(null,null,null,null);

if ($controleur == null) {
	echo json_encode(['rep' => 'failed', 'errors' => ["Echec de connexion à la base de donnée"]]);
	exit();
}

if ((isset($_POST['token']) & $_POST['token'] == $_SESSION['token']) | ($_POST['action'] == "sendAsContact" & $_POST['token'] == "anon")) {
	if (!isset($_POST['action'])) {
		echo json_encode(['rep' => 'failed', 'errors' => ["Les variables nécessaires ne sont pas renseignées"]]);
	} else {
		 if ($_POST['action'] == "suppr") {
			$error = [];
			if (!isset($_POST['id']) | $_POST['id'] == "") {
				array_push($error, "Id du MP non spécifié");
			}
			if (sizeof($error) != 0) {
				echo json_encode(['rep' => 'failed', 'errors' => $error]);
			} else {
				$rep = $controleur->supprmp($_POST['id']);
				if ($rep == true & gettype($rep) != "string") {
					echo json_encode(['rep' => 'success']);
				} else {
					if (array_key_exists($rep, $errormsg)) {
						echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
					} else {
						echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
					}
				}
			}
		} else if ($_POST['action'] == "send") {
			$error = [];
			if (!isset($_POST['dst']) | $_POST['dst'] == "") {
				array_push($error, "Destinataire non spécifié");
			}
			if (!isset($_POST['objet']) | $_POST['objet'] == "") {
				array_push($error, "Objet non spécifié");
			} else if (strlen($_POST['objet']) > 30) {
				array_push($error, "L'objet fait plus de 30 caractères");
			}
			if (!isset($_POST['content']) | $_POST['content'] == "") {
				array_push($error, "Contenu du message non spécifié");
			} else if (strlen($_POST['content']) > 500) {
				array_push($error, "Le message fait plus de 500 caractères");
			}
			if (sizeof($error) != 0) {
				echo json_encode(['rep' => 'failed', 'errors' => $error]);
			} else {
				$rep = $controleur->sendmp($_POST['dst'],$_POST['objet'],$_POST['content']);
				if ($rep == true & gettype($rep) != "string") {
					echo json_encode(['rep' => 'success']);
				} else {
					if (array_key_exists($rep, $errormsg)) {
						echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
					} else {
						echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
					}
				}
			}
		} else if ($_POST['action'] == "sendAsContact") {
			$error = [];
			if (!isset($_POST['nom']) | $_POST['nom'] == "") {
				array_push($error, "Nom non spécifié");
			}
			if (!isset($_POST['prenom']) | $_POST['prenom'] == "") {
				array_push($error, "Prenom non spécifié");
			}
			if (!isset($_POST['mail']) | $_POST['mail'] == "") {
				array_push($error, "Mail non spécifié");
			} else if (!preg_match("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/",$_POST['mail'])) {
				array_push($error, "Format de mail incorrect");
			}
			if (!isset($_POST['numtel']) | $_POST['numtel'] == "") {
				array_push($error, "Numero de télephone non spécifié");
			} else if (!preg_match("/^((0|0033)[1-68])([0-9]{8})$/",str_replace("+","00",str_replace(" ","",$_POST['numtel'])))) {
				array_push($error, "Format de numéro de télephone incorrect");
			}
			if (!isset($_POST['objet']) | $_POST['objet'] == "") {
				array_push($error, "Objet non spécifié");
			} else if (strlen($_POST['objet']) > 60) {
				array_push($error, "L'objet fait plus de 30 caractères");
			}
			if (!isset($_POST['content']) | $_POST['content'] == "") {
				array_push($error, "Contenu du message non spécifié");
			} else if (strlen($_POST['content']) > 450) {
				array_push($error, "Le message fait plus de 500 caractères");
			}
			if (sizeof($error) != 0) {
				echo json_encode(['rep' => 'failed', 'errors' => $error]);
			} else {
				$rep = $controleur->sendmpAsContact($_POST['objet'],$_POST['content'],$_POST['nom'],$_POST['prenom'],$_POST['mail'],$_POST['numtel']);
				if ($rep == true & gettype($rep) != "string") {
					echo json_encode(['rep' => 'success']);
				} else {
					if (array_key_exists($rep, $errormsg)) {
						echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
					} else {
						echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
					}
				}
			}
		} else if ($_POST['action'] == "setLu") {
			$error = [];
			if (!isset($_POST['id']) | $_POST['id'] == "") {
				array_push($error, "Id non spécifié");
			}
			if (sizeof($error) != 0) {
				echo json_encode(['rep' => 'failed', 'errors' => $error]);
			} else {
				$rep = $controleur->setLu($_POST['id']);
				if ($rep == true & gettype($rep) != "string") {
					echo json_encode(['rep' => 'success']);
				} else {
					if (array_key_exists($rep, $errormsg)) {
						echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
					} else {
						echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
					}
				}
			}
		} else {
			echo json_encode(['rep' => 'failed', 'errors' => ['Action non reconnu']]);
		}
	}
} else {
	echo json_encode(['rep' => 'failed', 'errors' => ["Le token ne correspond pas"]]);
}

?>