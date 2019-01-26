$('.scrolling').on('click', function() {
	var page = $(this).attr('href');
	$('html, body').animate({scrollTop: $(page).offset().top-100}, 800);
	return;
});