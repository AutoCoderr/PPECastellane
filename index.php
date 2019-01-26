<html>
<?php
$title = "Acceuil";
$onglet = "presentationli";
include("vue/head.php");
?>
<style>
#background {
	height: 300vH;
}
</style>
<section class="bg-primary text-center text-white">
    <h1 id="presentation">
        Castellane-Auto
    </h1>
</section>
<a class="scrolling lien" href="#comments">Voir les commentaires</a>
<section class='bg-second text-center texte-black'>
<table>
	<tr>
		<td style="text-align:center">
		<h5>
			<p>L'auto-école Castellane-Auto vous accueille chaleureusement dans son
			agence basée à Toulon dans le département du Var.<br>
			Idéalement situés en plein centre-ville, nous sommes à proximité <br>
			des établissements scolaires.</p>
			<br>

			<p>Que vous soyez collégien, lycéen ou déjà dans le monde du travail, <br>
			l’auto-école Castellane-Auto, <br>
			située au 357 Avenue de la République à Toulon est heureuse de vous accueillir pour vous proposer<br>
			des formations de conduite depuis 1981.</p>
		</h5>
		</td>
		<td style="vertical-align: top;">
			<img style="width: 20vW;height: auto"src="/img/photo.jpg"/>
		</td>
	</tr>
</table>
    <h5>
	
		<br>
		<br>
		
		<p>Fort de nos nombreuses années d’expériences, <br>
		nous souhaitons offrir à nos clients des prestations de qualité <br>
		tout en gardant des tarifs compétitifs ponctués par un suivi personnalisé.<br>
		Nous dispensons un grand nombre de formations telle que la conduite accompagnée (AAC), le permis A, le permis B,
		le BSR ainsi que le code de la Route.</p>
		<br>

		<p>Il est également possible de suivre ces formations en accéléré, d’effectuer<br>
		des stages de perfectionnement et de
		récupération de points.</p>
		<br>

		<p>Afin de vous permettre de vous entrainer dans des conditions de conduite optimales,<br>
		nos véhicules de formation sont
		des Peugeot 208.</p>
		<br>

		<p>Une équipe de 10 enseignants à la conduite seront ainsi à votre disposition<br>
		pendant toute la durée de votre apprentissage.</p>
		<br>

		<p>Chaque élève est unique, c’est pourquoi nous vous accompagnons individuellement dans une ambiance professionnelle et sympathique.</p>
    </h5>
</section>
<br>
<br>
<h2 id="comments">Les commentaires : </h2>
<br>
<a class="scrolling lien" href="#presentation">Voir la présentation</a>
<br>
Ajouter un commentaire :<br>
<textarea style="width: 50vW; height: 150px;" id="comment"></textarea><br>
<input type="button" value="Envoyer" onclick="sendAvis()">
<div id="errorAvis"></div>
<br>
<center>
<br>
<?php
echo $controleur->displayAvis();
?>
</center>
<br><br><br><br><br><br><br><br>
<script src="/js/scrolling.js"></script>
<script>

$("#background").css("height",parseInt($("#commentsTable").height())+parseInt($("#background").css("height").replace("px",""))+"px");

var token = 
<?php 
if (isset($_SESSION['token'])) { 
	echo $_SESSION['token'];
} else {
	echo "'anon'";
}
?>;
var globalid;
var oldMessage;

function displayModifForm(id) {
	oldMessage = $("#"+id).html();
	$("#"+id).html("<textarea style='width:500px;height:75px;'>"+remplace(oldMessage,"<br>","\n")+"</textarea><br><input type='button' value='Valider' onclick='sendModif("+id+")'>");
	$("#buttonModif_"+id).val("Annuler");
	$("#buttonModif_"+id).attr("onclick","hideModifForm("+id+")");
	$("#"+id+" textarea").select();
}

function hideModifForm(id) {
	$("#"+id).html(oldMessage);
	$("#buttonModif_"+id).val("Modifier");
	$("#buttonModif_"+id).attr("onclick","displayModifForm("+id+")");
}

function sendModif(id) {
	if (!confirm("Êtes vous sûre de vouloir modifier ce message ?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_messages.php',
		{
			action: "modifAvis",
			content: $("#"+id+" textarea").val(),
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#"+globalid).html(remplace($("#"+id+" textarea").val(),"\n","<br>"));
				$("#errorModif_"+globalid).empty();
				$("#errorModif_"+globalid).append("<font color='green'>Modification réussi!</font>");
				$("#buttonModif_"+id).val("Modifier");
				$("#buttonModif_"+id).attr("onclick","displayModifForm("+id+")");
			} else if (data.rep == "failed") {
				$("#errorModif_"+globalid).empty();
				$("#errorModif_"+globalid).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#errorModif_"+globalid).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#errorModif_"+globalid).append("</ul>");
			}
		},
		'json'
	);
}

function suppr(id) {
	if (!confirm("Êtes vous sûre de vouloir supprimer ce message ?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_messages.php',
		{
			action: "supprAvis",
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				window.location.reload();
			} else if (data.rep == "failed") {
				$("#errorSuppr_"+globalid).empty();
				$("#errorSuppr_"+globalid).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#errorSuppr_"+globalid).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#errorSuppr_"+globalid).append("</ul>");
			}
		},
		'json'
	);
}

function sendAvis() {
	$.post(
		'/POST/controleur_messages.php',
		{
			action: "sendAvis",
			content: $("#comment").val(),
			token: token
		},

		function(data){
			if (data.rep == "success") {
				window.location.reload();
			} else if (data.rep == "failed") {
				$("#errorAvis").empty();
				$("#errorAvis").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#errorAvis").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#errorAvis").append("</ul>");
			}
		},
		'json'
	);
}

function remplace(str,A,B) {
	while(str.replace(A,B) != str) {
		str = str.replace(A,B);
	}
	return str;
}
</script>
<?php
include("vue/foot.php");
?>
</html>