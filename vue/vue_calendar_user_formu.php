<br>
<strong>(Ouvert entre entre 08:00 et 12:00 et entre 14:00 et 19:00)</strong>
<br><br>
<h3>Ajouter un rendez-vous :</h3>
<br>
<form>
	Quel type de rendez vous ? : <SELECT onchange="FormType()" id="type">
		<option value="">Choisir</option>
		<option value="planning">Cours</option>
		<option value="exam">Exam</option>
	</SELECT><br>
	<div id="formu">
	</div>
</form>
<br>
<br>
<br>
<br>
<br>
<br>

<script>

function FormType() {
	var string;
	$("#formu").empty();
	if ($("#type").val() == "planning") {
		$("#formu").append("la date du cours : <input type='date' id='date' min='<?php echo date("Y-m-d"); ?>'><br>");
		$("#formu").append("de quel heure ? : <input type='time' id='heureD'><br>");
		$("#formu").append("à quel heure ? : <input type='time' id='heureF'><br>");
		$("#formu").append("quelle leçon ? : ");
		string = "<SELECT id='lecon'>";
		for (var id in lecons) {
			string += "<option value='"+id+"'>"+lecons[id].nom+" ("+lecons[id].prix+"€/h)</option>";
		}
		string += "</SELECT><br>";
		$("#formu").append(string);
		$("#formu").append("<input type='button' onclick='sendform()' id='submit' value='Ajouter'>");
		$("#formu").append("<div id='formuinfo'></div>");
		
	} else if ($("#type").val() == "exam") {
		$("#formu").append("la date de l'exam : <input type='date' id='date' min='<?php echo date("Y-m-d"); ?>'><br>");
		$("#formu").append("à quel heure ? : <input type='time' id='heure'><br>");
		$("#formu").append("quel type d'exam ? : ");
		string = "<SELECT id='typeexam'>";
		for (var id in typeexams) {
			string += "<option value='"+id+"'>"+typeexams[id].type+" ("+typeexams[id].prix+"€)</option>";
		}
		string += "</SELECT><br>";
		$("#formu").append(string);
		$("#formu").append("<input type='button' onclick='sendform()' id='submit' value='Ajouter'>");
		$("#formu").append("<div id='formuinfo'></div>");
	}
}


 
function sendform() {
		
	var requests;
	if ($("#type").val() == "planning") {
		requests = 
		{
			action: "add",
			type: "planning",
			date: $("#date").val(),
			heureD: $("#heureD").val(),
			heureF: $("#heureF").val(),
			lecon: $("#lecon").val(),
			token: token
		};
	} else if ($("#type").val() == "exam") {
		requests = 
		{
			action: "add",
			type: "exam",
			date: $("#date").val(),
			heure: $("#heure").val(),
			typeexam: $("#typeexam").val(),
			token: token
		};
	}
 
    $.post(
		'/POST/controleur_rendezvous.php',
		requests,

		function(data){
			if (data.rep == "success") {
				$("#formuinfo").empty();
				$("#formuinfo").append("<font color='green'>Rendez-vous créé!</font>");
				displayyear($("#date").val().split("-")[0]);
				if ($("#"+$("#date").val()+"_year").css("background-color") != "green") {
					$("#"+$("#date").val()+"_year").css("background-color","orange");
					$("#"+$("#date").val()+"_year font").css("color","red");
					var newcontenttd = "<a href='javascript:display_rendez_vous(\""+$("#date").val()+"\");'";
					newcontenttd += " title=\"Voir le rendez vous du "+$("#date").val().split("-")[2]+"/"+$("#date").val().split("-")[1]+"/"+$("#date").val().split("-")[0]+"\">"+$("#"+$("#date").val()+"_year").html()+"</a>";
					$("#"+$("#date").val()+"_year").empty();
					$("#"+$("#date").val()+"_year").append(newcontenttd);
					ansL[currentyear] = $("#displayyear").html();
				}
				if ($("#"+$("#date").val()+"_month").css("background-color") != "green") {
					$("#"+$("#date").val()+"_month").css("background-color","orange");
					$("#"+$("#date").val()+"_month font").css("color","red");
					var newcontenttd = "<a href='javascript:display_rendez_vous(\""+$("#date").val()+"\");'";
					newcontenttd += " title=\"Voir le rendez vous du "+$("#date").val().split("-")[2]+"/"+$("#date").val().split("-")[1]+"/"+$("#date").val().split("-")[0]+"\">"+$("#"+$("#date").val()+"_month").html()+"</a>";
					$("#"+$("#date").val()+"_month").empty();
					$("#"+$("#date").val()+"_month").append(newcontenttd);
					moisL[currentmonth+"/"+currentyearofmonth] = $("#displaymonth").html();
				}
				if (typeof(rendez_vous[$("#date").val()]) == "undefined") {
					rendez_vous[$("#date").val()] = [];
				}
				if ($("#type").val() == "planning") {
					rendez_vous[$("#date").val()].push( 
					{
					id: data.id, validated: 'no', type: 'Planning', debut: $("#date").val()+" "+$("#heureD").val()+":00", 
					fin: $("#date").val()+" "+$("#heureF").val()+":00",
					Lecon: lecons[$("#lecon").val()].nom, prix: lecons[$("#lecon").val()].prix*diffheure($("#heureD").val(),$("#heureF").val())
					});
				} else if ($("#type").val() == "exam") {
					console.log("id : "+data.id);
					rendez_vous[$("#date").val()].push(
					{
					id: data.id, validated: 'no', type: 'Exam', debut: $("#heure").val()+":00", fin: parseInt($("#heure").val().split(":")[0])+2+":"+$("#heure").val().split(":")[1]+":00",
					typeexam: typeexams[$("#typeexam").val()].type, prix: typeexams[$("#typeexam").val()].prix
					});
				}
				display_rendez_vous($("#date").val());
			} else if (data.rep == "failed") {
				$("#formuinfo").empty();
				$("#formuinfo").append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#formuinfo").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#formuinfo").append("</ul>");
			}
		},
		'json'
	);
}

function diffheure(heureA,heureB) {
	var minutediff = (parseInt(heureB.split(":")[0])-parseInt(heureA.split(":")[0]))*60+(parseInt(heureB.split(":")[1])-parseInt(heureA.split(":")[1]));
	var heurediff = minutediff/60;
	return heurediff;
}

function availables() {
	if ($("#availables").css("display") == "none") {
		$("#availables").css("display","block");
	} else {
		$("#availables").css("display","none");
	}
}

var lecons = {};
var typeexams = {};
<?php
$listlecon = $controleur->listoflecons();

foreach($listlecon as $lecon)
{
	echo ("lecons[".(int)$lecon['id']."] = {nom: '".$lecon['nom']."', prix: '".$lecon['tarif']."'};");
}

$listexam = $controleur->listofexams();

foreach ($listexam as $exam)
{
	echo ("typeexams[".(int)$exam['id']."] =  {type: '".$exam['nom']."', prix: '".$exam['prix']."'};");
}

if (isset($_GET['type']) & isset($_GET['id'])) {
	?>
	$("#type").val("<?php echo $_GET['type'] ; ?>");
	FormType();
	<?php
		if ($_GET['type'] == "planning") {
			echo ("$('#lecon').val('".$_GET['id']."');\n");
		} else if ($_GET['type'] == "exam") {
			echo ("$('#typeexam').val('".$_GET['id']."');\n");
		}
		echo ("setTimeout(function(){ $('#tonameofyear').click(); }, 500);");
}
?>
</script>