<?php
class Modele {
	private $bdd;
	
	public function __construct($server,$database,$user,$passwd) {
		$this->bdd = new PDO('mysql:host='.$server.';dbname='.$database.';charset=utf8', $user, $passwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		if ($this->bdd == null) {
			return null;
		}
	}
	
	public function update($table,$changes,$where) {
		$sets = "";
		$i = 0;
		foreach ($changes as $index => $val) {
			if (gettype($val) == "integer" | gettype($val) == "double" | $val == "NULL" | $val == "null") {
				$sets .= $index." = ".$val;
			} else if (gettype($val) == "string") {
				$sets .= $index." = '".str_replace("'","\'",$val)."'";
			}
			if ($i < sizeof($changes)-1) {
				$sets .= ", ";
			}
			$i += 1;
		}
		
		$sql = "UPDATE ".$table." SET ".$sets." WHERE ".$where.";";
		$rep = $this->bdd->prepare($sql);
		$rep->execute();
		$errors = $rep->errorInfo();
		if ($errors[0] == 00000) {
			return true;
		} else if ($errors[0] == 45000){
			return $errors[2];
		} else {
			error_log("[".$errors[0]."] ".$errors[2]." : ".$sql);
			return false;
		}
	}
	
	public function deletefrom($table,$where) {
		$sql = "DELETE FROM ".$table." WHERE ".$where.";";
		$rep = $this->bdd->prepare($sql);
		$rep->execute();
		$errors = $rep->errorInfo();
		if ($errors[0] == 00000) {
			return true;
		} else if ($errors[0] == 45000){
			return $errors[2];
		} else {
			error_log("[".$errors[0]."] ".$errors[2]." : ".$sql);
			return false;
		}
	}
	
	public function insertinto($table,$valuesarray) {
		$valuestring = "";
		$i = 0;
		foreach($valuesarray as $value) {
			if (gettype($value) == "integer" | gettype($value) == "double" | $value == "NULL" | $value == "null") {
				$valuestring .= $value;
			} else if (gettype($value) == "string") {
				$valuestring .= "'".str_replace("'","\'",$value)."'";
			}			
			if ($i < sizeof($valuesarray)-1) {
				$valuestring .= ",";
			}
			$i += 1;
		}
		$sql = "INSERT INTO ".$table." VALUES (".$valuestring.");";
		$rep = $this->bdd->prepare($sql);
		$rep->execute();
		$errors = $rep->errorInfo();
		if ($errors[0] == 00000) {
			return true;
		} else if ($errors[0] == 45000){
			return $errors[2];
		} else {
			error_log("[".$errors[0]."] ".$errors[2]." : ".$sql);
			return false;
		}
	}
	
	public function getfromtable($colonnes,$table,$args) {
		$sql = "SELECT ".$colonnes." FROM ".$table." WHERE ".$args.";";
		$rep = $this->bdd->prepare($sql);
		$rep->execute();
		$errors = $rep->errorInfo();
		if ($errors[0] == 00000) {
			$result = array();
			$i = 0;
			foreach ($rep as $row) {
				array_push($result, array());
				$j = 1;
				foreach ($row as $index => $val) {
					if ($j % 2 != 0) {
						$result[$i][$index] = $val;
					}
					$j += 1;
				}
				$i += 1;
			}
			return ["nb" => $i, "result" => $result];
		} else if ($errors[0] == 45000){
			return $errors[2];
		} else {
			error_log("error in : ".$sql);
			return false;
		}
	}
	
	public function callProcedure($name,$paramsList) {
		$params = "";
		$i = 0;
		foreach ($paramsList as $param) {
			if (gettype($param) == "integer" | gettype($param) == "double" | $param == "NULL" | $param == "null") {
				$params .= $param;
			} else if (gettype($param) == "string") {
				$param = str_replace("'","\'",$param); // bloque les injections sql
				$param = str_replace('"','\"',$param);
				$params .= "'".$param."'";
			}
			if ($i < sizeof($paramsList)-1) {
				$params .= ",";
			}
			$i += 1;
		}
		/*$outs = "";
		$i = 0;
		foreach($outList as $out) {
			$outs .= $out;
			if ($i < sizeof($outList)-1) {
				$outs .= ",";
			}
			$i += 1;
		}*/
		//$sql = "call ".$name."(".$params.",".$outs."); SELECT ".$outs.";";
		$sql = "call ".$name."(".$params.");";
		$rep = $this->bdd->prepare($sql);
		$rep->setFetchMode(PDO::FETCH_ASSOC);
		$rep->execute();
		$errors = $rep->errorInfo();
		if ($errors[0] == 00000) {
			//error_log("sql : ".$sql);
			$out = $rep->fetch();
			return $out;
		} else if ($errors[0] == 45000){
			return $errors[2];
		} else {
			error_log("[".$errors[0]."] ".$errors[2]." : ".$sql);
			return false;
		}
	}
}
?>