<style>
#background {
	height: 200vH;
}
</style>
<script>
var users = {};
</script>
<center>
<br>
<br>
<h1>Administration</h1>
<h3 id='gestioncompte'>Gestion des comptes :</h3>
<input class="scrolling" href='#addmoniteur' type='button' value='Ajouter moniteur'>
<table>
	<tr>
		<td class='tabtd' style='width: 10vW'>Etudiants : <br><br><?php echo $controleur->listusers('COETUDIANT'); ?></td>
		<td class='tabtd' style='width: 10vW'>Salariés : <br><br><?php echo $controleur->listusers('COSALARIE'); ?></td>
		<td class='tabtd' style='width: 10vW'>Moniteurs : <br><br><?php echo $controleur->listusers('COMONITEUR'); ?></td>
	</tr>
</table>
<br>
<div id='ban'></div>
<div id='fiche'></div>
<br>
<br>
<h3 id='addmoniteur'>Ajouter moniteur :</h3>
<input class="scrolling" href='#gestioncompte' type='button' value='Gestion des comptes'><br><br>
Son prénom : <input id="prenom" type="text"><br>
Son nom : <input id="nom" type="text"><br>
Son mot de passe : <input id="passwd1" type="password"><br>
Le re-rentrer <input id="passwd2" type="password"><br>
<input type="button" value="Ajouter" onclick="addmoniteur()"><br>
<div id='repMoniteur'></div>
</center>
<script src="/js/scrolling.js"></script>
<script>

var token = <?php echo $_SESSION['token'];?>;

var globalid;

var typedict = {
	"salarie": "Salarié",
	"etudiant": "Etudiant",
	"moniteur": "Moniteur"
}

function addmoniteur() {
	$("#repMoniteur").empty();
	if ($("#passwd1").val() != $("#passwd2").val()) {
		$("#repMoniteur").append("<ul><li><font color='red'>Vous n'avez pas rentré deux fois le même mot de passe</font></li></ul>");
		return;
	}
	$.post(
		'/POST/controleur_admin.php',
		{
			action: "addmoniteur",
			prenom: $("#prenom").val(),
			nom: $("#nom").val(),
			passwd: $("#passwd1").val(),
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#repMoniteur").append("<font color='green' size='4'>Création du compte réussi!</font>");
				setTimeout(function(){ location.href = "/admin.php"; }, 1000);
			} else if (data.rep == "failed") {
				$("#repMoniteur").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#repMoniteur").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#repMoniteur").append("</ul>");
			}
		},
		'json'
	);
}

function ban(id) {
	$("#ban").empty();
	if (typeof(users['id-'+id]) == "undefined") {
		$("#ban").append("<font color='red' size='4'>Cet utilisateur n'existe pas</font>");
		return;
	}
	if (!confirm("Etes vous sûre de vouloir bannir cet utilisateur?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_admin.php',
		{
			action: "ban",
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#ban").append("<font color='green' size='4'>Bannissement réussi!</font>");
				$("#buttonban_"+globalid).val("Dé-bannir");
				$("#buttonban_"+globalid).attr("onclick","unban("+globalid+")");
			} else if (data.rep == "failed") {
				$("#ban").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#ban").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#ban").append("</ul>");
			}
		},
		'json'
	);
}

function unban(id) {
	$("#ban").empty();
	if (typeof(users['id-'+id]) == "undefined") {
		$("#ban").append("<font color='red' size='4'>Cet utilisateur n'existe pas</font>");
		return;
	}
	if (!confirm("Etes vous sûre de vouloir dé-bannir cet utilisateur?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_admin.php',
		{
			action: "unban",
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#ban").append("<font color='green' size='4'>Dé-bannissement réussi!</font>");
				$("#buttonban_"+globalid).val("Bannir");
				$("#buttonban_"+globalid).attr("onclick","ban("+globalid+")");
			} else if (data.rep == "failed") {
				$("#ban").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#ban").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#ban").append("</ul>");
			}
		},
		'json'
	);
}

function fiche(id) {
	$("#fiche").empty();
	$("#ban").empty();
	if (typeof(users['id-'+id]) == "undefined") {
		$("#fiche").append("<font color='red' size='4'>Cet utilisateur n'existe pas</font>");
		return;
	}
	var html = "";
	html += users['id-'+id].prenom+" "+users['id-'+id].nom+" ["+typedict[users['id-'+id].type]+"] : <br><br>";
	html += "Adresse mail : "+users['id-'+id].mail+"<br>";
	if (users['id-'+id].type == "etudiant" | users['id-'+id].type == "salarie") {
		html += "Adresse : "+users['id-'+id].addr+"<br>";
		html += "Date de naissance : "+users['id-'+id].dateN+"<br>";
		html += "Date d'inscription: "+users['id-'+id].dateI+"<br>";
		html += "Numéro de télephone : "+users['id-'+id].numtel+"<br>";
		html += "Mode de facturation : "+users['id-'+id].facturation+"<br>";
		if (users['id-'+id].type == "etudiant") {
			html += "Niveau d'étude : "+users['id-'+id].etude+"<br>";
			html += "Réduction : "+users['id-'+id].reduction+"<br>";
		} else if (users['id-'+id].type == "salarie") {
			html += "Entreprise : "+users['id-'+id].entreprise+"<br>";
		}
	} else if (users['id-'+id].type == "moniteur") {
		html += "Date d'embauche : "+users['id-'+id].dateE+"<br>";
	}
	$("#fiche").append(html);
}
</script>