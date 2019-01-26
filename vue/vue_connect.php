<br>
<br>
<br>
<center>
<font size="5" >Se connecter : </font>
<br>
<br>
<form style="border: 2px solid black; width: 475px;">
<br>
Prénom : <input type="text" id="prenom"><br>
Nom : <input type="text" id="nom"><br>
Mot de passe : <input type="password" id="passwd"><br>
<div id="error"></div><br>
<input type="button" id="submit" value="Connexion">
<br>
<a class="lien petit" href="/forgotpassword.php">Mot de passe oublié?</a>
</center>
</body>
<script>

onkeypress = function(e){
    if(e.charCode == 13){
        $("#submit").click();
    }
}

$(document).ready(function(){
 
    $("#submit").click(function(e){
        e.preventDefault();
 
        $.post(
			'/POST/controleur_connect.php',
			{
				prenom: $("#prenom").val(),
				nom: $("#nom").val(),
				passwd: $("#passwd").val()
			},

			function(data){
				if (data.rep == "success") {
					$("#error").empty();
					alert("Connexion reussi!");
					location.href = "/index.php";
				} else if (data.rep == "failed") {
					$("#error").empty();
					$("#error").append("<ul>");
					for (var i=0;i<data.errors.length;i++) {
						$("#error").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
					}
					$("#error").append("</ul>");
				}
			},
			'json'
		);
    });
});


</script>