<?php
include ("/var/www/PPE/modele/modele.php");

function lastmonth($ans,$mois) {
	$mois -= 1;
	if ($mois == 0) {
		$mois = 12;
		$ans -= 1;
	}
	return [$ans,$mois];
}

function complete($var,$num) {	
	while (strlen($var) < $num) {
		$var = "0".$var;
	}
	return $var;
}

function removeForbidChar($str) {
	return str_replace("<","&lt;",str_replace(">","&gt;",$str));
}

class Controleur {
	
	public $mois_infos = [
		"01" => ['nbj' => 31, 'name' => 'Janvier'],
		"02" => ['nbj' => 28, 'name' => 'Février'],
		"03" => ['nbj' => 31, 'name' => 'Mars'],
		"04" => ['nbj' => 30, 'name' => 'Avril'],
		"05" => ['nbj' => 31, 'name' => 'Mai'],
		"06" => ['nbj' => 30, 'name' => 'Juin'],
		"07" => ['nbj' => 31, 'name' => 'Juillet'],
		"08" => ['nbj' => 31, 'name' => 'Aout'],
		"09" => ['nbj' => 30, 'name' => 'Septembre'],
		"10" => ['nbj' => 31, 'name' => 'Octobre'],
		"11" => ['nbj' => 30, 'name' => 'Novembre'],
		"12" => ['nbj' => 31, 'name' => 'Decembre']
	];
	
	private $sqlserver;
	private $sqldatabase;
	private $sqluser;
	private $sqlpasswd;
	private $modele;
	
	public function __construct($server,$database,$user,$passwd) {
		if ($server == null) {
			$this->sqlserver = "127.0.0.1";
		} else {
			$this->sqlserver = $server;
		}
		if ($database == null) {
			$this->sqldatabase = "PPE";
		} else {
			$this->sqldatabase = $database;
		}
		if ($user == null) {
			$this->sqluser = "julien";
		} else {
			$this->sqluser = $user;
		}
		if ($passwd == null) {
			$this->sqlpasswd = "ctrl980";
		} else {
			$this->sqlpasswd = $passwd;
		}
		$this->modele = new Modele($this->sqlserver,$this->sqldatabase,$this->sqluser,$this->sqlpasswd);
		if ($this->modele == null) {
			return null;
		}
	}
	
	public function existlecon($id) {
		$result = $this->modele->getfromtable("*","LECON","IDL = '".$id."'");
		if ($result['nb'] > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function existexam($id) {
		$result = $this->modele->getfromtable("*","EXAM","IDEXAM = '".$id."'");
		if ($result['nb'] > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function listoflecons() {
		$result = $this->modele->getfromtable("IDL as id,NOML as nom,TARIFHEUREL as tarif","LECON","1 = 1");
		return $result['result'];
	}
	
	public function listofexams() {
		$result = $this->modele->getfromtable("IDEXAM as id,TYPEEXAM as nom,PRIXEXAM as prix","EXAM","1 = 1");
		return $result['result'];
	}
	
	public function listcomptes() {
		$rep = $this->modele->getfromtable("IDCOMPTE as id, PRENOMCOMPTE as prenom, NOMCOMPTE as nom, STATUSCOMPTE as status, PERMSCLIENT as perms","COMPTES","1 = 1 ORDER BY prenom");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		return $rep['result'];
	}
	
	public function createclient($prenom,$nom,$passwd,$type,$type_vars,$addr,$dateN,$numtel,$facturation,$mail) {
		// créer le compte
		$rep = $this->createcompte($prenom,$nom,$type,"user",$passwd,$mail);
		if (gettype($rep) == "string") {
			if ($rep == "INSERT_ERROR") {
				return "COMPTE_INSERT_ERROR";
			} else {
				return $rep;
			}
		}
		// récupère l'id du compte qui vient d'être créé
		$idcompte = $this->modele->getfromtable("IDCOMPTE as id","COMPTES","PRENOMCOMPTE = '".$prenom."' and NOMCOMPTE = '".$nom."'");
		if ($idcompte == false) {
			return "COMPTE_SELECT_ERROR";
		}
		$idcompte = (int)$idcompte["result"][0]["id"];
		
		//créer le client associé au compte venant d'être créé
		$rep = $this->modele->insertinto("CLIENT",["NULL",$idcompte,$addr,$dateN,$numtel,date("Y")."-".date("m")."-".date("d"),$facturation,0]);
		if ($rep == false) {
			return "CLIENT_INSERT_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		
		//récupère l'id du client qui vient d'être créé
		$idclient = $this->modele->getfromtable("CL.IDCLIENT as id","CLIENT CL,COMPTES CO","CO.IDCOMPTE = CL.IDCOMPTE and CO.PRENOMCOMPTE = '".$prenom."' and CO.NOMCOMPTE = '".$nom."'");
		if ($idclient == false) {
			return "CLIENT_SELECT_ERROR";
		}
		$idclient = (int)$idclient["result"][0]["id"];
		
		if ($type == "etudiant") {
			//insert la ligne dans 'ETUDIANT' associé à ce client
			$rep = $this->modele->insertinto("ETUDIANT",[$idclient,$type_vars['niveau'],$type_vars['reduc'],"NULL","NULL","NULL","NULL","NULL"]);
			if ($rep == false) {
				return "ETUDIANT_INSERT_ERROR";
			} else if (gettype($rep) == "string") {
				return $rep;
			}
		} else if ($type == "salarie"){
			//insert la ligne dans 'SALARIE' associé à ce client
			$rep = $this->modele->insertinto("SALARIE",[$idclient,$type_vars['entreprise'],"NULL","NULL","NULL","NULL","NULL"]);
			if ($rep == false) {
				return "SALARIE_INSERT_ERROR";
			} else if (gettype($rep) == "string") {
				return $rep;
			}
		}
		return true;
	}
	
	public function createmoniteur($prenom,$nom,$passwd) {
		$rep = $this->createcompte($prenom,$nom,"moniteur","admin",$passwd);
		if (gettype($rep) == "string") {
			if ($rep == "INSERT_ERROR") {
				return "COMPTE_INSERT_ERROR";
			} else {
				return $rep;
			}
		}
		$idcompte = $this->modele->getfromtable("IDCOMPTE as id","COMPTES","PRENOMCOMPTE = '".$prenom."' and NOMCOMPTE = '".$nom."'");
		if ($idcompte == false) {
			return "COMPTE_SELECT_ERROR";
		}
		$idcompte = (int)$idcompte["result"][0]["id"];
		
		$rep = $this->modele->insertinto("MONITEUR",["NULL",$idcompte,2,$prenom." ".$nom,date("Y")."-".date("m")."-".date("d")]);
		if ($rep == false) {
			return "MONITEUR_INSERT_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function createcompte($prenom,$nom,$status,$perm,$passwd,$mail) {
		$prenom = removeForbidChar($prenom);
		$nom = removeForbidChar($nom);
		
		$rep = $this->modele->insertinto("COMPTES",["NULL",$status,$prenom,$nom,$perm,sha1($passwd),0,$mail]);
		if ($rep == false) {
			return "INSERT_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function changePasswd($passwd) {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->update("COMPTES",["PASSWDCOMPTE" => sha1($passwd)],"IDCOMPTE = ".$idcompte);
		if ($rep == false) {
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function connectuser($prenom,$nom,$passwd) {
		$out = $this->modele->callProcedure("connectClient",[$prenom,$nom,sha1($passwd)]);
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out['rep'] == "NOT_EXIST") {
			return false;
		} else if ($out['rep'] == 'BANNED') {
			return "Vous êtes BANNIS";
		} else if ($out['rep'] == "OK") {
			return ["id" => $out['id'], 
					"perm" => $out['perm'],
					"prenom" => $out['prenom'],
					"nom" => $out['nom'],
					"type" => $out['type']];
		}
	}
	
	public function valid_rendezvous($id,$idcompte,$type) {
		if ($type == "Exam") {
			$out = $this->modele->callProcedure("validExam",[(int)$id,(int)$idcompte]);
		} else if ($type == "Planning") {
			$out = $this->modele->callProcedure("validPlanning",[(int)$id,(int)$idcompte]);
		}
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		return true;
		
	}
	
	public function devalid_rendezvous($id,$type) {
		if ($type == "Exam") {
			$out = $this->modele->callProcedure("deValidExam",[(int)$id,(int)$_SESSION['id'],$_SESSION['perm']]);
		} else if ($type == "Planning") {
			$out = $this->modele->callProcedure("deValidPlanning",[(int)$id,(int)$_SESSION['id'],$_SESSION['perm']]);
		}
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		return true;
		
	}
	
	public function delete_planning($id,$idcompte) {
		$out = $this->modele->callProcedure("deletePlanning",[(int)$id,(int)$_SESSION['id'],$_SESSION['perm']]);
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		return true;
	}
	
	public function delete_exam($id,$idcompte) {
		$out = $this->modele->callProcedure("deleteExam",[(int)$id,(int)$_SESSION['id'],$_SESSION['perm']]);
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		return true;
	}
	
	public function add_planning($date,$heureD,$heureF,$lecon,$idcompte) {
		$out = $this->modele->callProcedure("addPlanning",[$date,$heureD,$heureF,(int)$lecon,(int)$idcompte]);
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		
		return (int)$out['idPlanning'];
	}
	
	public function add_exam($date,$heure,$typeexam,$idcompte) {
		$out = $this->modele->callProcedure("addExam",[$date,$heure,(int)$typeexam,(int)$idcompte]);
		if ($out == false) {
			return "SQL_PROCEDURE_ERROR";
		} else if (gettype($out) == "string") {
			return $out;
		}
		if ($out["rep"] != "OK") {
			return $out["rep"];
		}
		
		return (int)$out['idExamPermis'];
	}
	
	public function getyear($ans) {
		/*
		1 = lundi
		2 = mardi
		3 = mercredi
		4 = jeudi
		5 = vendredi
		6 = samedi
		0 = dimanche
		*/
		$listrdv = array();
		$mois = 1;
		$daynb = 0;
		if ($ans >= 2017) {
			$firstday = 0+($ans-2017);
			$firstday += floor(($ans-2017)/4);
		} else {
			$firstday = 7-(2017-$ans);
			$firstday -= floor((2020-$ans)/4);
		}
		//$firstday = $firstday % 7;
		$firstday = -1 * ((floor($firstday/7))*7-($firstday)); // equation pour retourner le modulo positif en cas de dividende negatif
		$yeartab = "";
		while ($mois <= 12) {
			if ($mois == 1) {
				$yeartab .= "<table class='calendar' border='1'>";
			}
			if ($mois-1 % 6 == 0) {
				$yeartab .= "\n<tr>";
			}
			$yeartab .= "\n<td style='text-align: center; vertical-align :top;'>";
			$rep = $this->getmonth($ans,$mois,$daynb,$firstday,"year");
			$yeartab .= $rep['html'];
			$daynb = $rep['daynb'];
			$yeartab .= "</td>";
			if ($mois % 6 == 0) {
				$yeartab .= "\n</tr>";
			}
			$mois += 1;
		}
		$yeartab .= "</table>";
		return $yeartab;
	}
	
	public function getmonth($ans,$mois,$daynb,$firstday,$typecalendar) {
		/*
		1 = lundi
		2 = mardi
		3 = mercredi
		4 = jeudi
		5 = vendredi
		6 = samedi
		0 = dimanche
		*/
		$monthtab = strtoupper($this->mois_infos[complete($mois,2)]['name']);
		$monthtab .= "\n<table>";
		$monthtab .= "\n<tr><td style='text-align: center;'><font color='grey' size='2'>Lu</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Ma</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Me</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Je</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Ve</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Sa</font></td>
						    \n<td style='text-align: center;'><font color='grey' size='2'>Di</font></td></tr>";
		$jour = 1;
		$j = 1;
		while ($jour <= $this->mois_infos[complete($mois,2)]['nbj']) {
			$daynb += 1;
			if ($j-1 % 7 == 0) {
				echo ("<tr>");
			}
			if ($jour == 1 & (($firstday+$daynb)-1)%7 != 1)  {
				if (((($firstday+$daynb)-1)%7) != 0) {
					$i = ((($firstday+$daynb)-1)%7)-1;
				} else {
					$i = 6;
				}
				while($i>0)
				{
					$monthtab .= "<td style='opacity: 0.3;text-align: center;";
					if ($ans.(complete($mois,2)).(complete($jour,2)) < date("Y").(complete(date("m"),2)).(complete(date("d"),2))) {
						$monthtab .= "background-color:#CECDCD;";
					}
					$monthtab .= "'><font color='red'>".($this->mois_infos[complete(lastmonth($ans,$mois)[1],2)]['nbj']-$i+1)."</font></td>";
						
					$j += 1;
					$i -= 1;
				}
			}
			$monthtab .= "<td";
			if ($ans.(complete($mois,2)).(complete($jour,2)) < date("Y").(complete(date("m"),2)).(complete(date("d"),2))) {
				$monthtab .= " class='past tabtd'>";
				$monthtab .= "<font style='color:red;'>" . $jour . "</font>";
			} else {
				$monthtab .= " style='";
				if ($_SESSION['perm'] == "user") {
					$monthtab .= $this->calendar_user($ans,$mois,$jour,$typecalendar);
				} else if ($_SESSION['perm'] == "admin") {
					$monthtab .= $this->calendar_admin($ans,$mois,$jour,$typecalendar);
				} else if ($_SESSION['perm'] == "superadmin") {
					$monthtab .= $this->calendar_superadmin($ans,$mois,$jour,$typecalendar);
				}
			}
			$monthtab .= "</td>";
			if ($j % 7 == 0 | ($jour == $this->mois_infos[complete($mois,2)]['nbj'] & ($ans%4 != 0 | $mois != 2))) {
				$monthtab .= "</tr>";
			}
			
			$jour += 1;
			$j += 1;
			
			if ($jour == 29 & $mois == 2 & $ans%4 == 0) {
				$daynb += 1;
				if ($j-1 % 7 == 0) {
					$monthtab .= "<tr>";
				}
				$monthtab .= "<td";
				if ($ans.(complete($mois,2)).(complete($jour,2)) < date("Y").(complete(date("m"),2)).(complete(date("d"),2))) {
					$monthtab .= " class='past tabtd'>";
					$monthtab .= "<font color='red'>" . $jour . "</font>";
				} else {
					$monthtab .= " style='";
					if ($_SESSION['perm'] == "user") {
						$monthtab .= $this->calendar_user($ans,$mois,$jour,$typecalendar);
					} else if ($_SESSION['perm'] == "admin") {
						$monthtab .= $this->calendar_admin($ans,$mois,$jour,$typecalendar);
					} else if ($_SESSION['perm'] == "superadmin") {
						$monthtab .= $this->calendar_superadmin($ans,$mois,$jour,$typecalendar);
					}
				}
				$monthtab .= "</td>";
				$monthtab .= "</tr>";							
			}
		}
		$monthtab .= "</table>";
		return ['html' => $monthtab, 'daynb' => $daynb];
			
	}
	
	public function calendar_user($ans,$mois,$jour,$typecalendar) {
		$code = "";
		$js = "";
		$type = "rien";
		// vérifie si le jour est occupé par un autre planning
		$result = $this->modele->getfromtable('*,M.NOMMONITEUR as moniteur', 
											  'PLANNING P,CLIENT CL, MONITEUR M',
											  'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE != "'.$_SESSION['id'].'" and 
											   P.IDMONITEUR = M.IDMONITEUR and 
											   P.VALIDATED = 1 and date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "occuped";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{type: 'occuped', debut: '".explode(" ",$rdv['DHDEBUTP'])[1]."', fin: '".explode(" ",$rdv['DHFINP'])[1]."', moniteur: '".$rdv['moniteur']."'});";
			}
		}
		
		
		// vérifie si le jour est occupé par un autre permis exam
		$result = $this->modele->getfromtable('*,M.NOMMONITEUR as moniteur', 
											  'EXAMPERMIS EP,CLIENT CL, MONITEUR M',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE != "'.$_SESSION['id'].'" and
											   EP.IDMONITEUR = M.IDMONITEUR and
											   EP.VALIDATED = 1 and EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "occuped";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{type: 'occuped', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."', moniteur: '".$rdv['moniteur']."'});";
			}
		}
		
		
		// vérifie si il ya un planning en attente
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix, L.NOML as nom', 
											  'PLANNING P,LECON L,CLIENT CL',
											  'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = "'.$_SESSION['id'].'" and 
											   P.IDL = L.IDL and P.VALIDATED = 0 and 
											   date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDPLANNING'].", validated: 'no', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."',
					Lecon: '".$rdv['nom']."', prix: '".$rdv['prix']."'});";
			}
		}
		
		// vérifie si ya un permis exam en attente
		$result = $this->modele->getfromtable('*,E.TYPEEXAM as type,E.PRIXEXAM as prix', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = "'.$_SESSION['id'].'" and 
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 0 and 
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'no', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['type']."', prix: '".$rdv['prix']."'});";
			}
		}
		
		// Vérifie si il ya un planning validé
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix, L.NOML as nom', 
									'PLANNING P,LECON L,MONITEUR M,CLIENT CL',
									'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = "'.$_SESSION['id'].'" and 
									 P.IDMONITEUR = M.IDMONITEUR and P.IDL = L.IDL and P.VALIDATED = 1 and 
									 date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDPLANNING'].", validated: 'yes', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."', Moniteur: '".$rdv['NOMMONITEUR']."',
					Lecon: '".$rdv['nom']."', prix: '".$rdv['prix']."'});";
			}
		}
		
		//vérifie si ya un permis validé
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL,MONITEUR M',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = "'.$_SESSION['id'].'" and 
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 1 and EP.IDMONITEUR = M.IDMONITEUR and
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$js.= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'yes', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['TYPEEXAM']."', prix: '".$rdv['PRIXEXAM']."', Moniteur: '".$rdv['NOMMONITEUR']."'});";
			}
		}
		if ($type == "validated") {
			$code .= "background-color:green;";
		} else if ($type == "waiting") {
			$code .= "background-color:orange;";
		} else if ($type == "occuped") {
			$code .= "background-color:brown;";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "background-color:grey;";
		}
		$code .= "' class='tabtd'";
		$code .= " id='".$ans."-".complete($mois,2)."-".complete($jour,2)."_".$typecalendar."'";
		$code .= "'>";
		if ($type == "rien") {
			$code .= "<font style='color:red;'>" . $jour . "</font>";
		} else if ($type == "occuped") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:back;'>" . $jour . "</font></a>";
		} else if ($type == "waiting") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($type == "validated") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "<font color='red'>" . $jour . "</font>";
		}
		if (strlen($js) > 0) {
			$code .= "<script>".$js."</script>";
		}
		return $code;
	}
	

	public function calendar_admin($ans,$mois,$jour,$typecalendar) {
		$code = "";
		$js = "";
		$type = "rien";
		// vérifie si le jour est occupé par un autre planning
		$result = $this->modele->getfromtable('*', 
											  'PLANNING P,MONITEUR M',
											  'P.IDMONITEUR = M.IDMONITEUR AND M.IDCOMPTE != "'.$_SESSION['id'].'" and 
											   P.VALIDATED = 1 and date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "occuped";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{type: 'occuped', debut: '".explode(" ",$rdv['DHDEBUTP'])[1]."', fin: '".explode(" ",$rdv['DHFINP'])[1]."'});";
			}
		}
		
		
		// vérifie si le jour est occupé par un autre permis exam
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,MONITEUR M',
											  'EP.IDMONITEUR = M.IDMONITEUR AND M.IDCOMPTE != "'.$_SESSION['id'].'" and 
											   EP.VALIDATED = 1 and EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "occuped";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{type: 'occuped', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."'});";
			}
		}
		
		
		// vérifie si il ya un planning en attente
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix', 
											  'PLANNING P,LECON L,CLIENT CL, COMPTES CO',
											  'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and
											   P.IDL = L.IDL and P.VALIDATED = 0 and 
											   date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDPLANNING'].", validated: 'no', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."',
					Lecon: '".$rdv['NOML']."', prix: '".$rdv['prix']."', prenom: '".$rdv['PRENOMCOMPTE']."', nom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		
		// vérifie si ya un permis exam en attente
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL,COMPTES CO',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 0 and 
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'no', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['TYPEEXAM']."', prix: '".$rdv['PRIXEXAM']."', prenom: '".$rdv['PRENOMCOMPTE']."', nom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		
		// Vérifie si il ya un planning validé
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix', 
												'PLANNING P,LECON L,MONITEUR M,CLIENT CL,COMPTES CO',
												'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and 
												 P.IDMONITEUR = M.IDMONITEUR and M.IDCOMPTE = "'.$_SESSION['id'].'" and P.IDL = L.IDL and P.VALIDATED = 1 and 
												 date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDPLANNING'].", validated: 'yes', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."',
					Lecon: '".$rdv['NOML']."', prix: '".$rdv['prix']."', prenom: '".$rdv['PRENOMCOMPTE']."', nom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		
		//vérifie si ya un permis validé
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL,COMPTES CO,MONITEUR M',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and EP.IDMONITEUR = M.IDMONITEUR and M.IDCOMPTE = "'.$_SESSION['id'].'" and
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 1 and 
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$js.= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'yes', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['TYPEEXAM']."', prix: '".$rdv['PRIXEXAM']."', prenom: '".$rdv['PRENOMCOMPTE']."', nom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		if ($type == "validated") {
			$code .= "background-color:green;";
		} else if ($type == "waiting") {
			$code .= "background-color:orange;";
		} else if ($type == "occuped") {
			$code .= "background-color:brown;";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "background-color:grey;";
		}
		$code .= "' class='tabtd'";
		$code .= " id='".$ans."-".complete($mois,2)."-".complete($jour,2)."_".$typecalendar."'";
		$code .= "'>";
		if ($type == "rien") {
			$code .= "<font style='color:red;'>" . $jour . "</font>";
		} else if ($type == "occuped") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:back;'>" . $jour . "</font></a>";
		} else if ($type == "waiting") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($type == "validated") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "<font color='red'>" . $jour . "</font>";
		}
		if (strlen($js) > 0) {
			$code .= "<script>".$js."</script>";
		}
		return $code;
	}
	
	public function calendar_superadmin($ans,$mois,$jour,$typecalendar) {
		$code = "";
		$js = "";
		$type = "rien";
		
		// vérifie si il ya un planning en attente
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix', 
											  'PLANNING P,LECON L,CLIENT CL, COMPTES CO',
											  'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and
											   P.IDL = L.IDL and P.VALIDATED = 0 and 
											   date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDPLANNING'].", validated: 'no', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."',
					Lecon: '".$rdv['NOML']."', prix: '".$rdv['prix']."', clientprenom: '".$rdv['PRENOMCOMPTE']."', clientnom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		
		// vérifie si ya un permis exam en attente
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL,COMPTES CO',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 0 and 
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "waiting";
			foreach($result['result'] as $rdv) {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push(
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'no', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['TYPEEXAM']."', prix: '".$rdv['PRIXEXAM']."', clientprenom: '".$rdv['PRENOMCOMPTE']."', clientnom: '".$rdv['NOMCOMPTE']."'});";
			}
		}
		
		// Vérifie si il ya un planning validé
		$result = $this->modele->getfromtable('*,ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix', 
												'PLANNING P,LECON L,CLIENT CL,COMPTES CO',
												'P.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and 
												 P.IDL = L.IDL and P.VALIDATED = 1 and 
												 date(P.DHDEBUTP) = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$prenomNomMoniteur = $result = $this->modele->getfromtable('CO.PRENOMCOMPTE as prenom,CO.NOMCOMPTE as nom', 
																		   'PLANNING P,MONITEUR M,COMPTES CO',
																		   'P.IDMONITEUR = M.IDMONITEUR AND M.IDCOMPTE = CO.IDCOMPTE and
																		    P.IDPLANNING = '.$rdv['IDPLANNING']);
				
				$prenomNomMoniteur = $prenomNomMoniteur['result'][0];

				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDPLANNING'].", validated: 'yes', type: 'Planning', debut: '".$rdv['DHDEBUTP']."', fin: '".$rdv['DHFINP']."',
					Lecon: '".$rdv['NOML']."', prix: '".$rdv['prix']."', clientprenom: '".$rdv['PRENOMCOMPTE']."', clientnom: '".$rdv['NOMCOMPTE']."',
					moniteurprenom: '".$prenomNomMoniteur['prenom']."', moniteurnom: '".$prenomNomMoniteur['nom']."'});";
			}
		}
		
		//vérifie si ya un permis validé
		$result = $this->modele->getfromtable('*', 
											  'EXAMPERMIS EP,EXAM E,CLIENT CL,COMPTES CO',
											  'EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE and
											   EP.IDEXAM = E.IDEXAM and EP.VALIDATED = 1 and 
											   EP.DATEP = "'.$ans.'-'.complete($mois,2).'-'.complete($jour,2).'"');
		if ($result["nb"] > 0) {
			if ($type == "rien") {
				$js .= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'] = [];";
			}
			$type = "validated";
			foreach($result['result'] as $rdv) {
				$prenomNomMoniteur = $result = $this->modele->getfromtable('CO.PRENOMCOMPTE as prenom,CO.NOMCOMPTE as nom', 
																		   'EXAMPERMIS EP,MONITEUR M,COMPTES CO',
																		   'EP.IDMONITEUR = M.IDMONITEUR AND M.IDCOMPTE = CO.IDCOMPTE and
																		    EP.IDEXAMPERMIS = '.$rdv['IDEXAMPERMIS']);
				
				$prenomNomMoniteur = $prenomNomMoniteur['result'][0];
				
				$js.= "rendez_vous['".$ans."-".complete($mois,2)."-".complete($jour,2)."'].push( 
					{id: ".$rdv['IDEXAMPERMIS'].", validated: 'yes', type: 'Exam', debut: '".$rdv['HEUREDEBUTP']."', fin: '".$rdv['HEUREFINP']."',
					typeexam: '".$rdv['TYPEEXAM']."', prix: '".$rdv['PRIXEXAM']."', clientprenom: '".$rdv['PRENOMCOMPTE']."', clientnom: '".$rdv['NOMCOMPTE']."',
					moniteurprenom: '".$prenomNomMoniteur['prenom']."', moniteurnom: '".$prenomNomMoniteur['nom']."'});";
			}
		}
		if ($type == "validated") {
			$code .= "background-color:green;";
		} else if ($type == "waiting") {
			$code .= "background-color:orange;";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "background-color:grey;";
		}
		$code .= "' class='tabtd'";
		$code .= " id='".$ans."-".complete($mois,2)."-".complete($jour,2)."_".$typecalendar."'";
		$code .= "'>";
		if ($type == "rien") {
			$code .= "<font style='color:red;'>" . $jour . "</font>";
		} else if ($type == "waiting") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($type == "validated") {
			$code .= "<a href='javascript:display_rendez_vous(\"".$ans."-".complete($mois,2)."-".complete($jour,2)."\");' 
			         title='Voir le rendez vous du ".complete($jour,2)."/".complete($mois,2)."/".$ans."'><font style='color:red;'>" . $jour . "</font></a>";
		} else if ($ans == date("Y") & $mois == date("m") & $jour == date("d")) {
			$code .= "<font color='red'>" . $jour . "</font>";
		}
		if (strlen($js) > 0) {
			$code .= "<script>".$js."</script>";
		}
		return $code;
	}
	
	public function examavec() {
		if ($_SESSION['perm'] == "admin") {
			$idcompte = $_SESSION['id'];
			$idmoniteur = $this->modele->getfromtable("IDMONITEUR as id","IDMONITEUR_BY_COMPTE","IDCOMPTE = '".$idcompte."'"); // recupere l'id moniteur associé au compte connecté
			if ($idmoniteur == false) {
				return "<font size='4' color='red'>SELECT_ERROR</font>";
			}
			$idmoniteur = (int)$idmoniteur["result"][0]["id"];
		}
		
		$listexam = $this->modele->getfromtable("IDEXAM as id, TYPEEXAM","EXAM","IDEXAM != 1");
		
		if ($listexam == false) {
			return "<font size='4' color='red'>SELECT_ERROR</font>";
		}
		$html = "<br><br>";
		foreach($listexam['result'] as $exam) {
			if ($_SESSION['perm'] == "admin") {
				$html .= "<strong>Client à qui vous faite passer le ".$exam['TYPEEXAM']." : </strong><br>";
				$listclient = $this->modele->getfromtable("IDEXAMPERMIS as id, PRENOMCOMPTE as prenom, NOMCOMPTE as nom, RESULTATP as resultat","CLIENTEXAM",
														  "IDMONITEUR = ".$idmoniteur." and IDEXAM = ".$exam['id']);
			} else {
				$html .= "<strong>Client qui passe le ".$exam['TYPEEXAM']." : </strong><br>";
				$listclient = $this->modele->getfromtable("IDEXAMPERMIS as id, PRENOMCOMPTE as prenom, NOMCOMPTE as nom, RESULTATP as resultat","CLIENTEXAM",
														  "IDEXAM = ".$exam['id']);
			}
			if ($listclient == false) {
				return "<font size='4' color='red'>SELECT_ERROR</font>";
			}
			if ($listclient['nb'] == 0) {
				$html .= "<font size='4' color='orange'>Personne</font><br><br>";
			} else {
				$html .= "<br><table>";
				$html .= "<tr><td class='tabtd'><strong>Clients</strong></td><td class='tabtd'><strong>Resultats</strong></td><td class='tabtd'><strong>Votre note</strong></td></tr>";
				foreach ($listclient['result'] as $client) {
					$html .= "<tr>";
					$html .= "<td class='tabtd'>".$client['prenom']." ".$client['nom']."</td>";
					if ($client['resultat'] == null) {
						$html .= "<td id='note_".$client['id']."' class='tabtd'>non noté</td>";
					} else {
						$html .= "<td id='note_".$client['id']."' class='tabtd'>".$client['resultat']."/20</td>";
					}
					$html .= "<td class='tabtd'><input type='text' style='width: 100px;' placeholder='(0-20)' id='getnote_".$client['id']."'><input type='button' value='Valider' onclick='setnote(".$client['id'].")'><br>";
					$html .= "<div id='msg_setnote_".$client['id']."'></div></td>";
					$html .= "</tr>";
				}
				$html .= "</table><br><br>";
			}
		}
		return $html;
	}
	
	public function setnote($id,$note) {
		if ($_SESSION['perm'] == "admin") {
			$idcompte = $_SESSION['id'];
			$idmoniteur = $this->modele->getfromtable("IDMONITEUR as id","IDMONITEUR_BY_COMPTE","IDCOMPTE = '".$idcompte."'"); // recupere l'id moniteur associé au compte connecté
			if ($idmoniteur == false) {
				return "SELECT_ERROR";
			}
			$idmoniteur = (int)$idmoniteur["result"][0]["id"];
		
			$rep = $this->modele->getfromtable("*","CLIENTEXAM","IDMONITEUR = ".$idmoniteur." and IDEXAMPERMIS = ".$id);
		} else {
			$rep = $this->modele->getfromtable("*","CLIENTEXAM","IDEXAMPERMIS = ".$id);
		}
		
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce client ne passe pas ce permis ou pas avec vous";
		}
		if ($note == "") {
			$note = "NULL";
		} else {
			$note = (int)$note;
		}
		$rep = $this->modele->update("EXAMPERMIS",["RESULTATP" => $note],"IDEXAMPERMIS = ".$id);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		return true;
	}
	
	public function displayExam() {
		$idclient = $this->modele->getfromtable("IDCLIENT as id","IDCLIENT_BY_COMPTE","IDCOMPTE = '".$_SESSION['id']."'"); // recupere l'id client associé au compte connecté
		if ($idclient == false) {
			return "SELECT_ERROR";
		}
		$idclient = (int)$idclient["result"][0]["id"];
		
		//recupere la liste des exams
		$listexam = $this->modele->getfromtable("IDEXAM as id,TYPEEXAM","EXAM","IDEXAM != 1");
		if ($listexam == false) {
			return "SELECT_ERROR";
		}
		$html = "";
		foreach ($listexam['result'] as $exam) { // pour chaque type d'exam, affiche la note qu'a ce client sur ce dernier
			$html .= "Votre note au ".$exam['TYPEEXAM']." : ";
			$note = $this->modele->getfromtable("RESULTATP","EXAMPERMIS","IDEXAM = ".$exam['id']." and IDCLIENT = ".$idclient);
			if ($note == false) {
				return "SELECT_ERROR";
			}
			if ($note['nb'] == 0) {
				$html .= "<strong>Vous n'êtes pas encore noté</strong>";
			} else if ($note['result'][0]["RESULTATP"] == NULL) {
				$html .= "<strong>Vous n'êtes pas encore noté</strong>";
			} else {
				$html .= "<strong>".$note['result'][0]["RESULTATP"]."/20</strong>";
			}
			$html .= "<br>";
		}
		return $html;
	}
	
	public function codeavec() {
		if ($_SESSION['perm'] == "admin") {
			$idcompte = $_SESSION['id'];
			$idmoniteur = $this->modele->getfromtable("IDMONITEUR as id","IDMONITEUR_BY_COMPTE","IDCOMPTE = '".$idcompte."'"); // recupere l'id moniteur associé au compte connecté
			if ($idmoniteur == false) {
				return "<font size='4' color='red'>SELECT_ERROR</font>";
			}
			$idmoniteur = (int)$idmoniteur["result"][0]["id"];
		
			$list = $this->modele->getfromtable("CL.CODE as code,CA.IDCLIENT as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom","CODEAVEC CA, CLIENT CL, COMPTES CO",
												"CA.IDMONITEUR = ".$idmoniteur." and CA.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE");
		} else if ($_SESSION['perm'] == "superadmin") {
			$list = $this->modele->getfromtable("CL.CODE as code,CL.IDCLIENT as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom","CLIENT CL, COMPTES CO",
												"CL.IDCOMPTE = CO.IDCOMPTE");
		}
		if ($list == false) {
			return "<font size='4' color='red'>SELECT_ERROR</font>";
		}
		if ($list['nb'] == 0) {
			return "<font size='4' color='orange'>Personne</font>";
		}
		$html = "";
		$html .= "<table>";
		foreach($list['result'] as $client) {
			$html .= "<tr>";
			$html .= "<td>";
			$html .= $client['prenom']." ".$client['nom']." ";
			$html .= "<span id='stringcode_".$client['id']."'>";
			if ($client['code'] == 0) {
				$html .= "(n'a pas le code)";
			} else if ($client['code'] == 1) {
				$html .= "(a le code)";
			}
			$html .= "</span>";
			$html .= "</td>";
			$html .= "<td>";
			if ($client['code'] == 0) {
				$html .= "<input id='button_code_".$client['id']."' type='button' value='Attribuer' onclick='givecode(".$client['id'].")'>";
			} else if ($client['code'] == 1) {
				$html .= "<input id='button_code_".$client['id']."' type='button' value='Dé-attribuer' onclick='removecode(".$client['id'].")'>";
			}
			$html .= "<br><div id='error_code_".$client['id']."'></div>";
			$html .= "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}
	
	public function altercode($idclient,$code) {
		if ($_SESSION['perm'] == "admin") {
			$idcompte = $_SESSION['id'];
			$idmoniteur = $this->modele->getfromtable("IDMONITEUR as id","IDMONITEUR_BY_COMPTE","IDCOMPTE = '".$idcompte."'"); // recupere l'id moniteur associé au compte connecté
			if ($idmoniteur == false) {
				return "SELECT_ERROR";
			}
			$idmoniteur = (int)$idmoniteur["result"][0]["id"];
		
			$rep = $this->modele->getfromtable("*","CODEAVEC","IDMONITEUR = ".$idmoniteur." and IDCLIENT = ".$idclient);
		} else if ($_SESSION['perm'] == "superadmin") {
			$rep = $this->modele->getfromtable("*","CLIENT","IDCLIENT = ".$idclient);
		}
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce client n'existe pas ou vous ne lui faite pas passer le code";
		}
		
		$rep = $this->modele->update("CLIENT",['CODE' => (int)$code],"IDCLIENT = ".$idclient);
		if ($rep == false) {
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function aLeCode() {
		$rep = $this->modele->getfromtable("CODE","CLIENT","IDCOMPTE = ".$_SESSION['id']);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['result'][0]['CODE'] == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	public function listusers($view) {
		if ($_SESSION['perm'] != "superadmin") {
			return false;
		}
		$list = $this->modele->getfromtable("*",$view,"1 = 1");
		if ($list == false) {
			return "<font size='4' color='red'>SELECT_ERROR</font>";
		}
		$listSansCour = $this->modele->getfromtable("*",$view."SANSCOUR","1 = 1");
		if ($listSansCour == false) {
			return "<font size='4' color='red'>SELECT_ERROR</font>";
		}
		foreach ($listSansCour['result'] as $userSansCour) {
			$sansCour = true;
			foreach ($list['result'] as $user) {
				if ($user['id'] == $userSansCour['id']) {
					$sansCour = false;
				}
			}
			if ($sansCour) {
				$list['result'][] = $userSansCour;
			}
		}
		$js = "";
		$html = "";
		$html .= "<center><table>";
		$i = 1;
		foreach ($list['result'] as $user) {
			$js .= "users['id-".$user['IDCOMPTE']."'] = {prenom: '".$user['prenom']."', nom: '".$user['nom']."', mail: '".$user['mail']."'};";
			if ($view == "COSALARIE" | $view == "COETUDIANT") {
				$js .= "users['id-".$user['IDCOMPTE']."'].addr = '".$user['addr']."';";
				$js .= "users['id-".$user['IDCOMPTE']."'].dateN = '".$user['dateN']."';";
				$js .= "users['id-".$user['IDCOMPTE']."'].numtel = '".$user['numtel']."';";
				$js .= "users['id-".$user['IDCOMPTE']."'].dateI = '".$user['dateI']."';";
				$js .= "users['id-".$user['IDCOMPTE']."'].facturation = '".$user['facturation']."';";
				if ($view == "COETUDIANT") {
					$js .= "users['id-".$user['IDCOMPTE']."'].type = 'etudiant';";
					$js .= "users['id-".$user['IDCOMPTE']."'].etude = '".$user['etude']."';";
					$js .= "users['id-".$user['IDCOMPTE']."'].reduction = '".$user['reduction']."';";
				} else if ($view == "COSALARIE") {
					$js .= "users['id-".$user['IDCOMPTE']."'].type = 'salarie';";
					$js .= "users['id-".$user['IDCOMPTE']."'].entreprise = '".$user['entreprise']."';";
				}
			} else if ($view == "COMONITEUR") {
				$js .= "users['id-".$user['IDCOMPTE']."'].type = 'moniteur';";
				$js .= "users['id-".$user['IDCOMPTE']."'].dateE = '".$user['dateE']."';";
			}
			$html .= "<tr>";
			$html .= "<td style='text-align: center;'>";
			$html .= $user['prenom']." ".$user['nom'];
			$html .= "<br/>(".$user['nbCour']." cours)";
			$html .= "<br><input type='button' value='Fiche' onclick='fiche(".$user['IDCOMPTE'].")'>";
			if ($user['banned'] == 0) {
				$html .= "<input id='buttonban_".$user['IDCOMPTE']."' type='button' value='Bannir' onclick='ban(".$user['IDCOMPTE'].")'>";
			} else {
				$html .= "<input id='buttonban_".$user['IDCOMPTE']."' type='button' value='Dé-bannir' onclick='unban(".$user['IDCOMPTE'].")'>";
			}
			$html .= "<script>".$js."</script>";
			if ($i < sizeof($list['result'])) {
				$html .= "<hr>";
			}
			$html .= "</td>";
			$html .= "</tr>";
			$i += 1;
		}
		$html .= "</table></center>";
		
		return $html;
		
	}
	
	public function banUnban($id,$val) {
		$rep = $this->modele->getfromtable("IDCOMPTE","COMPTES","IDCOMPTE = ".$id." and PERMSCLIENT != 'superadmin'");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "NOT_EXIST";
		}
		$rep = $this->modele->update("COMPTES",["BANNED" => (int)$val],"IDCOMPTE = ".$id);
		
		if ($rep == false) {
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		
		return true;
	}
	
	public function displayAvis() {
		$rep = $this->modele->getfromtable("A.IDAVIS as id, A.IDCOMPTE, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, A.DHAVIS, A.CONTENTAVIS",
							  "AVIS A, COMPTES CO","(A.IDCOMPTE = CO.IDCOMPTE) OR (A.IDCOMPTE is null) GROUP BY A.IDAVIS ORDER BY A.IDAVIS desc");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "<font color='orange' size='4'>Aucun avis</font>";
		}
		$anonyme = $this->modele->getfromtable("*","AVIS","IDCOMPTE is null");
		if ($anonyme == false) {
			return "SELECT_ERROR";
		}
		$anonyme = $anonyme['nb'];
		$html = "<table id='commentsTable' style='width: 66vw;'>";
		foreach($rep['result'] as $avis) {
			$html .= "<tr><td style='background-color: #FFF; border-top: 1px #000000 solid; border-left: 1px #000000 solid; border-right: 1px #000000 solid;'>";
			if ($avis['IDCOMPTE'] != NULL) {
				$html .= "Posted by ".$avis['prenom']." ".$avis['nom']." [".$avis['DHAVIS']."] : </td></tr>";
			} else {
				$html .= "Posted by Anonymous N°".$anonyme." [".$avis['DHAVIS']."] : </td></tr>";
				$anonyme -= 1;
			}
			$html .= "<tr><td style='background-color: #FFF; border-bottom: 1px #000000 solid; border-left: 1px #000000 solid; border-right: 1px #000000 solid;'>
			<div style='text-align: left; margin-left: 23vW;'><span id='".$avis['id']."'>".str_replace("\n","<br>",removeForbidChar($avis['CONTENTAVIS']))."</span>";
			if (isset($_SESSION['id'])) {
				if ($avis['IDCOMPTE'] == $_SESSION['id']) {
					$html .= "<br><input type='button' id='buttonModif_".$avis['id']."' value='Modifier' onclick='displayModifForm(".$avis['id'].")'>
					              <input type='button' value='supprimer' onclick='suppr(".$avis['id'].")'><br>
								  <div id='errorModif_".$avis['id']."'><div id='errorSuppr_".$avis['id']."'></div>";
				}
			}
			$html .= "</div></td></tr>";
			$html .= "<tr><td style='height: 25px;'></td></tr>";		
		}
		$html .= "</table>";
		return $html;
	}
	public function sendAvis($content) {
		if (!isset($_SESSION['id'])) {
			$idcompte = "NULL";
		} else {
			$idcompte = $_SESSION['id'];
		}
		$now = date("Y-m-d H:i:s");
		$rep = $this->modele->insertinto("AVIS",["NULL",$now,$idcompte,$content]);
		if ($rep == false) {
			return "INSERT_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function supprAvis($id) {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->getfromtable("*","AVIS","IDAVIS = ".$id." and IDCOMPTE = ".$idcompte);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce message n'existe pas";
		}
		$rep = $this->modele->deletefrom("AVIS","IDAVIS = ".$id);
		if ($rep == false) { 
			return "DELETE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function modifAvis($id, $content) {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->getfromtable("*","AVIS","IDAVIS = ".$id." and IDCOMPTE = ".$idcompte);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce message n'existe pas ou vous n'en n'êtes pas l'auteur";
		}
		
		$rep = $this->modele->update("AVIS",["CONTENTAVIS" => $content],"IDAVIS = ".$id);
		if ($rep == false) { 
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function getOwnFiche() {
		
		if ($_SESSION['perm'] == "superadmin") {
			return "<strong><font size='4' color='black'>Vous êtes directeur</font></strong>";
		}
		
		$view_type = [
		"salarie" => "COSALARIE",
		"etudiant" => "COETUDIANT",
		"moniteur" => "COMONITEUR"
		];
		$typesOrtho = [
		"salarie" => "Salarié(e)",
		"etudiant" => "Etudiant(e)",
		"moniteur" => "Moniteur"
		];
		
		$user = $this->modele->getfromtable("*",$view_type[$_SESSION['type']],"IDCOMPTE = ".$_SESSION['id']);
		
		if ($user == false) {
			return "<font color='red' size='4'>SELECT_ERROR</font>";
		}
		if ($user['nb'] == 0) {

			$user = $this->modele->getfromtable("*",$view_type[$_SESSION['type']]."SANSCOUR","IDCOMPTE = ".$_SESSION['id']);

			if ($user == false) {
				return "<font color='red' size='4'>SELECT_ERROR</font>";
			}

			if ($user['nb'] == 0) {
				return "<font color='red' size='4'>Erreur : votre compte n'est pas trouvé</font>";
			}
		}
		$user = $user['result'][0];
		
		$html = "Vous êtes ".$typesOrtho[$_SESSION['type']]."<br><br>";
		$html .= "Adresse mail : ".$user['mail']."<br>";
		if ($_SESSION['type'] == "salarie" | $_SESSION['type'] == "etudiant") {
			$html .= "Adresse : ".$user['addr']."<br>";
			$html .= "Date de naissance : ".$user['dateN']."<br>";
			$html .= "Numéro de télephone : ".$user['numtel']."<br>";
			$html .= "Date d'inscription : ".$user['dateI']."<br>";
			$html .= "Mode de facturation : ".$user['facturation']."<br>";
			if ($_SESSION['type'] == "etudiant") {
				$html .= "Niveau d'étude : ".$user['etude']."<br>";
				$html .= "Réduction : ".$user['reduction']."<br>";
			} else if ($_SESSION['type'] == "salarie") {
				$html .= "Votre entreprise : ".$user['entreprise']."<br>";
			}
		} else if ($_SESSION['type'] == "moniteur") {
			$html .= "Votre date d'embauche : ".$user['dateE']."<br>";
		}
		return $html;
	}
	
	public function getmp($idcompte) {
		$MPs = $this->modele->getfromtable("MP.IDMP as id, MP.LU as lu, MP.DATEANDTIME as datetime,MP.OBJET,MP.CONTENT, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom","MP, COMPTES CO",
										   "MP.IDCOMPTEDST = ".$idcompte." and MP.IDCOMPTESRC = CO.IDCOMPTE ORDER BY MP.DATEANDTIME desc");
		if ($MPs == false) {
			return "SELECT_ERROR";
		}
		$code = "";
		foreach ($MPs['result'] as $MP) {
			$code .= "MPs.push({id: ".$MP['id'].",datetime: `".$MP['datetime']."`, objet: `".str_replace("\n","",removeForbidChar($MP['OBJET']))."`, 
					  prenom : `".$MP['prenom']."`, nom: `".$MP['nom']."`, lu: ".$MP['lu'].", content: `";
			$code .= "<table>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "De : ".$MP['prenom']." ".$MP['nom'];
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "Object : ".str_replace("\n","",removeForbidChar($MP['OBJET']));
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "<br>Content : <br><br>".str_replace("\n","<br>",removeForbidChar($MP['CONTENT']));
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "</table>";
			$code .= "`});";
		}
		$MSs = $this->modele->getfromtable("MP.IDMP as id,MP.DATEANDTIME as datetime,MP.OBJET,MP.CONTENT, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom","MP, COMPTES CO",
										   "MP.IDCOMPTESRC = ".$idcompte." and MP.IDCOMPTEDST = CO.IDCOMPTE ORDER BY MP.DATEANDTIME desc");
		if ($MSs == false) {
			return "SELECT_ERROR";
		}
		foreach ($MSs['result'] as $MS) {
			$code .= "MSs.push({id: ".$MS['id'].",datetime: '".$MS['datetime']."', objet: `".str_replace("\n","",removeForbidChar($MS['OBJET']))."`, prenom : `".$MS['prenom']."`, nom: `".$MS['nom']."`, content: `";
			$code .= "<table>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "A : ".$MS['prenom']." ".$MS['nom'];
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "Object : ".str_replace("\n","",removeForbidChar($MS['OBJET']));
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "<tr>";
			$code .= "<td>";
			$code .= "<br>Content : <br><br>".str_replace("\n","<br>",removeForbidChar($MS['CONTENT']));
			$code .= "</td>";
			$code .= "</tr>";
			$code .= "</table>";
			$code .= "`});";
		}
		return $code."\n";
	}
	
	public function supprmp($id) {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->getfromtable("*","MP","(IDCOMPTEDST = ".$idcompte." or IDCOMPTESRC = ".$idcompte.") and IDMP = ".$id);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce MP n'existe pas ou il n'est pas pour vous";
		}
		$rep = $this->modele->deletefrom("MP","IDMP = ".$id);
		if ($rep == false) { 
			return "DELETE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
		
	}
	
	public function sendmp($dsts,$objet,$content) {
		$idcompte = $_SESSION['id'];
		$dstsArray = explode(",",$dsts);
		foreach($dstsArray as $dst) {
			$rep = $this->modele->getfromtable("*","COMPTES","IDCOMPTE = ".$dst);
			if ($rep == false) {
				return "SELECT_ERROR";
			}
			if ($rep['nb'] == 0) {
				return "Ce compte n'existe pas";
			}
			$rep = $this->modele->insertinto("MP",["NULL",$idcompte,(int)$dst,date("Y-m-d H:i"),$objet,$content,0]);
			if ($rep == false) { 
				return "INSERT_ERROR";
			} else if (gettype($rep) == "string") {
				return $rep;
			}
		}
		return true;
	}
	
	public function sendmpAsContact($objet,$content,$nom,$prenom,$mail,$numtel) {
		$idcompte = $this->modele->getfromtable("IDCOMPTE as id","COMPTES","STATUSCOMPTE = 'fromContact'");
		if ($idcompte == false) {
			return "SELECT_ERROR";
		}
		$curdate = date("Y-m-d H:i");
		$idcompte = $idcompte['result'][0]['id'];
		
		$finalContent = "From : ".$prenom." ".$nom." [".$mail."] [".$numtel."]\n";
		$finalContent .= "Object : ".$objet."\n\n";
		$finalContent .= $content;
		$finalObjet = "From ".$prenom." ".$nom;
		$listcomptes = $this->listcomptes();
		foreach ($listcomptes as $compte) {
			if ($compte['perms'] == "superadmin") {
				$rep = $this->modele->insertinto("MP",["NULL",$idcompte,$compte['id'],$curdate,$finalObjet,$finalContent,0]);
				if ($rep == false) {
					return "INSERT_ERROR";
				}
			}
		}
		return true;
	}
	
	public function setLu($id) {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->getfromtable("IDMP","MP","IDMP = ".$id." and IDCOMPTEDST = ".$idcompte);
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Ce MP n'existe pas ou vous n'en n'êtes pas le destinataire";
		}
		$rep = $this->modele->update("MP",["LU" => 1],"IDMP = ".$id);
		if ($rep == false) { 
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		return true;
	}
	
	public function nb_mp() {
		$idcompte = $_SESSION['id'];
		$rep = $this->modele->getfromtable("*","MP","IDCOMPTEDST = ".$idcompte." and LU = 0");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		return $rep['nb'];
	}

	public function getQuiz() {
		$questions = $this->modele->getfromtable("*","QUESTIONS","1 = 1");
		if ($questions == false) {
			return "<font size='4' color='red'>SELECT_ERROR</font>";
		}
		$code = "";
		foreach ($questions['result'] as $question) {
			$code .= "\nquiz.push({});";
			$code .= "\nquiz[quiz.length-1].question = {id: ".$question['IDQUESTION'].", question: `".$question['QUESTION']."`};";
			$code .= "\nquiz[quiz.length-1].reponses = [];";
			$reponses = $this->modele->getfromtable("IDREPONSE,REPONSE","REPONSES","IDQUESTION = ".$question['IDQUESTION']);
			if ($reponses == false) {
				return "<font size='4' color='red'>SELECT_ERROR</font>";
			}
			foreach($reponses['result'] as $reponse) {
				$code .= "\nquiz[quiz.length-1].reponses.push({id: ".$reponse['IDREPONSE'].",reponse: `".$reponse['REPONSE']."`});";
			}
		}
		return $code."\n";
	}

	public function verifReponse($idQuestion,$idReponse) {
		if (!isset($_SESSION['currentQuestion'])) {
			return "Variable necessaire inexistante";
		} else if ($_SESSION['currentQuestion'] != $idQuestion) {
			return "Vous devez d'abord répondre à la question précedente";
		}
		$nbQuestions = $this->modele->getfromtable("IDQUESTION","QUESTIONS","1=1");
		if ($nbQuestions == false) {
			return "SELECT_ERROR";
		}

		$nbQuestions = (int)$nbQuestions['nb'];

		$rep = $this->modele->getfromtable("IDREPONSE","REPONSES","IDQUESTION = ".$idQuestion." and IDREPONSE = ".$idReponse." and BONNE = 1");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] > 0) {
			$_SESSION['note'] += 1;
		}
		$_SESSION['currentQuestion'] += 1;
		if ($_SESSION['currentQuestion'] > $nbQuestions) {
			return "finish";
		}
		return "next";
	}
	
	public function forgotPassword($prenom,$nom) {
		// vérifie si il ya bien un compte qui utilise cette adresse mail sur le site
		$rep = $this->modele->getfromtable("IDCOMPTE as id, MAIL as mail","COMPTES","PRENOMCOMPTE = '".$prenom."' and NOMCOMPTE = '".$nom."'");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "Aucun compte sur ce site n'utilise ce prenom et ce nom.";
		}
		
		$rep = $rep['result'][0];
		
		$mailSend = $rep['mail'];
		
		// génere un mot de passe aléatoire
		$password = "";
		$longPassword = 12;
		$chars = ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
				  "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
				  "1","2","3","4","5","6","7","8","9","0",
				  "&","-","_","=","#","@","$","%","*","!",":","/",";",".",",","?"];
		$i = 0;
		while($i < $longPassword) {
			$password .= $chars[rand(0,sizeof($chars)-1)];
			$i += 1;
		}
		
		//attribue ce mot de passe à l'utilisateur
		
		$rep = $this->modele->update("COMPTES",['PASSWDCOMPTE' => sha1($password)],"IDCOMPTE = ".$rep['id']);
		
		if ($rep == false) { 
			return "UPDATE_ERROR";
		} else if (gettype($rep) == "string") {
			return $rep;
		}
		
		//envoie un mail avec ce mot de passe à l'adresse mail de l'utilisateur
		
		include("/var/www/PPE/libs/PHPMailer/src/PHPMailer.php");
		include("/var/www/PPE/libs/PHPMailer/src/SMTP.php");
		
		$mail = array("SMTPOptions" => array('ssl' => 
						array(
							'verify_peer' => false,
							'verify_peer_name' => false,
							'allow_self_signed' => true)));
		
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		
		$mail->isSMTP(); // Paramétrer le Mailer pour utiliser SMTP 
		$mail->Host = 'smtp-mail.outlook.com'; // Spécifier le serveur SMTP
		$mail->SMTPAuth = true; // Activer authentication SMTP
		$mail->Username = 'directeur.castelane@outlook.com'; // Votre adresse email d'envoi
		$mail->Password = '9iu9ALcuX28K'; // Le mot de passe de cette adresse email
		$mail->SMTPSecure = 'tls'; // Accepter SSL
		$mail->Port = 587;

		$mail->setFrom('directeur.castelane@outlook.com', 'Castellane Auto'); // Personnaliser l'envoyeur
		$mail->addAddress($mailSend, $prenom." ".$nom); // Ajouter le destinataire
		$mail->addReplyTo('directeur.castelane@outlook.com', 'Castellane Auto'); // L'adresse de réponse
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		//$mail->addAttachment('/var/tmp/file.tar.gz'); // Ajouter un attachement
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); 
		$mail->isHTML(true); // Paramétrer le format des emails en HTML ou non
	
		$mail->Subject = 'Mot de passe - Castellane AUTO';
		$mail->Body = "<html><head></head><body><b>Bonjour ".$prenom." ".$nom."</b>,<br> Voici votre nouveau mot de passe pour le site Castellane AUTO : <i>".$password."</i></body></html>";
		$mail->AltBody = "Bonjour ".$prenom." ".$nom.", Voici votre nouveau mot de passe pour le site Castellane AUTO : ".$password;
		
		$mail->SMTPDebug = 1;
		
		if(!$mail->send()) {
			error_log('Mailer Error: ' . $mail->ErrorInfo);
			return "Error lors de l'envoie du mail";
		} else {
			error_log("Mail envoyé à : ".$mailSend." avec le mot de passe ".$password);
			return true;
		}
	}

	public function listVehicules() {
		$rep = $this->modele->getfromtable("SUM(R.KMPARCOURUS) as parcourus, V.NB_KM_INITIAL as initial, V.DATE_ACHAT,V.NUM_IMMATRICULATION, M.NOMMODELE,(V.NB_KM_INITIAL+SUM(R.KMPARCOURUS)) as total",
									"ROULER R, VEHICULE V, MODELE M",
									"R.IDVEHICULE = V.IDVEHICULE and V.IDMODELE = M.IDMODELE GROUP BY NUM_IMMATRICULATION ORDER BY total");

		if ($rep == false) {
			return "SELECT_ERROR";
		}
		if ($rep['nb'] == 0) {
			return "<font color='orange' size='4'>Aucun vehicule sur ce site</font>";
		}

		$html = "";
		$html .= "<table>";
		$html .= "<tr><td class='tabtd'>Modèle</td><td class='tabtd'>Immatriculation</td><td class='tabtd'>Date d'achat</td><td class='tabtd'>Km initials</td>    <td class='tabtd'>Km parcourus</td><td class='tabtd'>Total kilomètre</td></tr>";
		foreach ($rep['result'] as $vehicule) {
			if ($vehicule['total'] >= 200000) {
				$html .= "<tr>";
				$html .= "<td class='tabtd'>".$vehicule['NOMMODELE']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['NUM_IMMATRICULATION']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['DATE_ACHAT']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['initial']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['parcourus']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['total']."</td>";
				$html .= "<td class='tabtd'><font color='orange' size='3'>Plus de 200000 km pour ce vehicule</font></td>";
				$html .= "</tr>";
			}
		}
		$html .= "<tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>";
		foreach ($rep['result'] as $vehicule) {
			if ($vehicule['total'] < 200000) {
				$html .= "<tr>";
				$html .= "<td class='tabtd'>".$vehicule['NOMMODELE']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['NUM_IMMATRICULATION']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['DATE_ACHAT']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['initial']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['parcourus']."</td>";
				$html .= "<td class='tabtd'>".$vehicule['total']."</td>";
				$html .= "</tr>";
			}
		}
		$html .= "</table>";
		return $html;
	}

	public function getRendezVousUserAndroid() {

		$rep = $this->modele->getfromtable("P.IDPLANNING as id, date(P.DHDEBUTP) as date, time(P.DHDEBUTP) as heureD, time(P.DHFINP) as heureF, L.NOML as lecon, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, 
											ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix, 'planning' as type, 'unvalidated' as state",
											"PLANNING P, CLIENT CL, COMPTES CO, LECON L",
											"P.IDL = L.IDL and P.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE = ".$_SESSION['id']." and P.VALIDATED = 0");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		
		$PLanningNoValidated = $rep['result'];


		$rep = $this->modele->getfromtable("P.IDPLANNING as id, M.NOMMONITEUR as moniteur, date(P.DHDEBUTP) as date, time(P.DHDEBUTP) as heureD, time(P.DHFINP) as heureF, L.NOML as lecon, CO.PRENOMCOMPTE as prenom,
		 									CO.NOMCOMPTE as nom, ROUND(getPrixLecon(P.DHDEBUTP,P.DHFINP,L.TARIFHEUREL),2) as prix, 'planning' as type, 'validated' as state",
											"PLANNING P, CLIENT CL, COMPTES CO, LECON L, MONITEUR M",
											"P.IDL = L.IDL and P.IDMONITEUR = M.IDMONITEUR and P.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE = ".$_SESSION['id']." and P.VALIDATED = 1");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		$PLanningValidated = $rep['result'];

		$rep = $this->modele->getfromtable("P.IDPLANNING as id, date(P.DHDEBUTP) as date, time(P.DHDEBUTP) as heureD, time(P.DHFINP) as heureF, 'planning' as type, 'occuped' as state",
											"PLANNING P, CLIENT CL, COMPTES CO",
											"P.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE != ".$_SESSION['id']." and P.VALIDATED = 1");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		$PLanningOccuped = $rep['result'];


		$rep = $this->modele->getfromtable("EP.IDEXAMPERMIS as id, EP.DATEP as date, EP.HEUREDEBUTP as heureD, EP.HEUREFINP as heureF, E.TYPEEXAM as exam, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, 
											E.PRIXEXAM as prix, 'exam' as type, 'unvalidated' as state",
											"EXAMPERMIS EP, CLIENT CL, COMPTES CO, EXAM E",
											"EP.IDEXAM = E.IDEXAM and EP.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE = ".$_SESSION['id']." and EP.VALIDATED = 0");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		$ExamPermisNoValidated= $rep['result'];


		$rep = $this->modele->getfromtable("EP.IDEXAMPERMIS as id, M.NOMMONITEUR as moniteur, EP.DATEP as date, EP.HEUREDEBUTP as heureD, EP.HEUREFINP as heureF, E.TYPEEXAM as exam, CO.PRENOMCOMPTE as prenom, 
											CO.NOMCOMPTE as nom, E.PRIXEXAM as prix, 'exam' as type, 'validated' as state",
											"EXAMPERMIS EP, CLIENT CL, COMPTES CO, EXAM E, MONITEUR M",
											"EP.IDEXAM = E.IDEXAM and EP.IDMONITEUR = M.IDMONITEUR and EP.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE = ".$_SESSION['id']." and EP.VALIDATED = 1");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		$ExamPermisValidated= $rep['result'];


		$rep = $this->modele->getfromtable("EP.IDEXAMPERMIS as id, EP.DATEP as date, EP.HEUREDEBUTP as heureD, EP.HEUREFINP as heureF, 'exam' as type, 'occuped' as state",
											"EXAMPERMIS EP, CLIENT CL, COMPTES CO",
											"EP.IDCLIENT = CL.IDCLIENT and CL.IDCOMPTE = CO.IDCOMPTE and CO.IDCOMPTE != ".$_SESSION['id']." and EP.VALIDATED = 1");
		if ($rep == false) {
			return "SELECT_ERROR";
		}
		$ExamPermisOccuped= $rep['result'];

		$RendezVous = array();
		foreach ($PLanningNoValidated as $planning) {
			$RendezVous[] = $planning;
		}
		foreach ($PLanningValidated as $planning) {
			$RendezVous[] = $planning;
		}
		foreach ($PLanningOccuped as $planning) {
			$RendezVous[] = $planning;
		}

		foreach ($ExamPermisNoValidated as $examPermis) {
			$RendezVous[] = $examPermis;
		}
		foreach ($ExamPermisValidated as $examPermis) {
			$RendezVous[] = $examPermis;
		}
		foreach ($ExamPermisOccuped as $examPermis) {
			$RendezVous[] = $examPermis;
		}

		//error_log("size $RendezVous : "+sizeof($RendezVous));

		$i = 0;
		while ($i < sizeof($RendezVous)) {
			$RendezVous[$i]['val'] = str_replace('-', '', $RendezVous[$i]['date']).str_replace(':', '', $RendezVous[$i]['heureD']);
			$i += 1;
		}

		$RendezVousTried = array();

		while(sizeof($RendezVous) > 0) {
			$i = 0;
			if (sizeof($RendezVous) > 1) {
				while ($i < sizeof($RendezVous)) {
					$min = true;
					$j = 0;
					while($j < sizeof($RendezVous)) {
						if ($i != $j & (int)$RendezVous[$i]['val'] > (int)$RendezVous[$j]['val']) {
							$min = false;
							break;
						}
						$j += 1;
					}
					if ($min == true) {
						$RendezVousTried[] = $RendezVous[$i];
						array_splice($RendezVous, $i, 1);
						break;
					}
					$i += 1;
				}
			} else {
				$RendezVousTried[] = $RendezVous[0];
				array_splice($RendezVous, 0, 1);
			}
		}
		$i = 0;
		while($i < sizeof($RendezVousTried)) {
			unset($RendezVousTried[$i]['val']);
			$i += 1;
		}
		return $RendezVousTried;
	}
}
?>