<br>
<br>
<br>
<center>
<a class="lien petit" href="/login.php">Connexion</a>
<br>
<br>
<font size="5" >Rentrez le prénom et le nom que vous utilisez sur ce site : </font>
<br>
<br>
Votre prenom : <input type="text" id="prenom"><br>
Votre nom : <input type="text" id="nom"><br>
<input type='button' value="Envoyer" onclick="sendMail()">
<div id="info"></div><br>
<script>


function sendMail() {
	if (!confirm("Etes vous sûre?")) {
		return;
	}
	$("#info").empty();
	$("#info").append("<font color='green'>Un mail contenant un nouveau mot de passe pour votre compte<br>va être envoyé à votre adresse mail<br>si vous ne recevez rien, n'hésitez pas à envoyer un mail à : directeur.castelane@outlook.com</font>");
	$.post(
			'/POST/controleur_forgotpassword.php',
			{
				prenom: $("#prenom").val(),
				nom: $("#nom").val()
			},

			function(data){
				if (data.rep == "success") {
					$("#info").empty();
					$("#info").append("<font color='green'>Un mail contenant un nouveau mot de passe pour votre compte<br>va être envoyé à votre adresse mail<br>si vous ne recevez rien, n'hésitez pas à envoyer un mail à : directeur.castelane@outlook.com</font>");
				} else if (data.rep == "failed") {
					$("#info").empty();
					$("#info").append("<ul>");
					for (var i=0;i<data.errors.length;i++) {
						$("#info").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
					}
					$("#info").append("</ul>");
				}
			},
			'json'
		);
}
</script>