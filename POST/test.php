<?php 
if (isset($_POST['pseudonyme']) & isset($_POST['password'])) {
	echo json_encode(["pseudonyme" => $_POST['pseudonyme'], "password" => $_POST['password']]);
}
error_log("MERDE");
?>