(function ($) {
	/**
	 * Allow any button to submit an AJAX request and process the response.
	 * Data can be sent by using the 'data-' attributes.
	 */
	$('body').on('click', '[data-submit-ajax]', function () {
		var btn = $(this);
		
		if(!btn.data('submitConfirm') || confirm(btn.data('submitConfirm'))) {
			var action = btn.data('submitAjax');
			var data = btn.data();
			var redirect = btn.data('successUrl') ? btn.data('successUrl') : window.location;
			delete data['submitAjax'];
			delete data['submitConfirm'];
			delete data['successUrl'];
			btn.attr('disabled', 'disabled');
			
			$.ajax({
				data   : $.param(data),
				url    : action,
				type   : "post",
				success: function () {
					window.location = redirect;
				},
				error  : function (data) {
					btn.attr('disabled', false);
					processAjaxErrors(data);
				}
			});
		}
	});
})(jQuery);