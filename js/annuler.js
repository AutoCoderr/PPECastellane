function annuler(i) {
	if (!confirm("Etes vous sûre de vouloir suprimmer ce rendez-vous ?")) {
		return;
	}
	indexrdv = i;
	$.post(
		'/POST/controleur_rendezvous.php',
		{
			action: "delete",
			type: rendez_vous[currentrdv][i].type,
			id: rendez_vous[currentrdv][i].id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#annuler_result_"+indexrdv).empty();
				$("#annuler_result_"+indexrdv).append("<font color='green'>Rendez-vous annulé!</font>");
				displayyear(currentrdv.split("-")[0]);
				displaymonth(currentrdv.split("-")[1]+"/"+currentrdv.split("-")[0]);
				rendez_vous[currentrdv].splice(indexrdv,indexrdv+1);
				if (rendez_vous[currentrdv].length == 0) {
					delete rendez_vous[currentrdv];
					$("#display_rdv").empty();
					$("#"+currentrdv+"_year").css("background-color","");
					$("#"+currentrdv+"_month").css("background-color","");
					$("#"+currentrdv+"_year a font").css("color","red");
					$("#"+currentrdv+"_month a font").css("color","red");
					
					var newcontentrdv = $("#"+currentrdv+"_year a").html();
					$("#"+currentrdv+"_year").empty();
					$("#"+currentrdv+"_year").append(newcontentrdv);
					
					newcontentrdv = $("#"+currentrdv+"_month a").html();
					$("#"+currentrdv+"_month").empty();
					$("#"+currentrdv+"_month").append(newcontentrdv);
					ansL[currentyear] = $("#displayyear").html()
					moisL[currentmonth+"-"+currentyearofmonth] = $("#displaymonth").html();
				} else {
					display_rendez_vous(currentrdv);
				}
			} else if (data.rep == "failed") {
				$("#annuler_result_"+indexrdv).empty();
				$("#annuler_result_"+indexrdv).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#annuler_result_"+indexrdv).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#annuler_result_"+indexrdv).append("</ul>");
			}
		},
		'json'
	);
}