<?php 
session_start();
$yo = $_SESSION;
//$yo = ["wesh" => "merde"];
$wesh = [];
foreach ($yo as $key => $value) {
	$wesh[$key] = $value;
}
echo json_encode($wesh);
?>