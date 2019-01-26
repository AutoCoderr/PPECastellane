<?php
include("controleur/controleur.php");
$controleur = new Controleur(null,null,null,null);
?>
<br><br>
<input href="#nameofyear" class="scrolling" type="button" value="&darr;">

<div id="enterdiv"></div>
<center>
	<table>
		<tr id="switchdiv">
		</tr>
	</table>
	<table>
		<tr>
			<td style="text-align:center;" id='display'>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;" id="display_rdv">
			</td>
		</tr>
	</table>
	<?php
		if ($_SESSION['perm'] == "user") {
			include("vue/vue_calendar_user_formu.php");
		}
	?>
</center>

<script>
var rendez_vous = {};
var ansL = {};
var moisL = {};
</script>
<script src="/js/scrolling.js"></script>

<script src="/js/annuler.js"></script>

<script src="/js/valider.js"></script>

<script src="/js/devalider.js"></script>
<script>

<?php
	if ($_SESSION['perm'] == "user" | $_SESSION['perm'] == "superadmin") {
		?>
		var annulerhtml =  `<?php include("vue/vue_calendar_annuler.php");echo("`");
	}
	if ($_SESSION['perm'] == "admin" | $_SESSION['perm'] == "superadmin") {
		if ($_SESSION['perm'] == "admin") {
			?> var validhtml =  `<?php include("vue/vue_calendar_valider.php");echo("`;");
		}
		?>
		var devalidhtml =  `<?php include("vue/vue_calendar_devalider.php");echo("`;");
	}
?>


onkeydown = function(e){
    if (e.keyCode == 37) {
		displayyear(parseInt(currentyear)-1);
	} else if (e.keyCode == 39) {
		displayyear(parseInt(currentyear)+1);
	}
}

var token = <?php echo $_SESSION['token']; ?>;
var loading = 0;
var currentrdv;
var indexrdv;
var currentyear = <?php echo date("Y");?>;
var currentmonth = <?php echo date("m");?>;
if (screen.width > 688) {
	displayyear(currentyear);
	$("#enterdiv").append('<input type="text" id="enteryear" value="<?php echo date("Y"); ?>"><input type="button" Value="Valider" onclick="javascript:displayyear(parseInt($(\'#enteryear\').val()))"><br>');
	$("#switchdiv").append('<td>');
	$("#switchdiv").append('<input type="button" value="&larr;" onclick="displayyear(parseInt(currentyear)-1)">');
	$("#switchdiv").append('</td>');
	$("#switchdiv").append('<td style="text-align:center;" id="nameofyear">');
	$("#switchdiv").append('</td>');
	$("#switchdiv").append('<td>');
	$("#switchdiv").append('<input type="button" value="&rarr;" onclick="displayyear(parseInt(currentyear)+1)">');
	$("#switchdiv").append('</td>');
} else {
	displaymonth(currentmonth+"/"+currentyear);
	$("#enterdiv").append('<input type="text" id="enteryearandmonth" value="<?php echo date("m")."/".date("Y"); ?>"><input type="button" Value="Valider" onclick="javascript:displaymonth($(\'#enteryearandmonth\').val())"><br>');
	$("#switchdiv").append('<td>');
	$("#switchdiv").append('<input type="button" value="&larr;" onclick="displaymonth(lastmonth(currentmonth,currentyear))">');
	$("#switchdiv").append('</td>');
	$("#switchdiv").append('<td style="text-align:center;" id="nameofyear">');
	$("#switchdiv").append('</td>');
	$("#switchdiv").append('<td>');
	$("#switchdiv").append('<input type="button" value="&rarr;" onclick="displaymonth(nextmonth(currentmonth,currentyear))">');
	$("#switchdiv").append('</td>');
}

function displaymonth(monthandyear) {
	if (loading == 1) {
		return;
	}
	var month = monthandyear.split("/")[0];
	var year = monthandyear.split("/")[1];
	currentmonth = month;
	currentyear = year;
	var cible = "#display";
	$("#nameofyear").empty();
	$("#nameofyear").append("<font color='black' size='5'>"+currentyear+"</font>");
	if (typeof(moisL[month+"-"+year]) == "undefined") {
		loading = 1;
		$(cible).append("<font size='4' color='orange'>Veuillez patienter...</font>");
		$.post(
			'/POST/controleur_getcalendar.php',
			{
				type: 'month',
				year: currentyear,
				month: currentmonth,
				token: token
			},

			function(data){
				$(cible).empty();
				if (data.substring(0,4) == "<tr>") {
					moisL[currentmonth+"-"+currentyear] = "<div class='calendar' style='background-color: #EEEEEE;'>"+data.substring(4,data.length)+"</div>";
				} else {
					moisL[currentmonth+"-"+currentyear] = data;
				}
				$(cible).append(moisL[currentmonth+"-"+currentyear]);
				loading = 0;
			},
			'html'
		);
	} else {
		$("#display").append(ansL[currentyear]);
	}
}

function displayyear(ans) {
	if (loading == 1) {
		return;
	}
	currentyear = ans;
	var cible = "#display";
	$("#nameofyear").empty();
	$("#nameofyear").append("<font color='black' size='5'>"+currentyear+"</font>");
	$(cible).empty();
	if (typeof(ansL[ans]) == "undefined") {
		loading = 1;
		$(cible).append("<font size='4' color='orange'>Veuillez patienter...</font>");
		$.post(
			'/POST/controleur_getcalendar.php',
			{
				type: 'year',
				year: currentyear,
				token: token
			},

			function(data){
				$("#display").empty();
				if (data.substring(0,4) == "<tr>") {
					ansL[currentyear] = "<div style='background-color: #EEEEEE;'>"+data.substring(48,data.length)+"</div>";
				} else {
					ansL[currentyear] = data;
				}
				$(cible).append(ansL[currentyear]);
				loading = 0;
			},
			'html'
		);
	} else {
		$("#display").append(ansL[currentyear]);
	}
}

</script>
<?php
include("vue/vue_display-rendez-vous_".$_SESSION['perm'].".php");
 ?>