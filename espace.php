<?php
if (isset($_SESSION['perm'])) {
	if ($_SESSION['perm'] == "admin") {
		$title = "Espace Moniteur";
	} else if ($_SESSION['perm'] == "superadmin") {
		$title = "Espace Directeur";
	} else if ($_SESSION['perm'] == "user") {
		$title = "Espace Client";
	}
} else {
	$title = "Espace Client";
}
$onglet = "espaceli";
include ("vue/head.php");
?>
<br>
<div class="alignmenu">
	 - <strong><?php if (isset($_SESSION['perm'])) {
				if ($_SESSION['perm'] == "admin") {
					echo ("Espace Moniteur : ");
				} else if ($_SESSION['perm'] == "superadmin") {
					echo ("Espace Directeur : ");
				} else if ($_SESSION['perm'] == "user") {
					echo ("Espace Client :");
				}
			  } else {
				echo ("Espaces Client : ");	
			  }
				?></strong><br>
	 <div style="margin-left: 3vW;">
			- <a class="lien petit" href="/calendar.php" target="_blank">Calendrier</a><br>
			- <a class="lien petit" href="/diplome.php" target="_blank">Diplomes</a><br>
			<?php
				if (isset($_SESSION['perm'])) {
					if ($_SESSION['perm'] == "superadmin") {
						echo (" - <a class='lien petit' href='/admin.php' target='_blank'>Administration</a>");
					}
				}
			?>
	 </div>
</div>
<?php
include("vue/foot.php");
?>