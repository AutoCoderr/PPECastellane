<?php 
session_start(); 
$statusdict = [
"salarie" => "Salarié(e)",
"etudiant" => "Etudiant",
"moniteur" => "Moniteur",
"Directeur" => "Directeur",
"CoDirecteur" => "Co-Directeur"
];
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=0.5">
  <link rel="stylesheet" href="/bootstrap.min.css">
  <link rel="stylesheet" href="/styles.css">
  <script src="/js/jquery.js"></script>
  <script src="/popper.min.js"></script>
  <script src="/bootstrap.min.js"></script>


<title><?php echo ($title); ?></title>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
  <a class="navbar-brand" title="Sommaire" href="/sommaire.php"><img style="float: left; margin-right: 10px;" src="/img/logo.png" width="25px" height="25px"/><span>Castellane-Auto</span></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
          <li class="nav-item" id="presentationli">
            <a class="nav-link" href="/index.php">Présentation</a>
          </li>
          <li class="nav-item" id="tarifli">
            <a class="nav-link" href="/tarif.php">Tarif</a>
          </li>
          <li class="nav-item" id="contactli">
			<a class="nav-link" href="/contact.php">Contacts</a>
          </li>
          <li class="nav-item dropdown" id="espaceli">
            <a class="nav-link dropdown-toggle" href="" id="navbardrop" data-toggle="dropdown">
                <?php if (isset($_SESSION['perm'])) {
                    if ($_SESSION['perm'] == "admin") {
                      echo ("Espace Moniteur");
                    } else if ($_SESSION['perm'] == "superadmin") {
                      echo ("Espace Directeur");
                    } else if ($_SESSION['perm'] == "user") {
                      echo ("Espace Client");
                    }
                    } else {
                    echo ("Espaces Client");  
                    }
                    ?>
            </a>
            <div class="dropdown-menu">
            	<a class="dropdown-item" href="/fiche.php">Mes informations</a>
              <a class="dropdown-item" href="/calendar.php">Calendrier</a>
              <a class="dropdown-item" href="/diplome.php">Diplomes</a>
			  <?php
				if (isset($_SESSION['perm'])) {
					if ($_SESSION['perm'] == "superadmin") {
						echo ("<a class='dropdown-item' href='/admin.php'>Administration</a>");
            echo ("<a class='dropdown-item' href='/vehicules.php'>Vehicules</a>");
          } else if ($_SESSION['perm'] == "user") {
            echo ("<a class='dropdown-item' href='/quiz.php'>Quiz</a>");
          }
        }
        ?>
            </div>
          </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                  <?php
    if (isset($_SESSION['id'])) {
		echo ('<li class="nav-item" id="MPli">
				<a class="nav-link" id="MPa" href="/MP.php">Messages privés</a>
			   </li>');
    }
    ?>
        <li class="nav-item" id="registerli"><a class="nav-link" href="/register.php"><span class="glyphicon glyphicon-user"></span>Inscription</a></li>
    <li class="nav-item" id="loginli"><a class="nav-link" href="/login.php"><span class="glyphicon glyphicon-log-in"></span><?php
                                                if (!isset($_SESSION['prenom'])) {
                                                  echo ("Connexion");
                                                } else {
                                                  echo ("Déconnexion");
                                                }
                                                ?></a></li>
    <?php
      if (isset($_SESSION['prenom'])) {
        //echo ("<li id='ficheli'><a href='/fiche.php' style='color: #FFFFFF;font-size: 13;' class='lien'>[".$_SESSION['prenom']." ".$_SESSION['nom']."] ".$statusdict[$_SESSION['type']]."</a></li>");
        echo ("<li><a style='color:#FFFFFF;font-size: 13;'>[".$_SESSION['prenom']." ".$_SESSION['nom']."] [".$statusdict[$_SESSION['type']]."]</a></li>");
      }
    ?>
      </ul>
    </div>
</nav>

<script>
  $("#<?php echo ($onglet); ?>").addClass("active");
  <?php
  include("/var/www/PPE/controleur/controleur.php");
  $controleur = new Controleur (null,null,null,null);
  if (isset($_SESSION['id'])) {
	$rep = $controleur->nb_mp($_SESSION['id']);
	if (gettype($rep) == "string") {
		echo ("$('#MPa').empty();");
		echo ("$('#MPa').append('Error');");
	} else if ($rep > 0){
		echo ("$('#MPa').empty();");
		echo ("$('#MPa').append('Messages privés (".$rep.")');");
		echo ("$('#MPa').attr('nb',".$rep.");");
	}
  }
?>
 </script>
</head>
<body>
<center>
<br><br><br>
<div id="background">
