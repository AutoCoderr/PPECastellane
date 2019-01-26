<script>
function remplace(str,A,B) {
	while (str.replace(A,B) != str) {
		str = str.replace(A,B);
	}
	return str;
}

function display_rendez_vous(date) {
	$("#display_rdv").empty();
	if (typeof(rendez_vous[date]) == "undefined") {
		$("#display_rdv").append("Aucun planning ni examen pour le "+date.split("-")[2]+"/"+date.split("-")[1]+"/"+date.split("-")[0]+" .");
		return;
	}
	currentrdv = date;
	$("#display_rdv").append(date.split("-")[2]+"/"+date.split("-")[1]+"/"+date.split("-")[0]+" : <br><br>");
	for (var i=0;i<rendez_vous[date].length;i++) {
		if (i > 0) {
			$("#display_rdv").append("<hr><br>");
		}
		if (rendez_vous[date][i].type == "Planning") {
			$("#display_rdv").append("Cour sur les "+rendez_vous[date][i].Lecon+" ");
			$("#display_rdv").append(" avec "+rendez_vous[date][i].prenom+" "+rendez_vous[date][i].nom+" <br>");
			$("#display_rdv").append("de "+rendez_vous[date][i].debut.split(" ")[1]+" à "+rendez_vous[date][i].fin.split(" ")[1]+"<br>");
			$("#display_rdv").append("pour "+rendez_vous[date][i].prix+" €. ");
			if (rendez_vous[date][i].validated == "yes") {
				$("#display_rdv").append(" (Validé par vous)");
			} else {
				$("#display_rdv").append(" (Non validé)");
			}
		} else if (rendez_vous[date][i].type == "Exam") {
			$("#display_rdv").append("Exam pour le "+rendez_vous[date][i].typeexam+".<br> ");
			$("#display_rdv").append("de "+rendez_vous[date][i].debut+" à "+rendez_vous[date][i].fin+" pour "+rendez_vous[date][i].prix+" €");
			$("#display_rdv").append(" avec "+rendez_vous[date][i].prenom+" "+rendez_vous[date][i].nom+" <br>");
			if (rendez_vous[date][i].validated == "yes") {
				$("#display_rdv").append("(Validé par vous)");
			} else {
				$("#display_rdv").append(" (Non validé)");
			}
		} else if (rendez_vous[date][i].type == "occuped") {
			$("#display_rdv").append("occupé de "+rendez_vous[date][i].debut+" à "+rendez_vous[date][i].fin+" par un autre moniteur");
		}
		if (rendez_vous[date][i].type != "occuped") {
			if (rendez_vous[date][i].validated == "no") {
				$("#display_rdv").append("<br>"+remplace(validhtml,"this_rendezvous",i));
			} else {
				$("#display_rdv").append("<br>"+remplace(devalidhtml,"this_rendezvous",i));
			}
		}
	}
}
</script>