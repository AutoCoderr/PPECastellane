<?php
$title = "Nos tarif";
$onglet = "tarifli";
include("vue/head.php");

$listlecons = $controleur->listoflecons();
$listexams = $controleur->listofexams();

if (sizeof($listexams) > sizeof($listlecons)) {
	$taille = sizeof($listexams);
} else {
	$taille = sizeof($listlecons);
}

?>
<h1>Nos tarifs</h1>
<br><br/>
<br><br/>

<div style="width: 66vW;" id="demo" class="carousel slide" data-ride="carousel">

  <!-- Indicators -->
  <ul class="carousel-indicators">
    <li data-target="#demo" data-slide-to="0" class="active"></li>
    <li data-target="#demo" data-slide-to="1"></li>
    <li data-target="#demo" data-slide-to="2"></li>
  </ul>

  <!-- The slideshow -->
  <div class="carousel-inner">
    <div class="carousel-item active">
      <br/>
      <img class="img-responsive center-block" src="/img/lecons.png" style="width:auto;height:40vH;">
      <br/>
      <table class="tableborder" style="width: 70%" align="center">
			<br/>
		<tr >
			<th>Nom</th>
			<th>Prix</th>
			<th></th>
		</tr>
		<?php
			if (sizeof($listlecons) == 0) {
				echo ("<tr><td>Aucune Leçons</td></tr>");
			} else {
				foreach($listlecons as $lecon) {
					echo ("<tr>");
					echo ("<td align='center'>Leçon sur ".$lecon['nom']."</td>");
					echo ("<td align='center'>".$lecon['tarif']."€/h</td>");
					echo ("<td align='center'><a href='/calendar.php?type=planning&id=".$lecon['id']."' class='btn btn-danger btn-lg' role='button'>Choisir</a></td>");
					echo ("</tr>");
				}
			}
		?>
	  </table>
	  <?php 
			$i = 0;
			while ($i < $taille-sizeof($listlecons)) {
				echo("<br><br>");
				$i += 1;
			}
		?>
			<br/>
    </div>

    <div class="carousel-item">
      <br/>
      <img class="img-responsive center-block" src="/img/code.png" style="width:auto;height:40vH;">
      <br/>
      <table class="tableborder" style="width: 70%" align="center">
			<br/>
   <tr >
       <th>Nom</th>
       <th>Prix</th>
       <th></th>
   </tr>
   <tr>
        <td align="center">Code de la route</td>
        <td align="center">22€</td>
		<td align="center"><a href="../calendar.php?type=exam&id=1" class="btn btn-danger btn-lg" role="button">Choisir</a></td>
   </tr>
	 <br/>
</table>
		<?php
			$i = 0;
			while ($i < $taille-1) {
				echo("<br><br>");
				$i += 1;
			}
		?>
		<br/>
    </div>

    <div class="carousel-item">
      <br/>
      <img class="img-responsive center-block" src="/img/permis.jpg" style="width:auto;height:40vH;">
      <br/>
      <table class="tableborder" style="width: 70%" align="center">
   <tr >
       <th>Nom</th>
       <th>Prix</th>
       <th></th>
   </tr>
		<?php
			if (sizeof($listexams) == 0) {
				echo ("<tr><td>Aucun exams</td></tr>");
			} else {
				foreach($listexams as $exam) {
					if ($exam['id'] != '1') {
						echo ("<tr>");
						echo ("<td align='center'>".$exam['nom']."</td>");
						echo ("<td align='center'>".$exam['prix']."€</td>");
						echo ("<td align='center'><a href='/calendar.php?type=exam&id=".$exam['id']."' class='btn btn-danger btn-lg' role='button'>Choisir</a></td>");
						echo ("</tr>");
					}
				}
			}
		?>
	  </table>
		<?php 
			$i = 0;
			while ($i < $taille-sizeof($listexams)) {
				echo("<br><br>");
				$i += 1;
			}
		?>
	<br/>
	<br/>
	<br/>
    </div>
    </div>
  </div>

  <!-- Left and right controls -->
  <a style="height: 10vH; margin-top: 425px;" class="carousel-control-prev" href="#demo" data-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </a>
  <a style="height: 10vH; margin-top: 425px;" class="carousel-control-next" href="#demo" data-slide="next">
    <span class="carousel-control-next-icon"></span>
  </a>

<?php include("vue/foot.php");?>



