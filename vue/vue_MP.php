<style>
#background {
	height: 200vH;
}
</style>
<!-- <table class="tableborder">
	<tr>
		<td>
			<table id="listmp">
			</table>
		</td>
		<td>
			<table class="tablenoborder" id="displaymsg">
				<tr>
					<td>
							<font color="orange" size="4">Personne n'est sélectioné</font>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>-->
<br/>
<br><br>
<h1>Messages privés : </h1>
<div class="container">
<span type="button" class="btn btn-info active" value='Boite de reception' onclick='listmp()'>Boite de reception</span>
<span type="button" class="btn btn-info active" value='Messages envoyés' onclick='listms()'>Boite d'envoi</span>
</div>
<br><br>
<center>
<table>
	<tr>
		<td id='display'></td>
	</tr>
</table>
<br>
<h2>Envoyer un message</h2>
<br>
<form style="width: 500px; border: 1px #000000 solid;">
à qui : 
<div id="dstsend">
</div><br>
L'objet : <input type="text" id="objet"><br>
Le message : <textarea style="width: 450px; height: 300px" id="content"></textarea><br>
<div id="error_send"></div>
<input type="button" onclick="sendmp()" value="Envoyer">
</form>
</center>
<script>
var oldCompteChoiced;
var nbSelect = 0;
var comptes = [];
var comptesAlreadyChoiced = [];
var MPs = [];
var MSs = [];
<?php
echo $controleur->getmp($_SESSION['id']);
?>
var token = <?php echo $_SESSION['token'];?>;

var globalid;

listmp();

function listmp() {
	$("#display").empty();
	if (MPs.length == 0) {
		$("#display").append("<font color='blue' size='4'>Aucun message</font>");
	} else {
		var html = "<center style='font-size: 20px;'>Boite de reception:</center>";
		html += "<table>";
		for (var i=0;i<MPs.length;i++) {
			html += "<tr>";
			html += "<td>";
			if (MPs[i].lu == 0) {
				html += "<strong>";
			}
			html += "["+MPs[i].datetime+"] de "+MPs[i].prenom+" "+MPs[i].nom+" ("+MPs[i].objet+")";
			if (MPs[i].lu == 0) {
				html += "</strong>";
			}
			html += "<input type='button' onclick='displaymp("+i+")' value='Voir le message'>"; 
			html += "<input type='button' value='Supprimer' onclick='supprmp("+i+")'><div id='error_suppr_"+i+"'></div>";
			html += "</td>";
			html += "</tr>";
		}
		html += "</table>";
		$("#display").append(html);
	}
}

function listms() {
	$("#display").empty();
	if (MSs.length == 0) {
		$("#display").append("<font color='blue' size='4'>Aucun message envoyé</font>");
	} else {
		var html = "<center style='font-size: 20px;'>Message envoyés</center>";
		html += "<table>";
		for (var i=0;i<MSs.length;i++) {
			html += "<tr>";
			html += "<td>";
			html += "["+MSs[i].datetime+"] à "+MSs[i].prenom+" "+MSs[i].nom+" ("+MSs[i].objet+") <input type='button' onclick='displayms("+i+")' value='Voir le message'>";
			html += "<input type='button' value='Supprimer' onclick='supprms("+i+")'><div id='error_suppr_"+i+"'></div>";
			html += "</td>";
			html += "</tr>";
		}
		html += "</table>";
		$("#display").append(html);
	}
}

function displaymp(i) {
	$("#display").empty();
	if (typeof(MPs[i]) == "undefined") {
		$("#display").append("<input type='button' value='<==' onclick='listmp()'><br><font color='orange' size='4'>Aucun message à ce nom</font>");
	} else {
		setLu(i);
		var html = "<input type='button' value='<==' onclick='listmp()'><br>["+MPs[i].datetime+"]";
		html += MPs[i].content;
		html += "<input type='button' value='Supprimer' onclick='supprmp("+i+")'><br><div id='error_suppr_"+i+"'>";
		$("#display").append(html);
	}
}

function displayms(i) {
	$("#display").empty();
	if (typeof(MSs[i]) == "undefined") {
		$("#display").append("<input type='button' value='<==' onclick='listmp()'><br><font color='orange' size='4'>Aucun message à ce nom</font>");
	} else {
		var html = "<input type='button' value='<==' onclick='listms()'><br>["+MSs[i].datetime+"]";
		html += MSs[i].content;
		html += "<input type='button' value='Supprimer' onclick='supprms("+i+")'><br><div id='error_suppr_"+i+"'>";
		$("#display").append(html);
	}
}

function supprmp(i) {
	if (!confirm("Êtes vous sûre de vouloir suprimmer ce message ?")) {
		return;
	}
	$.post(
			'/POST/controleur_mp.php',
			{
				action: "suppr",
				id: MPs[i].id,
				token: token
			},

			function(data){
				if (data.rep == "success") {
					$("#error_suppr_"+i).empty();
					location.href = "/MP.php";
				} else if (data.rep == "failed") {
					$("#error_suppr_"+i).empty();
					$("#error_suppr_"+i).append("<ul>");
					for (var j=0;i<data.errors.length;i++) {
						$("#error_suppr_"+i).append("<li><font color='red'>"+data.errors[j]+"</font></li>");
					}
					$("#error_suppr_"+i).append("</ul>");
				}
			},
			'json'
		);
}

function supprms(i) {
	if (!confirm("Êtes vous sûre de vouloir suprimmer ce message ?")) {
		return;
	}
	$.post(
			'/POST/controleur_mp.php',
			{
				action: "suppr",
				id: MSs[i].id,
				token: token
			},

			function(data){
				if (data.rep == "success") {
					$("#error_suppr_"+i).empty();
					location.href = "/MP.php";
				} else if (data.rep == "failed") {
					$("#error_suppr_"+i).empty();
					$("#error_suppr_"+i).append("<ul>");
					for (var j=0;i<data.errors.length;i++) {
						$("#error_suppr_"+i).append("<li><font color='red'>"+data.errors[j]+"</font></li>");
					}
					$("#error_suppr_"+i).append("</ul>");
				}
			},
			'json'
		);
}

function sendmp() {
	var destinataires = $("#dstsend").find("SELECT");
	var destinatairesString = "";
	for (var i=0;i<destinataires.length;i++) {
		destinatairesString += destinataires[i].value;
		if (i < destinataires.length-1) {
			if (destinataires[i+1].value != "") {
				destinatairesString += ",";
			}
		}
	}
	$.post(
			'/POST/controleur_mp.php',
			{
				action: "send",
				dst: destinatairesString,
				objet : $("#objet").val(),
				content: $("#content").val(),
				token: token
			},

			function(data){
				if (data.rep == "success") {
					$("#error_send").empty();
					$("#dstsend").val("");
					$("#objet").val("");
					$("#content").val("");
					$("#error_send").append("<font color='green'>message envoyé</font>");
					setTimeout(function () { location.href = "/MP.php"; },1000);
				} else if (data.rep == "failed") {
					$("#error_send").empty();
					$("#error_send").append("<ul>");
					for (var i=0;i<data.errors.length;i++) {
						$("#error_send").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
					}
					$("#error_send").append("</ul>");
				}
			},
			'json'
		);
}

function setLu(i) {
	globalid = i;
	$.post(
			'/POST/controleur_mp.php',
			{
				action: "setLu",
				id: MPs[i].id,
				token: token
			},

			function(data){
				if (data.rep == "success") {
					MPs[globalid].lu = 1;
					$("#MPa").attr('nb',parseInt($("#MPa").attr('nb'))-1);
					if (parseInt($("#MPa").attr('nb')) > 0) {
						$("#MPa").html("Messages privés  ("+$("#MPa").attr('nb')+")");
					} else {
						$("#MPa").html("Messages privés");
					}
				} else if (data.rep == "failed") {
					console.log("Error setLu() : ");
					for (var i=0;i<data.errors.length;i++) {
						console.log(data.errors[i]);
					}
				}
			},
			'json'
		);
}

function nextDst(nbSelectTmp) {
	if ($("#select"+nbSelectTmp).val() != "" & !(isAlreadyChoiced(parseInt($("#select"+nbSelectTmp).val()))) &
		nbSelect == nbSelectTmp) {
		var compteChoiced = parseInt($("#select"+nbSelectTmp).val());
		oldCompteChoiced = compteChoiced;
		comptesAlreadyChoiced.push(compteChoiced);
		createDstSelect();
	} else if (nbSelect > nbSelectTmp & $("#select"+nbSelectTmp).val() != "") {
		var compteChoiced = parseInt($("#select"+nbSelectTmp).val());
		comptesAlreadyChoiced.push(compteChoiced);
		$("#select"+nbSelect).remove();
		nbSelect -= 1;
		deleteFromAlreadyChoiced(oldCompteChoiced);
		oldCompteChoiced = compteChoiced;
		createDstSelect();
	}
}

/*nbSelect = 1;
nbSelectTmp = 1;*/

function createDstSelect() {
	if (comptesAlreadyChoiced.length == comptes.length) {
		return;
	}
	nbSelect += 1;
	var selectString = "\n<div><SELECT id='select"+nbSelect+"' onchange='nextDst("+nbSelect+")'>";
	selectString += "\n<option value=''>Choisir</option>";
	for(var i=0;i<comptes.length;i++) {
		if (!isAlreadyChoiced(comptes[i].id)) {
			selectString += "\n<option value='"+comptes[i].id+"'>"+comptes[i].prenom+" "+comptes[i].nom+"</option>";
		}
	}
	selectString += "\n</SELECT></div>";
	$("#dstsend").append(selectString);
}

function isAlreadyChoiced(id) {
	for (var i=0;i<comptesAlreadyChoiced.length;i++) {
		if (id == comptesAlreadyChoiced[i]) {
			return true;
		}
	}
	return false;
}

function deleteFromAlreadyChoiced(id) {
	for (var i=0;i<comptesAlreadyChoiced.length;i++) {
		if (id == comptesAlreadyChoiced[i]) {
			comptesAlreadyChoiced.splice(i,1);
		}
	}
}

<?php
$listco = $controleur->listcomptes();
if (gettype($listco) == "string") {
	echo $listco;
} else {
	foreach($listco as $compte) {
		if ($compte['id'] != $_SESSION['id'] & 
		    (($_SESSION['perm'] != "superadmin" & $compte['perms'] != "superadmin" & $compte["status"] != "fromContact") | 
			 ($_SESSION['perm'] == "superadmin" & $compte["status"] != "fromContact"))) {
			echo ("\ncomptes.push({id: ".$compte['id'].", prenom: '".$compte['prenom']."', nom: '".$compte['nom']."'});");
		}
	}
}
echo ("\ncreateDstSelect();");
?>

</script>