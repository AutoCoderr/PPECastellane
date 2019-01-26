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
  <link rel="stylesheet" href="/styles.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="/js/jquery.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


<title><?php echo ($title); ?></title>
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a title="Sommaire" class="navbar-brand" href="/sommaire.php"><img style="float: left; margin-right: 10px;" src="/img/logo.png" width="25px" height="25px"/><span>Castellane-Auto</span></a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li id="presentationli"><a href="/index.php">Présentation</a></li>
		<li id="tarifli"><a href="/tarif.php">Tarif</a></li>
		<li id="contactli"><a href="/contact.php">Contact</a></li>
		<li id="espaceli"><a href="/fiche.php">
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
				?></a>
			<ul class="submenu">
				<li><a href='/calendar.php'>Calendrier</a></li>
				<li><a href='/diplome.php'>Diplomes</a></li>
				<?php
					if (isset($_SESSION['perm'])) {
						if ($_SESSION['perm'] == "superadmin") {
							echo ("<li><a href='/admin.php'>Administration</a></li>");
						}
					}
				?>
			</ul>
		</li>
      </ul>
	  <ul class="nav navbar-nav navbar-right">
		<?php
		if (isset($_SESSION['id'])) {
			echo ('<li id="MPli"><a id="MPa" href="/MP.php">Messages privés</a></li>');
		}
		?>
        <li id="registerli"><a href="/register.php"><span class="glyphicon glyphicon-user"></span>Inscription</a></li>
		<li id="loginli"><a href="/login.php"><span class="glyphicon glyphicon-log-in"></span><?php
																								if (!isset($_SESSION['prenom'])) {
																									echo ("Connexion");
																								} else {
																									echo ("Déconnexion");
																								}
																							  ?></a></li>
		<?php
			if (isset($_SESSION['prenom'])) {
			  //echo ("<li id='ficheli'><a href='/fiche.php' style='color: #FFFFFF;font-size: 13;' class='lien'>[".$_SESSION['prenom']." ".$_SESSION['nom']."] ".$statusdict[$_SESSION['type']]."</a></li>");
			  echo ("<li><a style='color:#FFFFFFFF;font-size: 13;'>[".$_SESSION['prenom']." ".$_SESSION['nom']."] [".$statusdict[$_SESSION['type']]."]</a></li>");
			}
		?>
      </ul>
    </div>
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
