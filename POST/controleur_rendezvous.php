<?php

$errormsg = [
	"HOUR_NOT_AVAILABLE" => "Plage horaire déjà occupée",
	"SELECT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"INSERT_ERROR" => "Erreur d'interaction avec la base de donnée",
	"DELETE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"UPDATE_ERROR" => "Erreur d'interaction avec la base de donnée",
	"EXAM_NOT_EXIST" => "Cet exam n'existe pas",
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
	if (!isset($_POST['type']) | !isset($_POST['action'])) {
		echo json_encode(['rep' => 'failed', 'errors' => ["Les variables nécessaires ne sont pas renseignées"]]);
	} else {
		if ($_POST['action'] == "add")  {
			if ($_SESSION['perm'] == "user") {
				if ($_POST['type'] == "planning") {
					$error = [];
					if (!isset($_POST['date']) | $_POST['date'] == "") {
						array_push($error, "Date non spécifiée");
					} else if (!preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['date'])) {
						array_push($error, "Format de la date incorrect");
					} else if (str_replace("-","",$_POST['date']) < str_replace("-","",date("Y-m-d"))) {
						array_push($error, "La date du rendez-vous ne doit pas être anterieure à la date actuelle");
					}
			
					if (!isset($_POST['heureD']) | $_POST['heureD'] == "") {
						array_push($error, "Heure de début non spécifiée");
					} else if (!preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/",$_POST['heureD'])) {
						array_push($error, "Format de l'heure de début incorrect");
					}
			
					if (!isset($_POST['heureF']) | $_POST['heureF'] == "") {
						array_push($error, "Heure de fin non spécifiée");
					} else if (!preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/",$_POST['heureF'])) {
						array_push($error, "Format de l'heure de fin incorrect");
					}
			
					if (isset($_POST['heureD']) & isset($_POST['heureF']) & $_POST['heureD'] != "" & $_POST['heureF'] != "" & 
						str_replace(":","",$_POST['heureD']) >= str_replace(":","",$_POST['heureF'])) {
						array_push($error, "L'heure de début doit être anterieure à l'heure de fin");
					}
			
					if (!isset($_POST['lecon']) | $_POST['lecon'] == "") {
						array_push($error, "Leçon non spécifiée");
					} else if (!$controleur->existlecon($_POST['lecon'])) {
						 array_push($error, "Leçon inconnue");				 
					}
			
					if (sizeof($error) != 0) {
						echo json_encode(['rep' => 'failed', 'errors' => $error]);
					} else {
						$rep = $controleur->add_planning($_POST['date'],$_POST['heureD'],$_POST['heureF'],$_POST['lecon'],$_SESSION['id']);
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
			
				} else if ($_POST['type'] == "exam") {
					$error = [];
					if (!isset($_POST['date']) | $_POST['date'] == "") {
						array_push($error, "Date non spécifiée");
					} else if (!preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['date'])) {
						array_push($error, "Format de la date incorrect");
					} else if (str_replace("-","",$_POST['date']) < str_replace("-","",date("Y-m-d"))) {
						array_push($error, "La date du rendez-vous ne doit pas être anterieure à la date actuelle");
					}
			
					if (!isset($_POST['heure']) | $_POST['heure'] == "") {
						array_push($error, "Heure non spécifiée");
					} else if (!preg_match("/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/",$_POST['heure'])) {
						array_push($error, "Format de l'heure incorrect");
					}
			
					if (!isset($_POST['typeexam']) | $_POST['typeexam'] == "") {
						array_push($error, "Type d'exam non spécifiée");
					} else if (!$controleur->existexam($_POST['typeexam'])) {
						array_push($error, "Type d'exam inconnu");				 
					}
					
					if (sizeof($error) != 0) {
						echo json_encode(['rep' => 'failed', 'errors' => $error]);
					} else {
						$rep = $controleur->add_exam($_POST['date'],$_POST['heure'],$_POST['typeexam'],$_SESSION['id']);
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
					echo json_encode(['rep' => 'failed', 'errors' => ["Champ 'type' non reconnu (".$_POST['type'].")"]]);
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else if ($_POST['action'] == "delete") {
			if ($_SESSION['perm'] == "user" | $_SESSION['perm'] == "superadmin") {
				$error = [];
				if (!isset($_POST['id']) | $_POST['id'] == "") {
					array_push($error, "Id non spécifiée");				 
				}
				if (!isset($_POST['type']) | $_POST['type'] == "") {
					array_push($error, "Type non spécifié");				 
				}
				if (sizeof($error) > 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					if($_POST['type'] == "Planning") {
						$rep = $controleur->delete_planning($_POST['id'],$_SESSION['id']);
						if ($rep == true & gettype($rep) != "string") {
							echo json_encode(['rep' => 'success']);
						} else {
							if (array_key_exists($rep, $errormsg)) {
								echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
							} else {
								echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
							}
						}
					} else if ($_POST['type'] == "Exam") {
						$rep = $controleur->delete_exam($_POST['id'],$_SESSION['id']);
						if ($rep == true & gettype($rep) != "string") {
							echo json_encode(['rep' => 'success']);
						} else {
							if (array_key_exists($rep, $errormsg)) {
								echo json_encode(['rep' => 'failed', 'errors' => [$errormsg[$rep]]]);
							} else {
								echo json_encode(['rep' => 'failed', 'errors' => [$rep]]);
							}
						}
					} else {
						echo json_encode(['rep' => 'failed', 'errors' => ["Type non reconnu"]]);
					}
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else if ($_POST['action'] == "valid") {
			if ($_SESSION['perm'] == "admin") {
				$error = [];
				if (!isset($_POST['id']) | $_POST['id'] == "") {
					array_push($error, "Id non spécifiée");				 
				}
				if (!isset($_POST['type']) | $_POST['type'] == "") {
					array_push($error, "Type non spécifié");				 
				}
				if (sizeof($error) > 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					if ($_POST['type'] != "Planning" & $_POST['type'] != "Exam") {
						echo json_encode(['rep' => 'failed', 'errors' => ["Type non reconnu"]]);
					} else {
						$rep = $controleur->valid_rendezvous($_POST['id'],$_SESSION['id'],$_POST['type']);
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
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else if ($_POST['action'] == "devalid") {
			if ($_SESSION['perm'] == "admin" | $_SESSION['perm'] == "superadmin") {
				$error = [];
				if (!isset($_POST['id']) | $_POST['id'] == "") {
					array_push($error, "Id non spécifiée");				 
				}
				if (!isset($_POST['type']) | $_POST['type'] == "") {
					array_push($error, "Type non spécifié");				 
				}
				if (sizeof($error) > 0) {
					echo json_encode(['rep' => 'failed', 'errors' => $error]);
				} else {
					if ($_POST['type'] != "Planning" & $_POST['type'] != "Exam") {
						echo json_encode(['rep' => 'failed', 'errors' => ["Type non reconnu"]]);
					} else {
						$rep = $controleur->devalid_rendezvous($_POST['id'],$_POST['type']);
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
				}
			} else {
				echo json_encode(['rep' => 'failed', 'errors' => ["Vous ne disposez pas des permissions necéssaire"]]);
			}
		} else {
			echo json_encode(['rep' => 'failed', 'errors' => 'Action non reconnu']);
		}
	}
} else {
	echo json_encode(['rep' => 'failed', 'errors' => ["Le token ne correspond pas"]]);
}

?>