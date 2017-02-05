(function ($) {
	$.fn.CookieAcceptance = function () {
		this.on('click', 'button.close', function() {
			Cookies.set('CookiePolicyAccepted', true, {expires : 30});
		});
	};

	jQuery(document).ready(function () {
		$('#cookie-policy-msg').CookieAcceptance();
		console.log(Cookies.getJSON());
	});
})(jQuery);