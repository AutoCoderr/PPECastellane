$(document).ready(function() {
	var menu = [{coora:-1, coorb: 400, idli: "#presentationli"},
	            {coora:400, coorb: 980, idli: "#E4li"},
				{coora:980, coorb: 3000, idli: "#E6li"},
				{coora:1530, coorb: 3000, idli: "#PPli"}];
				
	var animations = ["lefttoright","righttoleft","toptobottom","bottomtotop"];
	
	var animationscoor = [{coora:-1, coorb: 3000, section: "presentation"},
	                      {coora:400, coorb: 3000, section: "E4"},
						  {coora:980, coorb: 3000, section: "E6"},
						  {coora:1530, coorb: 3000, section: "PP"}];
	
	var Colors = [{coora:-1, coorb: 400, colors: [249,196,176]},
	              {coora:400, coorb: 980, colors: [239,186,166]},
				  {coora:980, coorb: 1530, colors: [229,176,156]},
				  {coora:1530, coorb: 3000, colors: [219,166,146]}];
						  
	var oldscroll = window.scrollY;
	
	//console.log(oldscroll);
    setactive(window.scrollY);
	setanime(window.scrollY);
	setColors(window.scrollY);
	setTimeout(function(){ 
		$("#linkE4").click();
	}, 1000);
	
	window.addEventListener('scroll', function(e) {
	   console.log("coor : " + window.scrollY);
	   //console.log("sous : " + (oldscroll - window.scrollY));
	   if ((oldscroll - window.scrollY) <= 30 & (oldscroll - window.scrollY) >= -30) {
		  //console.log("if");
	      setactive(window.scrollY);
		  setanime(window.scrollY);
		  setColors(window.scrollY);
	   }
	   oldscroll = window.scrollY;
    });
	
	
	$('.scrolling').on('click', function() {
		var page = $(this).attr('href');
		$('html, body').animate({scrollTop: $(page).offset().top-100}, 800);
		//console.log("------------ANIMATE------------------");
		return;
	});
	
	
	function setactive(Y) {
		for (var i=0;i<menu.length;i++) {
			 //console.log("i = " + i + "\nmenu.length = " + menu.length);
			 if (Y >= menu[i].coora & menu[i].coorb > Y) {
				//console.log(menu[i].idli);
				$(".active").removeClass("active");
				$(menu[i].idli).addClass("active");
			 }
	     }
	}
	
	function setanime(Y) {
		var animedclass;
		var strclass;
		var selectedtag;
		var animeds;
		for (var h=0;h<animationscoor.length;h++){
			if (Y >= animationscoor[h].coora & animationscoor[h].coorb > Y) {
				animeds = $("#" + animationscoor[h].section + " > .animed")
				for (var i=0;i<animeds.length;i++) {
					animedclass = animeds[i].className.split(" ");
					strclass = "";
					for (var j=0;j<animedclass.length;j++) {
						strclass += "." + animedclass[j];
					}
					selectedtag = $("#" + animationscoor[h].section + ">" + strclass);
					selectedtag.removeClass("animed");
					for (var j=0;j<animedclass.length;j++) {
						for (var k=0;k<animations.length;k++) {
							if (animedclass[j] == animations[k]) {
								selectedtag.removeClass(animedclass[j]);
								selectedtag.addClass(animedclass[j] + "-active");
							}
						}
					}
				}
			}
		}
	}
	function setColors(Y) {
		var colorsSet;
		//var colorSetArrowHover;
		//var colorSetArrowActive;
		for (var i=0;i<Colors.length;i++) {
			if (Y >= Colors[i].coora & Colors[i].coorb > Y) {
				colorsSet = Colors[i].colors;
				//colorSetArrowHover = Colors[i].arrow.hover;
				//colorSetArrowActive = Colors[i].arrow.active;
				i = Colors.length;
			}
		}
		if (typeof(colorsSet) != "undefined"/* & typeof(colorSetArrowHover) != "undefined" & typeof(colorSetArrowActive) != "undefined"*/) {
			var bodyColorString = "";
			for (var i=0;i<3;i++) {
				bodyColorString += colorsSet[i] + ", ";
			}
			bodyColorString = "rgb(" + bodyColorString.substring(0,bodyColorString.length-2) + ")";
			$('body').css('background-color', bodyColorString);
			
			/*bodyColorString = "";
			for (var i=0;i<3;i++) {
				bodyColorString += colorSetArrowHover[i] + ", ";
			}
			bodyColorString = "rgb(" + bodyColorString.substring(0,bodyColorString.length-2) + ")";
			$('body').css('background-color', bodyColorString);
			
			bodyColorString = "";
			for (var i=0;i<3;i++) {
				bodyColorString += colorSetArrowActive[i] + ", ";
			}
			bodyColorString = "rgb(" + bodyColorString.substring(0,bodyColorString.length-2) + ")";
			$('body').css('background-color', bodyColorString);*/
		}
	}
});