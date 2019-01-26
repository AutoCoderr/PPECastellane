DROP DATABASE IF EXISTS PPE;

CREATE DATABASE IF NOT EXISTS PPE;
USE PPE;

CREATE TABLE Erreur (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    erreur VARCHAR(255) UNIQUE
);

# -----------------------------------------------------------------------------
#       TABLE : MOIS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MOIS
 (
   NUMMOIS INTEGER NOT NULL  ,
   ANNEE INTEGER NULL  
   , PRIMARY KEY (NUMMOIS) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : ETUDIANT
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ETUDIANT
 (
   IDCLIENT INTEGER NOT NULL  ,
   NIVEAUETUDE VARCHAR(128) NULL  ,
   REDUCTION CHAR(3) NULL  ,
   ADDRCLIENT CHAR(50) NULL  ,
   DATENAISSANCECLIENT DATE NULL  ,
   NUMTELCLIENT CHAR(10) NULL  ,
   DATEINSCRIPTIONCLIENT DATE NULL  ,
   MODEFACTURATIONCLIENT VARCHAR(15) NULL  
   , PRIMARY KEY (IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MOTO
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MOTO
 (
   IDMODELE INTEGER NOT NULL  ,
   CYLINDRE CHAR(10) NULL  ,
   PUISSANCE CHAR(10) NULL  ,
   NOMMODELE VARCHAR(30) NULL  ,
   ANNEMODELE DATE NULL  
   , PRIMARY KEY (IDMODELE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : EXAM
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS EXAM
 (
   IDEXAM INTEGER NOT NULL AUTO_INCREMENT  ,
   TYPEEXAM VARCHAR(30) NOT NULL  ,
   PRIXEXAM int(3) NOT NULL  ,
   KM INTEGER NOT NULL
   , PRIMARY KEY (IDEXAM) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : VOITURE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS VOITURE
 (
   IDMODELE INTEGER NOT NULL  ,
   TYPE_CONSO VARCHAR(30) NULL  ,
   NOMMODELE VARCHAR(30) NULL  ,
   ANNEMODELE DATE NULL  
   , PRIMARY KEY (IDMODELE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : VEHICULE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS VEHICULE
 (
   IDVEHICULE INTEGER NOT NULL AUTO_INCREMENT  ,
   IDMODELE INTEGER NOT NULL  ,
   NUM_IMMATRICULATION CHAR(20) NULL  ,
   DATE_ACHAT DATE NULL  ,
   NB_KM_INITIAL INTEGER NULL  
   , PRIMARY KEY (IDVEHICULE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE VEHICULE
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_VEHICULE_MODELE
     ON VEHICULE (IDMODELE ASC);

# -----------------------------------------------------------------------------
#       TABLE : COMPTES
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS COMPTES
 (
   IDCOMPTE INTEGER NOT NULL AUTO_INCREMENT  ,
   STATUSCOMPTE VARCHAR(30) NULL  ,
   PRENOMCOMPTE VARCHAR(30) NULL  ,
   NOMCOMPTE VARCHAR(30) NULL  ,
   PERMSCLIENT CHAR(10) NULL  ,
   PASSWDCOMPTE CHAR(40) NULL  ,
   BANNED BOOL NOT NULL  ,
   MAIL VARCHAR(70) NULL
   , PRIMARY KEY (IDCOMPTE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MP
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MP
 (
   IDMP INTEGER NOT NULL AUTO_INCREMENT  ,
   IDCOMPTESRC INTEGER NOT NULL  ,
   IDCOMPTEDST INTEGER NOT NULL  ,
   DATEANDTIME datetime NOT NULL  ,
   OBJET varchar(60) NOT NULL  ,
   CONTENT varchar(500) NOT NULL  ,
   LU BOOL NOT NULL
   , PRIMARY KEY (IDMP) 
 ) 
 comment = "";
 
 
# -----------------------------------------------------------------------------
#       TABLE : AVIS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS AVIS
 (
   IDAVIS INTEGER NOT NULL AUTO_INCREMENT  ,
   DHAVIS datetime NOT NULL  ,
   IDCOMPTE INTEGER NULL  ,
   CONTENTAVIS VARCHAR(500) NOT NULL
   , PRIMARY KEY (IDAVIS) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : CLIENT
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CLIENT
 (
   IDCLIENT INTEGER NOT NULL AUTO_INCREMENT  ,
   IDCOMPTE INTEGER NOT NULL  ,
   ADDRCLIENT CHAR(50) NULL  ,
   DATENAISSANCECLIENT DATE NULL  ,
   NUMTELCLIENT VARCHAR(12) NULL  ,
   DATEINSCRIPTIONCLIENT DATE NULL  ,
   MODEFACTURATIONCLIENT VARCHAR(15) NULL ,
   CODE BOOL NOT NULL
   , PRIMARY KEY (IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE CLIENT
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_CLIENT_COMPTES
     ON CLIENT (IDCOMPTE ASC);

# -----------------------------------------------------------------------------
#       TABLE : MONITEUR
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MONITEUR
 (
   IDMONITEUR INTEGER NOT NULL AUTO_INCREMENT  ,
   IDCOMPTE INTEGER NOT NULL  ,
   IDVEHICULE INTEGER NOT NULL  ,
   NOMMONITEUR VARCHAR(30) NULL  ,
   DATEEMBAUCHEMONITEUR DATE NULL  
   , PRIMARY KEY (IDMONITEUR) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE MONITEUR
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_MONITEUR_VEHICULE
     ON MONITEUR (IDVEHICULE ASC);

	 
# -----------------------------------------------------------------------------
#       TABLE : SALARIE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SALARIE
 (
   IDCLIENT INTEGER NOT NULL  ,
   NOM_ENTREPRISE VARCHAR(128) NULL  ,
   ADDRCLIENT CHAR(50) NULL  ,
   DATENAISSANCECLIENT DATE NULL  ,
   NUMTELCLIENT CHAR(10) NULL  ,
   DATEINSCRIPTIONCLIENT DATE NULL  ,
   MODEFACTURATIONCLIENT VARCHAR(15) NULL  
   , PRIMARY KEY (IDCLIENT) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MODELE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MODELE
 (
   IDMODELE INTEGER NOT NULL AUTO_INCREMENT  ,
   NOMMODELE VARCHAR(30) NULL  ,
   ANNEMODELE DATE NULL  
   , PRIMARY KEY (IDMODELE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : LECON
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS LECON
 (
   IDL INTEGER NOT NULL AUTO_INCREMENT  ,
   NOML VARCHAR(30)  ,
   TARIFHEUREL REAL(5,2) NULL  ,
   KM_H INTEGER NOT NULL
   , PRIMARY KEY (IDL) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : ROULER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS ROULER
 (
   NUMMOIS INTEGER NOT NULL  ,
   IDVEHICULE INTEGER NOT NULL  ,
   KMPARCOURUS INTEGER NULL
   , PRIMARY KEY (NUMMOIS,IDVEHICULE) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE ROULER
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_ROULER_MOIS
     ON ROULER (NUMMOIS ASC);

CREATE  INDEX I_FK_ROULER_VEHICULE
     ON ROULER (IDVEHICULE ASC);

# -----------------------------------------------------------------------------
#       TABLE : EXAMPERMIS
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS EXAMPERMIS
 (
   IDEXAMPERMIS INTEGER NOT NULL AUTO_INCREMENT,
   IDCLIENT INTEGER NOT NULL  ,
   IDEXAM INTEGER NOT NULL  ,
   IDMONITEUR INTEGER NULL  ,
   DATEP DATE NULL  ,
   HEUREDEBUTP TIME NULL  ,
   HEUREFINP TIME NULL  ,
   RESULTATP int(2) NULL  ,
   VALIDATED BOOL NULL  
   , PRIMARY KEY (IDEXAMPERMIS)
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE EXAMPERMIS
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_EXAMPERMIS_CLIENT
     ON EXAMPERMIS (IDCLIENT ASC);

CREATE  INDEX I_FK_EXAMPERMIS_EXAM
     ON EXAMPERMIS (IDEXAM ASC);

# -----------------------------------------------------------------------------
#       TABLE : PLANNING
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS PLANNING
 (
   IDPLANNING INTEGER NOT NULL AUTO_INCREMENT  ,
   IDL INTEGER NOT NULL  ,
   IDCLIENT INTEGER NOT NULL  ,
   IDMONITEUR INTEGER NULL  ,
   DHDEBUTP DATETIME NULL  ,
   DHFINP DATETIME NULL  ,
   VALIDATED BOOL NULL   
   , PRIMARY KEY (IDPLANNING)
 ) 
 comment = "";

#------------------------------------------------------------------------------
#                 LE QUIZ
#------------------------------------------------------------------------------
DROP TABLE IF EXISTS REPONSES;
DROP TABLE IF EXISTS QUESTIONS;
CREATE TABLE QUESTIONS(
  IDQUESTION int(3) NOT NULL AUTO_INCREMENT,
  QUESTION varchar(255) NOT NULL,
  PRIMARY KEY (IDQUESTION)
)
comment = "";

CREATE TABLE REPONSES (
  IDREPONSE int(3) NOT NULL AUTO_INCREMENT,
  IDQUESTION int(3) NOT NULL,
  REPONSE varchar(255) NOT NULL,
  BONNE BOOL NOT NULL,
  PRIMARY KEY (IDREPONSE),
  FOREIGN KEY (IDQUESTION) REFERENCES QUESTIONS(IDQUESTION)
)
comment = "";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE PLANNING
# -----------------------------------------------------------------------------

CREATE  INDEX I_FK_PLANNING_LECON
     ON PLANNING (IDL ASC);

CREATE  INDEX I_FK_PLANNING_CLIENT
     ON PLANNING (IDCLIENT ASC);

CREATE  INDEX I_FK_PLANNING_MONITEUR
     ON PLANNING (IDMONITEUR ASC);

# -----------------------------------------------------------------------------
#       TABLE : H_IDL_LECON
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS H_IDL_LECON
 (
   IDL INTEGER NOT NULL  ,
   DATE_HISTO DATE NOT NULL  
   , PRIMARY KEY (IDL,DATE_HISTO) 
 ) 
 comment = "Table d'historisation des modifications de la table LECON";

# -----------------------------------------------------------------------------
#       INDEX DE LA TABLE H_IDL_LECON
# -----------------------------------------------------------------------------


CREATE  INDEX I_FK_H_IDL_LECON_LECON
     ON H_IDL_LECON (IDL ASC);


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------


ALTER TABLE ETUDIANT 
  ADD FOREIGN KEY FK_ETUDIANT_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE MOTO 
  ADD FOREIGN KEY FK_MOTO_MODELE (IDMODELE)
      REFERENCES MODELE (IDMODELE) ;


ALTER TABLE VOITURE 
  ADD FOREIGN KEY FK_VOITURE_MODELE (IDMODELE)
      REFERENCES MODELE (IDMODELE) ;


ALTER TABLE VEHICULE 
  ADD FOREIGN KEY FK_VEHICULE_MODELE (IDMODELE)
      REFERENCES MODELE (IDMODELE) ;

ALTER TABLE MP
  ADD FOREIGN KEY FK_MP_COMPTESRC (IDCOMPTESRC)
      REFERENCES COMPTES (IDCOMPTE) ;

ALTER TABLE MP 
  ADD FOREIGN KEY FK_MP_COMPTEDST (IDCOMPTEDST)
      REFERENCES COMPTES (IDCOMPTE) ;
	  
ALTER TABLE MONITEUR 
  ADD FOREIGN KEY FK_MONITEUR_COMPTES (IDCOMPTE)
      REFERENCES COMPTES (IDCOMPTE) ;


ALTER TABLE MONITEUR 
  ADD FOREIGN KEY FK_MONITEUR_VEHICULE (IDVEHICULE)
      REFERENCES VEHICULE (IDVEHICULE) ;


ALTER TABLE SALARIE 
  ADD FOREIGN KEY FK_SALARIE_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE ROULER 
  ADD FOREIGN KEY FK_ROULER_MOIS (NUMMOIS)
      REFERENCES MOIS (NUMMOIS) ;


ALTER TABLE ROULER 
  ADD FOREIGN KEY FK_ROULER_VEHICULE (IDVEHICULE)
      REFERENCES VEHICULE (IDVEHICULE) ;


ALTER TABLE EXAMPERMIS 
  ADD FOREIGN KEY FK_EXAMPERMIS_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE EXAMPERMIS 
  ADD FOREIGN KEY FK_EXAMPERMIS_EXAM (IDEXAM)
      REFERENCES EXAM (IDEXAM) ;
	 
ALTER TABLE EXAMPERMIS 
  ADD FOREIGN KEY FK_EXAMPERMIS_MONITEUR (IDMONITEUR)
      REFERENCES MONITEUR (IDMONITEUR) ;

ALTER TABLE PLANNING 
  ADD FOREIGN KEY FK_PLANNING_LECON (IDL)
      REFERENCES LECON (IDL) ;


ALTER TABLE PLANNING 
  ADD FOREIGN KEY FK_PLANNING_CLIENT (IDCLIENT)
      REFERENCES CLIENT (IDCLIENT) ;


ALTER TABLE PLANNING 
  ADD FOREIGN KEY FK_PLANNING_MONITEUR (IDMONITEUR)
      REFERENCES MONITEUR (IDMONITEUR) ;


ALTER TABLE H_IDL_LECON 
  ADD FOREIGN KEY FK_H_IDL_LECON_LECON (IDL)
      REFERENCES LECON (IDL) ;

CREATE VIEW IDCLIENT_BY_COMPTE 
	AS
		(SELECT CO.IDCOMPTE, CL.IDCLIENT FROM COMPTES CO, CLIENT CL WHERE CO.IDCOMPTE = CL.IDCOMPTE);

CREATE VIEW IDMONITEUR_BY_COMPTE
	AS
		(SELECT CO.IDCOMPTE, M.IDMONITEUR FROM COMPTES CO, MONITEUR M WHERE CO.IDCOMPTE = M.IDCOMPTE);

CREATE VIEW CODEAVEC /* vue qui indique quel moniteur à fait passer le code à quel elève */
	AS
		(SELECT DISTINCT IDMONITEUR,IDCLIENT FROM EXAMPERMIS WHERE VALIDATED = 1 and IDEXAM = 1);
		
CREATE VIEW CLIENTEXAM /* vue qui indique quel moniteur a fait passé quel type d'exam à quel elève */
	AS
		(SELECT DISTINCT EP.IDEXAMPERMIS,EP.IDMONITEUR,CO.PRENOMCOMPTE,CO.NOMCOMPTE,EP.RESULTATP,E.TYPEEXAM,EP.IDEXAM FROM EXAM E,EXAMPERMIS EP, CLIENT CL, COMPTES CO WHERE 
		 EP.VALIDATED = 1 AND EP.IDEXAM = E.IDEXAM AND EP.IDCLIENT = CL.IDCLIENT AND CL.IDCOMPTE = CO.IDCOMPTE AND EP.IDEXAM != 1);

CREATE VIEW COSALARIESANSCOUR /* vue qui liste tous les salariés sans cour*/
  AS
    (SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail,  CO.IDCOMPTE, CO.BANNED as banned, CL.ADDRCLIENT as addr, 
      CL.DATENAISSANCECLIENT as dateN, CL.NUMTELCLIENT as numtel,  CL.DATEINSCRIPTIONCLIENT as dateI, CL.MODEFACTURATIONCLIENT as facturation, 
      S.NOM_ENTREPRISE as entreprise, 0 as nbCour  
      FROM COMPTES CO, CLIENT CL, SALARIE S, PLANNING P  
      WHERE CO.STATUSCOMPTE = 'salarie' and CO.IDCOMPTE = CL.IDCOMPTE and CL.IDCLIENT = S.IDCLIENT GROUP BY id
    );

CREATE VIEW COSALARIE /* vue qui liste tous les salariés */
	AS
		(SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail,  CO.IDCOMPTE, CO.BANNED as banned, CL.ADDRCLIENT as addr, 
      CL.DATENAISSANCECLIENT as dateN, CL.NUMTELCLIENT as numtel,  CL.DATEINSCRIPTIONCLIENT as dateI, CL.MODEFACTURATIONCLIENT as facturation, 
      S.NOM_ENTREPRISE as entreprise, (COUNT(P.IDPLANNING)) as nbCour  
      FROM COMPTES CO, CLIENT CL, SALARIE S, PLANNING P  
      WHERE CO.STATUSCOMPTE = 'salarie' and CO.IDCOMPTE = CL.IDCOMPTE and CL.IDCLIENT = S.IDCLIENT and CL.IDCLIENT = P.IDCLIENT GROUP BY id
    );

CREATE VIEW COETUDIANTSANSCOUR  /* vue qui liste tous les étudiants sans cour */
  AS
    (SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail, CO.IDCOMPTE, CO.BANNED as banned, CL.ADDRCLIENT as addr, 
      CL.DATENAISSANCECLIENT as dateN, CL.NUMTELCLIENT as numtel,  CL.DATEINSCRIPTIONCLIENT as dateI, CL.MODEFACTURATIONCLIENT as facturation, E.NIVEAUETUDE as etude, 
      REDUCTION as reduction, 0 as nbCour  
      FROM COMPTES CO, CLIENT CL, ETUDIANT E, PLANNING P  
      WHERE CO.STATUSCOMPTE = 'etudiant' and CO.IDCOMPTE = CL.IDCOMPTE and CL.IDCLIENT = E.IDCLIENT GROUP BY id
    );

CREATE VIEW COETUDIANT  /* vue qui liste tous les étudiants */
	AS
		(SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail, CO.IDCOMPTE, CO.BANNED as banned, CL.ADDRCLIENT as addr, 
      CL.DATENAISSANCECLIENT as dateN, CL.NUMTELCLIENT as numtel,  CL.DATEINSCRIPTIONCLIENT as dateI, CL.MODEFACTURATIONCLIENT as facturation, E.NIVEAUETUDE as etude, 
      REDUCTION as reduction, (COUNT(P.IDPLANNING)) as nbCour  
      FROM COMPTES CO, CLIENT CL, ETUDIANT E, PLANNING P  
      WHERE CO.STATUSCOMPTE = 'etudiant' and CO.IDCOMPTE = CL.IDCOMPTE and CL.IDCLIENT = E.IDCLIENT and CL.IDCLIENT = P.IDCLIENT GROUP BY id
    );

CREATE VIEW COMONITEURSANSCOUR /* vue qui liste tous les moniteurs sans cour */
  AS
    (SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail, CO.IDCOMPTE, CO.BANNED as banned, 0 as nbCour , 
      M.DATEEMBAUCHEMONITEUR as dateE  
      FROM COMPTES CO, MONITEUR M, PLANNING P 
      WHERE CO.STATUSCOMPTE = 'moniteur' and CO.IDCOMPTE = M.IDCOMPTE
    );

CREATE VIEW COMONITEUR /* vue qui liste tous les moniteurs */
	AS
		(SELECT CO.IDCOMPTE as id, CO.PRENOMCOMPTE as prenom, CO.NOMCOMPTE as nom, CO.MAIL as mail, CO.IDCOMPTE, CO.BANNED as banned, (COUNT(P.IDPLANNING)) as nbCour , 
      M.DATEEMBAUCHEMONITEUR as dateE  
      FROM COMPTES CO, MONITEUR M, PLANNING P 
      WHERE CO.STATUSCOMPTE = 'moniteur' and CO.IDCOMPTE = M.IDCOMPTE and (P.IDMONITEUR = M.IDMONITEUR)
    );


DROP TRIGGER IF EXISTS before_insert_EXAMPERMIS;
DELIMITER //
CREATE TRIGGER before_insert_EXAMPERMIS /* CONTROLE LES AJOUT D'EXAM */
before insert ON EXAMPERMIS
for each row
BEGIN
	/* vérifie si la plage horraire est autorisé */
	if  ((TIME_TO_SEC(new.HEUREDEBUTP) between TIME_TO_SEC("00:00") and TIME_TO_SEC("07:59")) or (TIME_TO_SEC(new.HEUREDEBUTP) between TIME_TO_SEC("12:01") and TIME_TO_SEC("13:59")) or (TIME_TO_SEC(new.HEUREDEBUTP) between TIME_TO_SEC("19:01") and TIME_TO_SEC("23:59")))  or
	    ((TIME_TO_SEC(new.HEUREFINP) between TIME_TO_SEC("00:00") and TIME_TO_SEC("07:59")) or (TIME_TO_SEC(new.HEUREFINP) between TIME_TO_SEC("12:01") and TIME_TO_SEC("13:59")) or (TIME_TO_SEC(new.HEUREFINP) between TIME_TO_SEC("19:01") and TIME_TO_SEC("23:59"))) or
		(TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC("00:01") and TIME_TO_SEC("08:00") <= TIME_TO_SEC(new.HEUREFINP)) or 
		(TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC("12:00") and TIME_TO_SEC("14:00") <= TIME_TO_SEC(new.HEUREFINP)) or
		(TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC("19:00") and TIME_TO_SEC("23:59") <= TIME_TO_SEC(new.HEUREFINP)) then 
		signal sqlstate '45000' set message_text = "Cette plage horaire n'est pas autorisée";
	end if;
	/* verifie si le client a déjà le code dans le cas où il souhaitte passer un permis */
	if (SELECT count(*) FROM CLIENT CL WHERE new.IDCLIENT = CL.IDCLIENT and CL.CODE = 1) = 0 and new.IDEXAM != 1 then
		signal sqlstate '45000' set message_text = "Vous n'avez pas le code";
	end if;
	/* verifie si le client n'a pas deja un autre exam de même type validé */
	if (SELECT count(*) FROM EXAMPERMIS EP WHERE (EP.IDEXAM = new.IDEXAM and EP.IDCLIENT = new.IDCLIENT and EP.VALIDATED = 1) and 
		( to_days(EP.DATEP) > to_days(curdate()) ) ) > 0 then
		signal sqlstate '45000' set message_text = "Vous avez déjà un exam de ce type <br>prévu avec un moniteur.";
	end if;
	
	/* verifie si il ya deja un planning à cette date et heure demandé par ce même client*/
	if (SELECT count(*) FROM PLANNING P WHERE new.DATEP = date(P.DHDEBUTP) and 
	( TIME_TO_SEC(new.HEUREDEBUTP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
	TIME_TO_SEC(new.HEUREFINP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
	TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(new.HEUREFINP) >= TIME_TO_SEC(P.DHFINP)) and 
	(P.IDCLIENT = new.IDCLIENT ) ) > 0 then
		signal sqlstate '45000' set message_text = "Vous avez déjà demandé un rendez à cette heure";
	end if;
	/* verifie si il ya deja un exam à cette date et heure demandé par ce même client */
	if (SELECT count(*) FROM EXAMPERMIS EP WHERE new.DATEP = EP.DATEP and 
	( TIME_TO_SEC(new.HEUREDEBUTP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
	TIME_TO_SEC(new.HEUREFINP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
	TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(new.HEUREFINP) >= TIME_TO_SEC(EP.HEUREFINP)) and 
	(EP.IDCLIENT = new.IDCLIENT ) ) > 0 then
		signal sqlstate '45000' set message_text = "Vous avez déjà demandé un rendez à cette heure";
	end if;
end //
DELIMITER ;

DROP TRIGGER IF EXISTS before_update_EXAMPERMIS;
DELIMITER //
CREATE TRIGGER before_update_EXAMPERMIS /* CONTROLE LES VALIDATIONS D'EXAM */
before update ON EXAMPERMIS
for each row
BEGIN
	if old.VALIDATED = 0 and new.VALIDATED = 1 then
		/* verifie si il ya deja un planning à cette date et heure validé par ce moniteur*/
		if (SELECT count(*) FROM PLANNING P WHERE new.DATEP = date(P.DHDEBUTP) and 
		( TIME_TO_SEC(new.HEUREDEBUTP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
		TIME_TO_SEC(new.HEUREFINP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
		TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(new.HEUREFINP) >= TIME_TO_SEC(P.DHFINP)) and 
		(P.VALIDATED = 1 and P.IDMONITEUR = new.IDMONITEUR ) ) > 0 then
			signal sqlstate '45000' set message_text = "Vous avez déjà validé un rendez à cette heure";
		end if;
		/* verifie si il ya deja un exam à cette date et heure validé par ce moniteur */
		if (SELECT count(*) FROM EXAMPERMIS EP WHERE new.DATEP = EP.DATEP and 
		( TIME_TO_SEC(new.HEUREDEBUTP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
		TIME_TO_SEC(new.HEUREFINP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
		TIME_TO_SEC(new.HEUREDEBUTP) <= TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(new.HEUREFINP) >= TIME_TO_SEC(EP.HEUREFINP)) and 
		(EP.VALIDATED = 1 AND EP.IDMONITEUR = new.IDMONITEUR ) ) > 0 then
			signal sqlstate '45000' set message_text = "Vous avez déjà validé un rendez à cette heure";
		end if;
	end if;
end //
DELIMITER ;

DROP TRIGGER IF EXISTS before_insert_PLANNING;
DELIMITER //
CREATE TRIGGER before_insert_PLANNING /* CONTROLE LES AJOUT DE PLANNING */
before insert ON PLANNING
for each row
BEGIN
	/* vérifie si la plage horraire est autorisé */
	if  ((TIME_TO_SEC(new.DHDEBUTP) between TIME_TO_SEC("00:00") and TIME_TO_SEC("07:59")) or (TIME_TO_SEC(new.DHDEBUTP) between TIME_TO_SEC("12:01") and TIME_TO_SEC("13:59")) or (TIME_TO_SEC(new.DHDEBUTP) between TIME_TO_SEC("19:01") and TIME_TO_SEC("23:59"))) or
	    ((TIME_TO_SEC(new.DHFINP) between TIME_TO_SEC("00:00") and TIME_TO_SEC("07:59")) or (TIME_TO_SEC(new.DHFINP) between TIME_TO_SEC("12:01") and TIME_TO_SEC("13:59")) or (TIME_TO_SEC(new.DHFINP) between TIME_TO_SEC("19:01") and TIME_TO_SEC("23:59"))) or
		(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC("00:01") and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC("08:00")) or 
		(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC("12:00") and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC("14:00")) or
		(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC("19:00") and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC("23:59")) then 
		signal sqlstate '45000' set message_text = "Plage horaire interdite";
	end if;
	/* verifie si il ya deja un planning à cette date et heure demandé par ce même client */
	if (SELECT count(*) FROM PLANNING P WHERE date(new.DHDEBUTP) = date(P.DHDEBUTP) and 
	( TIME_TO_SEC(new.DHDEBUTP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
	TIME_TO_SEC(new.DHFINP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
	(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC(P.DHFINP)) ) and 
	(P.IDCLIENT = new.IDCLIENT ) ) > 0 then
		signal sqlstate '45000' set message_text = "Vous avez déjà demandé un rendez à cette heure";
	end if;
	/* verifie si il ya deja un exam à cette date et heure demandé par ce même client */
	if (SELECT count(*) FROM EXAMPERMIS EP WHERE date(new.DHDEBUTP) = EP.DATEP and 
	(TIME_TO_SEC(new.DHDEBUTP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
	TIME_TO_SEC(new.DHFINP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
	(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC(EP.HEUREFINP)) ) and 
	(EP.VALIDATED = 1 AND EP.IDMONITEUR = new.IDMONITEUR ) ) > 0 then
		signal sqlstate '45000' set message_text = "Vous avez déjà demandé un rendez à cette heure";
	end if;
end //
DELIMITER ;

DROP TRIGGER IF EXISTS before_update_PLANNING;
DELIMITER //
CREATE TRIGGER before_update_PLANNING /* CONTROLE LES VALIDATIONS DE PLANNING */
before UPDATE ON PLANNING
for each row
BEGIN
	if old.VALIDATED = 0 AND new.VALIDATED = 1 then
		/* verifie si il ya deja un planning à cette date et heure validé par ce moniteur*/
		if (SELECT count(*) FROM PLANNING P WHERE date(new.DHDEBUTP) = date(P.DHDEBUTP) and 
		( TIME_TO_SEC(new.DHDEBUTP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
		TIME_TO_SEC(new.DHFINP) BETWEEN TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(P.DHFINP) or
		(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC(P.DHDEBUTP) and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC(P.DHFINP)) ) and 
		(P.VALIDATED = 1 AND P.IDMONITEUR = new.IDMONITEUR ) ) > 0 then
			signal sqlstate '45000' set message_text = "Vous avez déjà validé un rendez à cette heure";
		end if;
		/* verifie si il ya deja un exam à cette date et heure validé par ce moniteur */
		if (SELECT count(*) FROM EXAMPERMIS EP WHERE date(new.DHDEBUTP) = EP.DATEP and 
		(TIME_TO_SEC(new.DHDEBUTP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
		TIME_TO_SEC(new.DHFINP) BETWEEN TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(EP.HEUREFINP) or
		(TIME_TO_SEC(new.DHDEBUTP) <= TIME_TO_SEC(EP.HEUREDEBUTP) and TIME_TO_SEC(new.DHFINP) >= TIME_TO_SEC(EP.HEUREFINP)) ) and 
		(EP.VALIDATED = 1 AND EP.IDMONITEUR = new.IDMONITEUR ) ) > 0 then
			signal sqlstate '45000' set message_text = "Vous avez déjà validé un rendez à cette heure";
		end if;
	end if;
end //
DELIMITER ;

DROP TRIGGER IF EXISTS before_insert_COMPTES;
DELIMITER //
CREATE TRIGGER before_insert_COMPTES /* CONTROLE QUE CE COMPTE N'EXISTE PAS DEJA */
before insert on COMPTES
for each row
BEGIN
	if (SELECT COUNT(IDCOMPTE) FROM COMPTES CO WHERE CO.PRENOMCOMPTE = new.PRENOMCOMPTE and CO.NOMCOMPTE = new.NOMCOMPTE) > 0 then
		signal sqlstate '45000' set message_text = "COMPTE_ALREADY_EXIST";
	end if;
end //
DELIMITER ;

DROP FUNCTION IF EXISTS getPrixLecon;
DELIMITER //
CREATE FUNCTION getPrixLecon(dateD datetime,dateF datetime,tarif float)
returns float
BEGIN
	DECLARE nbH float;
	SET nbH = TIMESTAMPDIFF(MINUTE, dateD, dateF)/60;
	return nbH*tarif;
END //
DELIMITER ;

/*DELIMITER //
CREATE TRIGGER after_update_PLANNING /* EN CAS DE VALIDATION D'UN PLANNING POUR UNE DATE ET UNE HEURE, SUPPRIME LES AUTRES RENDEZ-VOUS POUR LES MÊME DATES ET HEURES 
after update ON PLANNING
for each row
BEGIN
	if new.VALIDATED != old.VALIDATED and new.VALIDATED = 1 then
		DELETE FROM PLANNING WHERE date(new.DHDEBUTP) = date(DHDEBUTP) and
		( CONCAT(HOUR(new.DHDEBUTP),MINUTE(new.DHDEBUTP)) BETWEEN CONCAT(HOUR(DHDEBUTP),MINUTE(DHDEBUTP)) and CONCAT(HOUR(DHFINP),MINUTE(DHFINP)) OR
		  CONCAT(HOUR(new.DHFINP),MINUTE(new.DHFINP)) BETWEEN CONCAT(HOUR(DHDEBUTP),MINUTE(DHDEBUTP)) and CONCAT(HOUR(DHFINP),MINUTE(DHFINP)));
		DELETE FROM EXAMPERMIS WHERE date(new.DHDEBUTP) = DATEP and
		( CONCAT(HOUR(new.DHDEBUTP),MINUTE(new.DHDEBUTP)) BETWEEN CONCAT(HOUR(HEUREDEBUTP),MINUTE(HEUREDEBUTP)) and CONCAT(HOUR(HEUREFINP),MINUTE(HEUREFINP)) OR
		  CONCAT(HOUR(new.DHFINP),MINUTE(new.DHFINP)) BETWEEN CONCAT(HOUR(HEUREDEBUTP),MINUTE(HEUREDEBUTP)) and CONCAT(HOUR(HEUREFINP),MINUTE(HEUREFINP)));
	end if;
end //
DELIMITER ;

DELIMITER //
CREATE TRIGGER after_update_EXAMPERMIS/* EN CAS DE VALIDATION D'UN EXAM POUR UNE DATE ET UNE HEURE, SUPPRIME LES AUTRES RENDEZ-VOUS POUR LES MÊME DATES ET HEURES 
after update ON EXAMPERMIS
for each row
BEGIN
	if new.VALIDATED != old.VALIDATED and new.VALIDATED = 1 then
		DELETE FROM PLANNING WHERE new.DATEP = date(DHDEBUTP) and
		( CONCAT(HOUR(new.HEUREDEBUTP),MINUTE(new.HEUREDEBUTP)) BETWEEN CONCAT(HOUR(DHDEBUTP),MINUTE(DHDEBUTP)) and CONCAT(HOUR(DHFINP),MINUTE(DHFINP)) OR
		  CONCAT(HOUR(new.HEUREFINP),MINUTE(new.HEUREFINP)) BETWEEN CONCAT(HOUR(DHDEBUTP),MINUTE(DHDEBUTP)) and CONCAT(HOUR(DHFINP),MINUTE(DHFINP)));
		DELETE FROM EXAMPERMIS WHERE new.DATEP = DATEP and
		( CONCAT(HOUR(new.HEUREDEBUTP),MINUTE(new.HEUREDEBUTP)) BETWEEN CONCAT(HOUR(HEUREDEBUTP),MINUTE(HEUREDEBUTP)) and CONCAT(HOUR(HEUREFINP),MINUTE(HEUREFINP)) OR
		  CONCAT(HOUR(new.HEUREFINP),MINUTE(new.HEUREFINP)) BETWEEN CONCAT(HOUR(HEUREDEBUTP),MINUTE(HEUREDEBUTP)) and CONCAT(HOUR(HEUREFINP),MINUTE(HEUREFINP)));
	end if;
end //
DELIMITER ;/*

