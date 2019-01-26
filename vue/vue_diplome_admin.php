<strong>Clients à qui vous faites passer le code : </strong><br><br>
<?php
echo $controleur->codeavec();

echo $controleur->examavec();
?>
<script>
var token = <?php echo $_SESSION['token'];?>;

var globalid;

function setnote(id) {
	if (!confirm("Etes vous sûre de vouloir lui attribuer cette note ?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_diplome.php',
		{
			action: "setnote",
			note: withoutletter($("#getnote_"+id).val()),
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#msg_setnote_"+globalid).empty();
				$("#msg_setnote_"+globalid).append("<font color='green'>Modifié</font>");
				$("#note_"+globalid).empty();
				if ($("#getnote_"+globalid).val() == "") {
					$("#note_"+globalid).append("non noté");
				} else {
					$("#note_"+globalid).append(withoutletter($("#getnote_"+globalid).val())+"/20");
				}
			} else if (data.rep == "failed") {
				$("#msg_setnote_"+globalid).empty();
				$("#msg_setnote_"+globalid).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#msg_setnote_"+globalid).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#msg_setnote_"+globalid).append("</ul>");
			}
		},
		'json'
	);
}

function givecode(id) {
	if (!confirm("Etes vous sûre de vouloir lui donner le code ?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_diplome.php',
		{
			action: "code",
			code: 1,
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#error_code_"+globalid).empty();
				$("#stringcode_"+globalid).empty();
				$("#stringcode_"+globalid).append('(a le code)');
				$("#button_code_"+globalid).val("Dé-attribuer");
				$("#button_code_"+globalid).attr("onclick","removecode("+globalid+")");
			} else if (data.rep == "failed") {
				$("#error_code_"+globalid).empty();
				$("#error_code_"+globalid).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#error_code_"+globalid).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#error_code_"+globalid).append("</ul>");
			}
		},
		'json'
	);
}

function removecode(id) {
	if (!confirm("Etes vous sûre de vouloir lui donner le code ?")) {
		return;
	}
	globalid = id;
	$.post(
		'/POST/controleur_diplome.php',
		{
			action: "code",
			code: 0,
			id: id,
			token: token
		},

		function(data){
			if (data.rep == "success") {
				$("#error_code_"+globalid).empty();
				$("#stringcode_"+globalid).empty();
				$("#stringcode_"+globalid).append("(n'a pas le code)");
				$("#button_code_"+globalid).val("Attribuer");
				$("#button_code_"+globalid).attr("onclick","givecode("+globalid+")");
			} else if (data.rep == "failed") {
				$("#error_code_"+globalid).empty();
				$("#error_code_"+globalid).append("<ul>");
				for (var i=0;i<data.errors.length;i++) {
					$("#error_code_"+globalid).append("<li><font color='red'>"+data.errors[i]+"</font></li>");
				}
				$("#error_code_"+globalid).append("</ul>");
			}
		},
		'json'
	);
}
function withoutletter(str) {
	str = remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(str,"z",""),"y",""),"x",""),"w",""),"v",""),"u",""),"t",""),"s",""),"r",""),"q",""),"p",""),"o",""),"n",""),"m",""),"l",""),"k",""),"j",""),"i",""),"h",""),"g",""),"f",""),"e",""),"d",""),"c",""),"b",""),"a","");
	str = remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(remplace(str,"Z",""),"Y",""),"X",""),"W",""),"V",""),"U",""),"T",""),"S",""),"R",""),"Q",""),"P",""),"O",""),"N",""),"M",""),"L",""),"K",""),"J",""),"I",""),"H",""),"G",""),"F",""),"E",""),"D",""),"C",""),"B",""),"A","");
	return str;
}
function remplace(str,A,B) {
	while (str.replace(A,B) != str) {
		str = str.replace(A,B);
	}
	return str;
}
</script>