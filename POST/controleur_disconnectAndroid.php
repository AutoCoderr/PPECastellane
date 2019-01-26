<?php

session_start();

$errors = array();

if (!isset($_POST['token']) | $_POST['token'] == "") {
	array_push($errors, "Token non spécifié");
} else if ($_POST['token'] != $_SESSION['token']) {
	array_push($errors, "Le token le correspond pas");
}

if (sizeof($errors) > 0) {
	echo json_encode(["rep" => "failed", "errors" => $errors]);
} else {
	session_destroy();
	echo json_encode(["rep" => "success"]);
}
?>