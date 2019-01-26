<style>
#background {
	height: 200vH;
}
</style>
<h1>Vos informations utilisateur : </h1>
<br>
<?php
echo $controleur->getOwnFiche();
?>
<br><br><br><br>
<h3>Changer votre mot de passe :</h3>
<br>
Entrez le une 1ère fois : <input id="passwd1" type="password"><br>
Entrez le une 2ème fois : <input id="passwd2" type="password"><br>
<input type="button" value="Valider" onclick="changePasswd()">
<br>
<div id="changePasswd"></div>

<script>
var token = <?php echo $_SESSION['token'];?>;

function changePasswd() {
	if ($("#passwd1").val() != $("#passwd2").val()) {
		$("#changePasswd").empty();
		$("#changePasswd").append("<font color='red' size='3'>Vous n'avez pas rentré deux fois le même mot de passe</font>");
		return;
	}
	if (!confirm("Êtes vous sûre de vouloir changer votre mot de passe ?")) {
		return;
	}
	
	$.post(
		'/POST/controleur_user.php',
		{
			action: "changePasswd",
			passwd: $("#passwd1").val(),
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#changePasswd").empty();
				$("#changePasswd").append("<font color='green'>Mot de passe modifier avec succès!</font>");
				$("#passwd1").val("");
				$("#passwd2").val("");
			} else if (data.rep == "failed") {
				$("#changePasswd").empty();
				$("#changePasswd").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#changePasswd").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#changePasswd").append("</ul>");
			}
		},
		'json'
	);
}
</script>