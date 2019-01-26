function valider(i) {
	if (!confirm("Etes vous sûre de vouloir valider ce rendez-vous ?")) {
		return;
	}
	indexrdv = i;
	$.post(
		'/POST/controleur_rendezvous.php',
		{
			action: "valid",
			type: rendez_vous[currentrdv][i].type,
			id: rendez_vous[currentrdv][i].id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				//console.log("before:");
				//displayid();
				$("#valider_result_"+indexrdv).empty();
				$("#valider_result_"+indexrdv).append("<font color='green'>Rendez-vous validé!</font>");
				displayyear(currentrdv.split("-")[0]);
				displaymonth(currentrdv.split("-")[1]+"/"+currentrdv.split("-")[0]);
				rendez_vous[currentrdv][indexrdv].validated = "yes";
				$("#"+currentrdv+"_year").css("background-color","green");
				$("#"+currentrdv+"_month").css("background-color","green");
				$("#"+currentrdv+"_year a font").css("color","red");
				$("#"+currentrdv+"_month a font").css("color","red");
				ansL[currentyear] = $("#displayyear").html();
				moisL[currentmonth+"-"+currentyearofmonth] = $("#displaymonth").html();
				display_rendez_vous(currentrdv);
				//console.log("after:");
				//displayid();
			} else if (data.rep == "failed") {
				$("#valider_result_"+indexrdv).empty();
				$("#valider_result_"+indexrdv).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#valider_result_"+indexrdv).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#valider_result_"+indexrdv).append("</ul>");
			}
		},
		'json'
	);
}
function displayid() {
	for (var i=0;i<rendez_vous[currentrdv].length;i++) {
		console.log(rendez_vous[currentrdv][i].id);
	}
}