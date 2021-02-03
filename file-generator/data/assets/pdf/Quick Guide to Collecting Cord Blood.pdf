(function($) {

	var App = {

		mainSlider: function() {
			$('#home-slider').flexslider({
				animation: 'slide'
			});
		},

		navigation: function() {
			$('#menu-btn').click(function() {
				$('#main-menu').slideToggle();
			});

			$(window).on('resize', function() {
				App.checkHeaderWidth();
			});

			$(window).on('scroll', function(e) {

				if ($(window).scrollTop() >= 224 && $(window).width() > 974) {
					$('#header').addClass('sticky-mode');
					$('body').addClass('padded');
				}
				else {
					$('#header').removeClass('sticky-mode');
					$('body').removeClass('padded');
				}

			});

			$('#main-menu .toggle-child').on('click', function(e) {
				$(this).next('.sub-menu').slideToggle();
				$(this).children('i').toggleClass('rotate');
				$(this).children('i').toggleClass('rotate-reset');
			});
		},

		checkHeaderWidth: function() {
			var widthSize = $(window).width(),
				$mainMenu = $('#main-menu'),
				$subMenus = $('#main-menu .sub-menu'),
				$toggles = $('#main-menu .toggle-child');

			if (widthSize > 974) {
				$mainMenu.removeClass('responsive');
				$mainMenu.css('display', 'block');
				$subMenus.css('display', 'none');
				$toggles.css('display', 'none');
				$('#main-menu .toggle-child i').removeClass('rotate');
				$('#main-menu .toggle-child i').addClass('rotate-reset');
			}
			else {
				$mainMenu.addClass('responsive');
				$mainMenu.css('display', 'none');
				$toggles.css('display', 'inline-block');
			}
		},

		signup: function() {
			$('#signup, .signup').click(function() {
				$('.modal').show();
			});
			$('.modal').click(function(e) {
				if ($('.model-content') != e.target) {
					$(this).hide();
				}
			});
		},

		scrolltoTop: function() {
			$(window).scroll(function() {
				if ($(this).scrollTop() > 50 && $(window).width() < 974) {
					$('.scrolltop:hidden').stop(true, true).fadeIn();
				}
				else {
					$('.scrolltop').stop(true, true).fadeOut();
				}
			});
			$(".scroll").click(function() {
				$("html,body").animate({ scrollTop: $("html").offset().top }, "1000");
				return false;
			});
		}
	};
	var Form = {
		checkEligibility: function() {
			$('#checkEligibility').click(function() {
				var yes = 0;
				var no = 0
				$('input:radio').filter(':checked').filter('[value=yes]').each(function() {
					yes++;
				});
				$('input:radio').filter(':checked').filter('[value=no]').each(function() {
					no++;
				});
				if ((yes + no) == 15) {
					if (yes == 15) {
						$('.eligibility-modal').show();
						$('#registerFamily').hide();
						$('#registerPublic').show();
					}
					else {
						$('.eligibility-modal').show();
						$('#registerFamily').show();
						$('#registerPublic').hide();
					}
				}
				else {
					alert('Please answer every question.');
				}
				$('.eligibility-modal').click(function(e) {
					if ($('.model-content') != e.target) {
						$(this).hide();
						$('#registerFamily').hide();
						$('#registerPublic').hide();
					}
				});
			});
		},
		preferred_switch: function() {
			$('input:radio[name="meet_appt"]').change(function() {
				if ($('#meet_appt_1').is(':checked')) {
					$('#next_appt').show();
				}
				else {
					$('#next_appt').hide();
				}
			});
		},
		donationTimes: function() {
			$('input:radio[name="past_donated_blood"]').change(function() {
				if ($('#past_donated_blood_1').is(':checked')) {
					$('#showDonationTimes').show();
				}
				else {
					$('#showDonationTimes').hide();
				}
			});
		},
		selected_others: function() {
			$('select[name="info_abt_us"]').change(function() {
				if ($('select[name="info_abt_us"]').val() == 'Others') {
					$('#showOthers').show();
				}
				else {
					$('#showOthers').hide();
				}
			});
		},
		showDate: function() {
			$('#baby_due_date').combodate();
		}
	};

	$(window).load(function() {
		App.checkHeaderWidth();
		App.mainSlider();
		App.navigation();
		App.signup();
		App.scrolltoTop();
		Form.checkEligibility();
		Form.preferred_switch();
		Form.selected_others();
		Form.donationTimes();
	});

})(jQuery);
