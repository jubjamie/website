(function ($) {
	$.fn.DisableButtons = function () {
		this.each(function () {
			var btn = $(this);
			btn.on('click', function (e) {
				btn.addClass("disabled")
					.off("click").on("click", function () {
						return false;
					})
					.children('span.fa').attr("class", '').addClass('fa fa-refresh fa-spin').next().text(btn.attr('disable-submit') || "Working ...");
			});
		});
	};

	jQuery(document).ready(function () {
		$('button[disable-submit]').DisableButtons();
	});
})(jQuery);