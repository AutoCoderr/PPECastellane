<style>
#background {
	height: 200vH;
}
</style>
<br><br>
<input href="#nameofyear" class="scrolling" id='tonameofyear' type="button" value="&darr;">
<div id="enterdiv"></div>
<center>
	<table>
		<tr>
			<input type="text" id="enteryear"><input type="button" Value="Valider" onclick="javascript:displayyear(parseInt($('#enteryear').val()))"><br>
			<td>
				<input type="button" value="&larr;" onclick="getcalendar(last())">
			</td>
			<td style="text-align:center;" id="nameofyear">
			</td>
			<td style="text-align:center;" id="nameofyearofmonth">
			</td>
			<td>
				<input type="button" value="&rarr;" onclick="getcalendar(next())">
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td id='displayyear'>
			</td>
			<td id='displaymonth'>
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
		var annulerhtml =  `<?php include("vue/vue_calendar_annuler.php");echo("`;");
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
		getcalendar(last());
	} else if (e.keyCode == 39) {
		getcalendar(next());
	}
}

var token = <?php echo $_SESSION['token']; ?>;
var loading = 0;
var currentrdv;
var indexrdv;
var currentyear = <?php echo date("Y");?>;
var currentyearofmonth = <?php echo date("Y");?>;
var currentmonth = <?php echo date("m");?>;

if (screen.width >= screen.height) {
	displayyear(currentyear);
	setTimeout(function () {
		displaymonth(currentmonth+"/"+currentyearofmonth);
	}, 500)
} else if (screen.width < screen.height) {
	displaymonth(currentmonth+"/"+currentyearofmonth);
	setTimeout(function () {
		displayyear(currentyear);
	}, 500)
}
//getcalendar({year: currentyear, month: currentmonth});

function getcalendar(date) {
	if (screen.width >= screen.height) {
		displayyear(date.year);
	} else if (screen.width < screen.height) {
		displaymonth(date.month+"/"+date.year);
	}
}

function displaymonth(monthandyear) {
	if (loading == 1) {
		return;
	}
	var month = parseInt(monthandyear.split("/")[0]);
	var year = parseInt(monthandyear.split("/")[1]);
	if (currentmonth != month) {
		currentmonth = month;
	}
	if (currentyearofmonth != year) {
		currentyearofmonth = year;
	}
	var cible = "#displaymonth";
	$(cible).empty();
	$("#nameofyearofmonth").empty();
	$("#nameofyearofmonth").append("<font color='black' size='5'>"+currentyearofmonth+"</font>");
	if (typeof(moisL[month+"-"+year]) == "undefined") {
		loading = 1;
		$(cible).append("<font size='4' color='orange'>Veuillez patienter...</font>");
		$.post(
			'/POST/controleur_getcalendar.php',
			{
				type: 'month',
				year: currentyearofmonth,
				month: currentmonth,
				token: token
			},

			function(data){
				$(cible).empty();
				if (data.substring(0,4) == "<tr>") {
					moisL[currentmonth+"-"+currentyearofmonth] = "<div class='calendar' style='background-color: #EEEEEE;'>"+data.substring(4,data.length)+"</div>";
				} else {
					moisL[currentmonth+"-"+currentyearofmonth] = data;
				}
				$(cible).empty();
				$(cible).append(moisL[currentmonth+"-"+currentyearofmonth]);
				loading = 0;
			},
			'html'
		);
	} else {
		$(cible).append(moisL[currentmonth+"-"+currentyearofmonth]);
	}
}

function displayyear(ans) {
	if (loading == 1) {
		return;
	}
	if (currentyear != ans) {
		currentyear = ans;
	}
	var cible = "#displayyear";
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
				$(cible).empty();
				$(cible).append(ansL[currentyear]);
				loading = 0;
			},
			'html'
		);
	} else {
		$(cible).append(ansL[currentyear]);
	}
}

function last() {
	if (screen.width >= screen.height) {
		return {year: currentyear-1};
	} else if (screen.width < screen.height) {
		var year = parseInt(currentyearofmonth);
		var month = parseInt(currentmonth);
		month -= 1;
		if (month == 0) {
			month = 12;
			year -= 1;
		}
		return {month: month, year: year};
	}
}
function next() {
	if (screen.width >= screen.height) {
		return {year: currentyear+1};
	} else if (screen.width < screen.height) {
		var year = parseInt(currentyearofmonth);
		var month = parseInt(currentmonth);
		month += 1;
		if (month == 13) {
			month = 1;
			year += 1;
		}
		return {month: month, year: year};
	}
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

</script>
<?php
include("vue/vue_display-rendez-vous_".$_SESSION['perm'].".php");
 ?>