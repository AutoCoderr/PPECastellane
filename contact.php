<?php
$title = "Les contacts";
$onglet = "contactli";
include("vue/head.php")
?>
<style>
#background {
	height: 200vh;
}
</style>
<center>
    <div class="container mt-5">
          <div class="col-md-10">
            <h2 class="text-uppercase mt-3 font-weight-bold text-black">Contactez-nous</h2>
            <form>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <input type="text" id="nom" class="form-control mt-2" placeholder="Nom" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <input type="text" id="prenom" class="form-control mt-2" placeholder="Prénom" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <input type="text" id="mail" class="form-control mt-2" placeholder="Email" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <input type="text" id="numtel" class="form-control mt-2" placeholder="Téléphone" required>
                  </div>
                </div>
				<div class="col-lg-12">
                  <div class="form-group">
                    <input type="text" id="objet" class="form-control mt-2" placeholder="Objet" required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <textarea class="form-control" id="content" placeholder="Entrez votre message" rows="3" required></textarea>
                  </div>
                </div>
                <div class="col-12">
                </div>
                <div class="col-12">
                  <button class="btn btn-light" type="button" onclick="sendToContact()" >Envoyer</button>
                </div>
              </div>
            </form>
			<div id='infoSend'></div>
            <div class="text-black">
            <h2 class="text-uppercase mt-4 font-weight-bold">Castellane-Auto</h2>

            (+33)123455678<br>
              contact@catellane.auto<br>
            357 Avenue de la République, 83000 Toulon <br>
                      <br/>
          <br/>
            </div>
          </div>
   <br />
    </div>
	<iframe style="width: 50vW; height: 70vH;" src="https://maps.google.com/maps?width=100%&amp;height=500&amp;hl=en&amp;q=357%20avenue%20de%20la%20R%C3%A9publique%2083000%20Toulon+(Castellane-Auto)&amp;ie=UTF8&amp;t=&amp;z=17&amp;iwloc=B&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a href="https://www.maps.ie/create-google-map/">Castellane-Auto</a></iframe>
	</center>
	<script>
	
	function sendToContact(){
		$.post(
			'/POST/controleur_mp.php',
			{
				action: "sendAsContact",
				objet : $("#objet").val(),
				content: $("#content").val(),
				nom: $("#nom").val(),
				prenom: $("#prenom").val(),
				mail: $("#mail").val(),
				numtel: $("#numtel").val(),
				token: "anon"
			},

			function(data){
				if (data.rep == "success") {
					$("#infoSend").empty();
					$("#infoSend").append("<font color='green'>message envoyé</font>");
					$("#objet").val("");
					$("#content").val("");
					$("#nom").val("");
					$("#prenom").val("");
					$("#mail").val("");
					$("#numtel").val("");
				} else if (data.rep == "failed") {
					$("#infoSend").empty();
					$("#infoSend").append("<ul>");
					for (var i=0;i<data.errors.length;i++) {
						$("#infoSend").append("<li><font color='red'>"+data.errors[i]+"</font></li>");
					}
					$("#infoSend").append("</ul>");
				}
			},
			'json'
		);
	}
	</script>
<?php
include ("vue/foot.php");
?>
