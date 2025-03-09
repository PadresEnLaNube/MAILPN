(function($) {
	'use strict';

	function mailpn_timer(step) {
		var step_timer = $('.mailpn-player-step[data-mailpn-step="' + step + '"] .mailpn-player-timer');
		var step_icon = $('.mailpn-player-step[data-mailpn-step="' + step + '"] .mailpn-player-timer-icon');
		
		if (!step_timer.hasClass('timing')) {
			step_timer.addClass('timing');

      setInterval(function() {
      	step_icon.fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 5000);

      setInterval(function() {
      	step_timer.text(Math.max(0, parseInt(step_timer.text()) - 1)).fadeOut('fast').fadeIn('slow').fadeOut('fast').fadeIn('slow');
      }, 60000);
		}
	}

	$(document).on('click', '.mailpn-popup-player-btn', function(e){
  	mailpn_timer(1);
	});

  $(document).on('click', '.mailpn-steps-prev', function(e){
    e.preventDefault();

    var steps_count = $('#mailpn-recipe-wrapper').attr('data-mailpn-steps-count');
    var current_step = $('#mailpn-popup-steps').attr('data-mailpn-current-step');
    var next_step = Math.max(0, (parseInt(current_step) - 1));
    
    $('.mailpn-player-step').addClass('mailpn-display-none-soft');
    $('#mailpn-popup-steps').attr('data-mailpn-current-step', next_step);
    $('.mailpn-player-step[data-mailpn-step=' + next_step + ']').removeClass('mailpn-display-none-soft');

    if (current_step <= steps_count) {
    	$('.mailpn-steps-next').removeClass('mailpn-display-none');
    }

    if (current_step <= 2) {
    	$(this).addClass('mailpn-display-none');
    }

    mailpn_timer(next_step);
	});

	$(document).on('click', '.mailpn-steps-next', function(e){
    e.preventDefault();

    var steps_count = $('#mailpn-recipe-wrapper').attr('data-mailpn-steps-count');
    var current_step = $('#mailpn-popup-steps').attr('data-mailpn-current-step');
    var next_step = Math.min(steps_count, (parseInt(current_step) + 1));

    $('.mailpn-player-step').addClass('mailpn-display-none-soft');
    $('#mailpn-popup-steps').attr('data-mailpn-current-step', next_step);
    $('.mailpn-player-step[data-mailpn-step=' + next_step + ']').removeClass('mailpn-display-none-soft');

    if (current_step >= 1) {
    	$('.mailpn-steps-prev').removeClass('mailpn-display-none');
    }

    if (current_step >= (steps_count - 1)) {
    	$(this).addClass('mailpn-display-none');
    }

    mailpn_timer(next_step);
	});

	$(document).on('click', '.mailpn-ingredient-checkbox', function(e){
    e.preventDefault();

    if ($(this).text() == 'radio_button_unchecked') {
    	$(this).text('task_alt');
    }else{
    	$(this).text('radio_button_unchecked');
    }
	});

	$('.mailpn-carousel-main-images .owl-carousel').owlCarousel({
    margin: 10,
    center: true,
    nav: false, 
    autoplay: true, 
    autoplayTimeout: 5000, 
    autoplaySpeed: 2000, 
    pagination: true, 
    responsive:{
      0:{
        items: 2,
      },
      600:{
        items: 3,
      },
      1000:{
        items: 4,
      }
    }, 
  });
})(jQuery);
