<style>
#background {
	height: 150vH;
}
</style>
<br>
<br>
<br>
<center>
<font size="5" >Incription : </font>
<br>
<br>
<form style="border: 2px solid black; width: 475px;" id="form" method="POST" action="/POST/controleur_register.php">
<br>
Prénom : <input type="text" id="prenom" name="prenom"><div id="prenomerror" ></div><br>
Nom : <input type="text" id="nom" name="nom"><div id="nomerror" ></div><br>
Type de client : <SELECT onchange="InputType()" id="type" name="type">
					<option value="">Choisissez</option>
					<option value="etudiant">Etudiant(e)</option>
					<option value="salarie">Salarié(e)</option>
				 </SELECT><div id="typeerror" ></div><br>
<div id="InputofType"></div><br>
Adresse : <input type="text" id="addr" name="addr"><div id="adresseerror" ></div><br>
Date de naissance : <input type="date" id="dateN" name="dateN"><div id="dateerror" ></div><br>
Numéro de télephone : <input type="text" id="numtel" name="numtel"><div id="numerror"></div><br>
Mode de facturation : <input type="text" id="facturation" name="facturation"><div id="facturationerror" ></div><br>
Mot de passe : <input type="password" name="passwd" id="passwd1"><div id="passworderror"></div><br>
Rentrez le une deuxième fois : <input type="password" id="passwd2"><br><br>
Adresse mail (en cas d'oublie de mot de passe) : <input type="text" id="mail" name="mail"><div id="mailerror" ></div>
<br>
<input onclick="sendform()" type="button" value="Incription">
<br><br>
</form>
</center>
</body>
<script>

onkeypress = function(e){
    if(e.charCode == 13){
        sendform();
    }
}

function complete(str,num) {
	if (typeof(str) != "String") {
		str = str.toString();
	}
	while(str.length < num) {
		str = "0"+str;
	}
	return str;
}

function sendform() {
	var date = new Date();
	var curdate = parseInt(date.getFullYear()+complete(date.getMonth()+1,2)+complete(date.getDate(),2));
	var regexnum = new RegExp("^((0|0033)[1-68])([0-9]{8})$");
	var regexdate = new RegExp("^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$");
	var regexmail = new RegExp("^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$");
	$("#numerror").empty();
	$("#prenomerror").empty();
	$("#nomerror").empty();
	$("#adresseerror").empty();
	$("#numerror").empty();
	$("#dateerror").empty();
	$("#passworderror").empty();
	$("#facturationerror").empty();
	$("#typeerror").empty();
	$("#mailerror").empty();
	var error = false;
	if ($("#numtel").val() == "") {
		error = true;
		$("#numerror").append("<font color='red'>Veuillez mettre votre numéro de télephone</font>");
	} else if (!regexnum.test($("#numtel").val().replace(" ","").replace("+","00"))) {
		error = true;
		$("#numerror").append("<font color='red'>Format du numéro de téléphone incorrect</font>");
	}
	if ($("#passwd1").val() != $("#passwd2").val()) {
		error = true;
		$("#passworderror").append("<font color='red'>Vous n'avez pas rentré deux fois le même mot de passe</font>");
	} else if ($("#passwd1").val() == "") {
		error = true;
		$("#passworderror").append("<font color='red'>Veuillez mettre un mot de passe</font>");
	}
	if ($("#prenom").val() == "") {
		error = true;
		$("#prenomerror").append("<font color='red'>Veuillez mettre un prénom</font>");
	} else if ($("#prenom").val().length > 30){
		error = true;
		$("#prenomerror").append("<font color='red'>Le prenom doit faie moins de 30</font>");
	}
	if ($("#nom").val() == "") {
		error = true;
		$("#nomerror").append("<font color='red'>Veuillez mettre un nom</font>");
	} else if ($("#nom").val().length > 30){
		error = true;
		$("#nomerror").append("<font color='red'>Le nom doit faie moins de 30</font>");
	}
	if ($("#addr").val() == "") {
		error = true;
		$("#adresseerror").append("<font color='red'>Veuillez mettre une adresse</font>");
	} else if ($("#addr").val().length >  50) {
		error = true;
		$("#adresseerror").append("<font color='red'>L'adresse doit prendre moins 50 caractères</font>");
	}
	if (!regexdate.test($("#dateN").val())) {
		error = true;
		$("#dateerror").append("<font color='red'>Format de la date incorrect</font>");
	} else if (parseInt(remplace($("#dateN").val(),"-","")) >= curdate) {
		error = true;
		$("#dateerror").append("<font color='red'>Veuillez mettre une date de naissance qui soit anterieure à la date actuelle</font>");
	}
	if (!regexmail.test($("#mail").val())) {
		error = true;
		$("#mailerror").append("<font color='red'>Format du mail incorrect</font>");
	} else if ($("#mail").val().length > 70) {
		error = true;
		$("#mailerror").append("<font color='red'>La longueur de l'adresse mail ne doit pas dépasser 70 caractères</font>");
	}
	if ($("#facturation").val() == "") {
		error = true;
		$("#facturationerror").append("<font color='red'>Veuillez mettre mode de facturation</font>");
	} else if ($("#facturation").val().length > 15) {
		error = true;
		$("#facturationerror").append("<font color='red'>Le mode de facturation doit prendre moins de 15 caractères</font>");
	}
	if ($("#type").val() != "etudiant" & $("#type").val() != "salarie") {
		error = true;
		$("#typeerror").append("<font color='red'>Veuillez choisir un type</font>");
	}
	
	if ($("#type").val() == "etudiant") {
		$("#niveauerror").empty();
		$("#reducerror").empty();
		if ($("#niveau").val() == "") {
			error = true;
			$("#niveauerror").append("<font color='red'>Veuillez spécifier votre niveau d'études</font>");
		} else if ($("#niveau").val().length > 128) {
			error = true;
			$("#niveauerror").append("<font color='red'>Le niveau d'etude doit prendre moins de 128 caractères</font>");
		}
		if ($("#reduc").val() == "") {
			error = true;
			$("#reducerror").append("<font color='red'>Veuillez mettre la réduction</font>");
		} else if ($("#reduc").val().length > 3) {
			error = true;
			$("#reducerror").append("<font color='red'>La reduction ne doit prendre que 3 caractères (ex : 10%)</font>");
		} 
	} else if ($("#type").val() == "salarie") {
		$("#entrepriseerror").empty();
		if ($("#entreprise").val() == "") {
			error = true;
			$("#entrepriseerror").append("<font color='red'>Veuillez spécifier le nom de votre entreprise</font>");
		} else if ($("#entreprise").val().length > 128) {
			error = true;
			$("#entrepriseerror").append("<font color='red'>Le nom de l'entreprise doit prendre moins de 128 caractères</font>");
		}
	}
	
	if (error == false) {
		$("#form").submit();
	}
}

function InputType() {
	$("#InputofType").empty();
	if ($("#type").val() == "etudiant") {
		$("#InputofType").append("Rentre ton niveau d'études : <input type='text' name='niveau' id='niveau'><div id='niveauerror' ></div><br>");
		$("#InputofType").append("Rentre la reduction : <input type='text' name='reduc' id='reduc'><div id='reducerror' ></div><br>");
	} else if ($("#type").val() == "salarie") {
		$("#InputofType").append("Rentre le nom de ton entreprise : <input type='text' name='entreprise' id='entreprise'><div id='entrepriseerror' ></div><br>");
	}
}

function remplace(str,A,B) {
	while(str.replace(A,B) != str) {
		str = str.replace(A,B);
	}
	return str;
}

</script>