function devalider(i) {
	if (!confirm("Etes vous sûre de vouloir de-valider ce rendez-vous ?")) {
		return;
	}
	indexrdv = i;
	$.post(
		'/POST/controleur_rendezvous.php',
		{
			action: "devalid",
			type: rendez_vous[currentrdv][i].type,
			id: rendez_vous[currentrdv][i].id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#devalider_result_"+indexrdv).empty();
				$("#devalider_result_"+indexrdv).append("<font color='green'>Rendez-vous de-validé!</font>");
				displayyear(currentrdv.split("-")[0]);
				displaymonth(currentrdv.split("-")[1]+"/"+currentrdv.split("-")[0]);
				rendez_vous[currentrdv][indexrdv].validated = "no";
				if (isntvalidated()) {
					$("#"+currentrdv+"_year").css("background-color","orange");
					$("#"+currentrdv+"_month").css("background-color","orange");
					$("#"+currentrdv+"_year a font").css("color","red");
					$("#"+currentrdv+"_month a font").css("color","red");
					ansL[currentyear] = $("#displayyear").html();
					moisL[currentmonth+"-"+currentyearofmonth] = $("#displaymonth").html();
				}
				display_rendez_vous(currentrdv);
			} else if (data.rep == "failed") {
				$("#devalider_result_"+indexrdv).empty();
				$("#devalider_result_"+indexrdv).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#devalider_result_"+indexrdv).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#devalider_result_"+indexrdv).append("</ul>");
			}
		},
		'json'
	);
}

function isntvalidated() {
	for (var i=0;i<rendez_vous[currentrdv].length;i++) {
		if (rendez_vous[currentrdv][i].validated == "yes") {
			return false;
		}
	}
	return true;
}