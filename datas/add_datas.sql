use PPE;

INSERT INTO QUESTIONS VALUES (1,"Si il ya un feux rouge, que doit je faire ?");

INSERT INTO REPONSES VALUES(1,1,"Je lui fonce dedant sans réflechir",0);
INSERT INTO REPONSES VALUES(2,1,"Je m'arrête",1);
INSERT INTO REPONSES VALUES(3,1,"je vend ma voiture",0);


INSERT INTO QUESTIONS VALUES (2,"Si il ya un accident, que doit je faire ?");

INSERT INTO REPONSES VALUES(4,2,"Je prend en photo les gens",0);
INSERT INTO REPONSES VALUES(5,2,"Je m'achete un kebab",0);
INSERT INTO REPONSES VALUES(6,2,"Je continue d'avancer pour ne pas gener",1);

INSERT INTO QUESTIONS VALUES (3,"Je circule :");

INSERT INTO REPONSES VALUES(7,3,"En feux de croisement",1);
INSERT INTO REPONSES VALUES(8,3,"En feux de position seuls",0);
INSERT INTO REPONSES VALUES(9,3,"En feux de route",0);

INSERT INTO QUESTIONS VALUES  (4,"Le plus grand responsable des accidents mortels est :");

INSERT INTO REPONSES VALUES(10,4,"Le comportement du conducteur",0);
INSERT INTO REPONSES VALUES(11,4,"L'alcool",0);
INSERT INTO REPONSES VALUES(12,4,"La vitesse",1);
INSERT INTO REPONSES VALUES(13,4,"La fatigue",0);

INSERT INTO QUESTIONS VALUES  (5,"Je suis le dernier de la file, j’allume les feux de détresse :");

INSERT INTO REPONSES VALUES(14,5,"Oui",1);
INSERT INTO REPONSES VALUES(15,5,"Non",0);

INSERT INTO QUESTIONS VALUES  (6,"Je doit ralentir :");

INSERT INTO REPONSES VALUES(16,6,"Oui",0);
INSERT INTO REPONSES VALUES(17,6,"Non",1);

INSERT INTO QUESTIONS VALUES  (7,"Je circule hors agglomération, j’allume :");

INSERT INTO REPONSES VALUES(18,7,"En feux de route",0);
INSERT INTO REPONSES VALUES(19,7,"En feux de croisement",1);
INSERT INTO REPONSES VALUES(20,7,"En feux de position seuls",0);

INSERT INTO QUESTIONS VALUES  (8,"Pour limiter la consommation de carburant, <br>je dois changer les vitesses à bas régime moteur :");

INSERT INTO REPONSES VALUES(21,8,"Oui",0);
INSERT INTO REPONSES VALUES(22,8,"Non",1);

INSERT INTO QUESTIONS VALUES  (9,"Dans cette situation : ");

INSERT INTO REPONSES VALUES(23,9,"J'avance lentement",0);
INSERT INTO REPONSES VALUES(24,9,"je m'arrête immédiatement",1);

INSERT INTO QUESTIONS VALUES  (10,"L’assurance responsabilité civile <br>couvre les dégâts d’un véhicule volé :");

INSERT INTO REPONSES VALUES(25,10,"Oui",1);
INSERT INTO REPONSES VALUES(26,10,"Non",0);



INSERT INTO MOIS VALUE (12,2010);


INSERT INTO MODELE VALUE (1,"Peugeot 208","2017-01-01");
INSERT INTO VOITURE VALUE (1,"Diesel",null,null);

INSERT INTO VEHICULE VALUE (1,1,"OP-485-LM 31","2017-05-18",20);
INSERT INTO VEHICULE VALUE (2,1,"HG-852-NA 31","2017-05-18",20);
INSERT INTO VEHICULE VALUE (3,1,"CE-159-AI 31","2017-05-18",20);
INSERT INTO VEHICULE VALUE (4,1,"DP-789-NB 31","2017-05-18",20);
INSERT INTO VEHICULE VALUE (5,1,"GE-963-ME 31","2017-05-18",20);

INSERT INTO MODELE VALUE (2,"Citroen DS4","2012-01-01");
INSERT INTO VOITURE VALUE (2,"Diesel",null,null);

INSERT INTO VEHICULE VALUE (6,2,"VT-357-ZP 31","2012-04-15",20);
INSERT INTO VEHICULE VALUE (7,2,"VD-157-UY 31","2012-04-15",20);

INSERT INTO ROULER VALUE (12,1,200000);
INSERT INTO ROULER VALUE (12,2,100000);
INSERT INTO ROULER VALUE (12,3,12000);
INSERT INTO ROULER VALUE (12,4,135848);
INSERT INTO ROULER VALUE (12,5,1500);
INSERT INTO ROULER VALUE (12,6,200376);
INSERT INTO ROULER VALUE (12,7,1548);



INSERT INTO COMPTES VALUE (0,"Directeur","LE","PATRON","superadmin","ec082a647eda874d1cc2bc03b493d0a39f24f1ba",0,"julienbouvet78@hotmail.com");

INSERT INTO COMPTES VALUE (0,"moniteur","Jean","LUC","admin","ec082a647eda874d1cc2bc03b493d0a39f24f1ba",0,"julienbouvet78@hotmail.com");

INSERT INTO COMPTES VALUE (0,"fromContact","From","Contact","user","",0,"");

INSERT INTO MONITEUR VALUE (1,3,2,"Jean LUC","1996-02-04");

INSERT INTO LECON VALUE (1,"LES FEUX ROUGES",5.0,20);
INSERT INTO LECON VALUE (2,"LES VIRAGES",5.0,20);


INSERT INTO PLANNING VALUE (0,1,1,1,"2018-12-15 15:00","2018-12-15 17:00",1);

INSERT INTO PLANNING VALUE (0,1,1,null,"2018-12-20 15:00","2018-12-20 17:00",0);

INSERT INTO PLANNING VALUE (0,1,1,null,"2018-12-20 10:00","2018-12-20 12:00",0);

INSERT INTO EXAM VALUE (1,"Code de la route",22,20);
INSERT INTO EXAM VALUE (2,"Permis de conduire (Permis B)",550,20);
INSERT INTO EXAM VALUE (3,"Permis moto (Permis A)",550,20);
INSERT INTO EXAM VALUE (4,"Permis camion (Permis C)",550,20);

INSERT INTO EXAMPERMIS VALUE (0,1,1,1,"2018-12-25","15:00","17:00",NULL,1);

INSERT INTO AVIS VALUE (0,"2018-11-25 17:30",1,"Je suis ARROGANT");
INSERT INTO AVIS VALUE (0,"2018-11-25 18:30:42",3,"Bonjour, je suis un moniteur");