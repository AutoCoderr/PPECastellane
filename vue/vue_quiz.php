<div id="question">
</div>
<div id="info"></div>
<input id="leBouton" type='button' value='Valider' onclick="valid()">
<script>
var quiz = [];
<?php 

echo $controleur->getQuiz();
$_SESSION['currentQuestion'] = 1;
$_SESSION['note'] = 0;


?>

var token = <?php echo $_SESSION['token']; ?>;

var currentQuestion = -1;

nextQuestion();

function valid() {
	/*if (!confirm("Êtes vous sûre ?")) {
		return;
	}*/
	if (typeof($("input[name='reponse']:checked").val()) == "undefined") {
		$("#info").empty();
		$("#info").append("<font color='red' size='3'>Aucune réponse sélectionnée</font>");
		return;
	}
	$.post(
		'/POST/controleur_quiz.php',
		{
			idQuestion: quiz[currentQuestion].question.id,
			idReponse: parseInt($("input[name='reponse']:checked").val())
		},

		function(data){
			if (data.rep == "next") {
				nextQuestion();
			} else if (data.rep == "finish") {
				$("#question").empty();
				$("#question").append("<font color='green' size='5'>Vous avez finis le quiz !<br>Votre note : "+data.note+"/"+quiz.length+"</font>");
				$("#leBouton").val("Page d'acceuil");
				$("#leBouton").attr("onclick","location.href = '/index.php'");
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

function nextQuestion() {
	currentQuestion += 1;
	$("#question").empty();
	if (typeof(quiz[currentQuestion]) == "undefined") {
		$("#question").append("\n<font color='red' size='5'>Question introuvable</font>");
	}
	$("#question").append("<font size='5' color='yellow'>Question N°"+(currentQuestion+1)+"</font><br>");
	$("#question").append("<img style='width: 200px; height: 200px;' src='/img/quiz/"+quiz[currentQuestion].question.id+".jpg'/><br>");
	$("#question").append("\n<font size='4' color='blue'>"+quiz[currentQuestion].question.question+"</font><br><br>");
	var reponses = "";
	for (var i=0;i<quiz[currentQuestion].reponses.length;i++) {
		reponses += "\n<input name='reponse' type='radio' value='"+quiz[currentQuestion].reponses[i].id+"'>"+quiz[currentQuestion].reponses[i].reponse;
		if (i < quiz[currentQuestion].reponses.length-1) {
			reponses += "<br>";
		}
	}
	$("#question").append(reponses);
}

</script>