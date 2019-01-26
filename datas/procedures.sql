DROP PROCEDURE IF EXISTS connectClient; /* connecter les utilisateurs */
DELIMITER //
CREATE PROCEDURE connectClient(IN prenom varchar(30), nom varchar(30), passwd char(40))
BEGIN
	/* verifie les information de connexion et les stocke dans des variables */
	SELECT count(*) as count,IDCOMPTE as id, PERMSCLIENT as perm, PRENOMCOMPTE as prenom, NOMCOMPTE as nom, STATUSCOMPTE as type, BANNED as banned
		INTO @count, @id, @perm, @prenom, @nom, @type, @banned
		FROM COMPTES WHERE PRENOMCOMPTE = prenom and NOMCOMPTE = nom and PASSWDCOMPTE = passwd GROUP BY IDCOMPTE;
	
	if @count is NULL then /* s'il n'ya aucun comte avec ces login mot de passe */
		SELECT "NOT_EXIST" as rep;
	ELSEIF @count = 1 and @banned = 1 then /* si ce compte est bannis */
		SELECT "BANNED" as rep;
	ELSE /* si il existe bel et bien et qu'il n'est pas bannis */
		SELECT "OK" as rep,@id as id,@perm as perm,@prenom as prenom,@nom as nom,@type as type;
	end if;
END//
DELIMITER ;

DROP PROCEDURE IF EXISTS validExam; /* valider un permis exam */
DELIMITER //
CREATE PROCEDURE validExam(IN idExamPermisIN int(2), idCO int(2))
BEGIN
	/* vérifie si ce permis d'exam existe */
	if (SELECT count(IDEXAMPERMIS) FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN) = 0 then
		SELECT "Ce permis n'existe pas" as rep;
	ELSE
		/* récupère l'id du moniteur associé au compte qui lance cette procedure */
		SELECT IDMONITEUR INTO @idMoniteur FROM IDMONITEUR_BY_COMPTE WHERE IDCOMPTE = idCO;
		
		/* valide l'exam permis */
		UPDATE EXAMPERMIS SET VALIDATED = 1, IDMONITEUR = @idMoniteur WHERE IDEXAMPERMIS = idExamPermisIN;
		
		/* récupère les id de l'exam et du client concerné par ce permis*/
		SELECT IDEXAM,IDCLIENT INTO @idExam, @idClient FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN;
		
		/* supprime les autres exam permis demandé par le même client et pour le même type d'exam */
		DELETE FROM EXAMPERMIS WHERE IDEXAM = @idExam and IDCLIENT = @idClient and IDEXAMPERMIS != idExamPermisIN;
		
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS validPlanning; /* valider un planning */
DELIMITER //
CREATE PROCEDURE validPlanning(IN idPlanningIN int(2), idCO int(2))
BEGIN
	/* vérifie si ce planning existe */
	if (SELECT count(IDPLANNING) FROM PLANNING WHERE IDPLANNING = idPlanningIN) = 0 then
		SELECT "Ce planning n'existe pas" as rep;
	ELSE
		/* récupère l'id du moniteur associé au compte qui lance cette procedure */
		SELECT IDMONITEUR INTO @idMoniteur FROM IDMONITEUR_BY_COMPTE WHERE IDCOMPTE = idCO;
		
		/* valide l'exam permis */
		UPDATE PLANNING SET VALIDATED = 1, IDMONITEUR = @idMoniteur WHERE IDPLANNING = idPlanningIN;
		
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deValidExam; /* dé-valider un permis exam */
DELIMITER //
CREATE PROCEDURE deValidExam(IN idExamPermisIN int(2), idCO int(2), perm varchar(10))
BEGIN
	/* vérifie si ce permis d'exam existe */
	if perm = "admin" then
		SELECT IDMONITEUR INTO @idMoniteur FROM IDMONITEUR_BY_COMPTE WHERE IDCOMPTE = idCO;
		SELECT COUNT(IDEXAMPERMIS) INTO @count FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN and IDMONITEUR = @idMoniteur;
	ELSEIF perm = "superadmin" then
		SELECT COUNT(IDEXAMPERMIS) INTO @count FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN;
	end if;
	if @count = 0 then
		SELECT "Ce rendez vous n'existe pas" as rep;
	ELSE
		UPDATE EXAMPERMIS SET VALIDATED = 0, IDMONITEUR = NULL, RESULTATP = NULL WHERE IDEXAMPERMIS = idExamPermisIN;
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deValidPlanning; /* dé-valider un planning */
DELIMITER //
CREATE PROCEDURE deValidPlanning(IN idPlanningIN int(2), idCO int(2), perm varchar(10))
BEGIN
	/* vérifie si ce planning existe */
	if perm = "admin" then
		SELECT IDMONITEUR INTO @idMoniteur FROM IDMONITEUR_BY_COMPTE WHERE IDCOMPTE = idCO;
		SELECT COUNT(IDPLANNING) INTO @count FROM PLANNING WHERE IDPLANNING = idPlanningIN and IDMONITEUR = @idMoniteur;
	ELSEIF perm = "superadmin" then
		SELECT COUNT(IDPLANNING) INTO @count FROM PLANNING WHERE IDPLANNING = idPlanningIN;
	end if;
	if @count = 0 then
		SELECT "Ce rendez vous n'existe pas" as rep;
	ELSE
		UPDATE PLANNING SET VALIDATED = 0, IDMONITEUR = NULL WHERE IDPLANNING = idPlanningIN;
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deleteExam; /* suprimme un exam permis */
DELIMITER //
CREATE PROCEDURE deleteExam(IN idExamPermisIN int(2), idCO int(2), perm varchar(10))
BEGIN
	/* vérifie si cet exam existe */
	if perm = "user" then
		SELECT IDCLIENT INTO @idClient FROM IDCLIENT_BY_COMPTE WHERE IDCOMPTE = idCO;
		SELECT COUNT(IDEXAMPERMIS) INTO @count FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN and IDCLIENT = @idClient;
	ELSEIF perm = "superadmin" then
		SELECT COUNT(IDEXAMPERMIS) INTO @count FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN;
	end if;
	if @count = 0 then
		SELECT "Ce rendez vous n'existe pas" as rep;
	ELSE
		DELETE FROM EXAMPERMIS WHERE IDEXAMPERMIS = idExamPermisIN;
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deletePlanning; /* suprimme un planning */
DELIMITER //
CREATE PROCEDURE deletePlanning(IN idPlanningIN int(2), idCO int(2), perm varchar(10))
BEGIN
	/* vérifie si ce planning existe */
	if perm = "user" then
		SELECT IDCLIENT INTO @idClient FROM IDCLIENT_BY_COMPTE WHERE IDCOMPTE = idCO;
		SELECT COUNT(IDPLANNING) INTO @count FROM PLANNING WHERE IDPLANNING = idPlanningIN and IDCLIENT = @idClient;
	ELSEIF perm = "superadmin" then
		SELECT COUNT(IDPLANNING) INTO @count FROM PLANNING WHERE IDPLANNING = idPlanningIN;
	end if;
	if @count = 0 then
		SELECT "Ce rendez vous n'existe pas" as rep;
	ELSE
		DELETE FROM PLANNING WHERE IDPLANNING = idPlanningIN;
		SELECT "OK" as rep;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS addExam; /* ajouter un exam */
DELIMITER //
CREATE PROCEDURE addExam(IN dateIN date, heureDiN time, typeIN int(2), idCO int(2))
BEGIN
	/* vérifie si le type d'exam existe bien */
	if (SELECT count(*) FROM EXAM WHERE IDEXAM = typeIN) = 0 then
		SELECT "Ce type d'exam n'existe pas" as rep;
	ELSE
		/* incremente de 2 heure l'heure de début de ce permis d'exam */
		SELECT TIME(date_add(CONCAT(dateIN," ",heureDiN), interval 2 hour)) INTO @heureFiN;
		/* récupère l'id client associé au compte qui lance cette procedure */
		SELECT IDCLIENT INTO @idClient FROM IDCLIENT_BY_COMPTE WHERE IDCOMPTE = idCO;
		/* ajouter le permis exam */
		INSERT INTO EXAMPERMIS VALUES (0,@idClient,typeIN,NULL,dateIN,heureDiN,@heureFiN,NULL,0);
		/* retourne 'OK' ainsi que l'id du permis exam venant d'être ajouté */
		SELECT "OK" as rep,IDEXAMPERMIS as idExamPermis FROM EXAMPERMIS WHERE dateP = dateIN and HEUREDEBUTP = heureDiN and HEUREFINP = @heureFiN and
																			  IDCLIENT = @idClient;
	end if;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS addPlanning; /* ajouter un planning */
DELIMITER //
CREATE PROCEDURE addPlanning(IN dateIN date, heureDiN time, heureFiN time, lecon int(2), idCO int(2))
BEGIN
	/* vérifie si ce type de lecon existe bien */
	if (SELECT count(*) FROM LECON WHERE IDL = lecon) = 0 then
		SELECT "Cette leçon n'existe pas" as rep;
	ELSE
		/* récupère l'id client associé au compte qui lance cette procedure */
		SELECT IDCLIENT INTO @idClient FROM IDCLIENT_BY_COMPTE WHERE IDCOMPTE = idCO;
		/* ajouter le planning */
		INSERT INTO PLANNING VALUES (0,lecon,@idClient,NULL,CONCAT(dateIN," ",heureDiN),CONCAT(dateIN," ",heureFiN),0);
		/* retourne 'OK' ainsi que l'id du permis exam venant d'être ajouté */
		SELECT "OK" as rep,IDPLANNING as idPlanning FROM PLANNING WHERE date(DHDEBUTP) = dateIN and TIME_TO_SEC(TIME(DHDEBUTP)) = TIME_TO_SEC(heureDiN) and 
														TIME_TO_SEC(TIME(DHFINP)) = TIME_TO_SEC(heureFiN) and IDCLIENT = @idClient;
	end if;
END //
DELIMITER ;