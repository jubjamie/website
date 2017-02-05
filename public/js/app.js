(function($) {
	var hideAlert = function(msg) {
		msg = msg.parent();
		msg.animate({
			opacity: '0'
		}, 100, function () {
			msg.slideUp(100, function () {
				msg.remove();
			});
		});
	};

	$.fn.CloseMessages = function() {
		var messages = this.find("div.alert");
		messages.each(function () {
			var $this = $(this);
			if($this.hasClass('alert-perm')) {
				var lnk = $('<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				$this.prepend(lnk);
			} else {
				setTimeout(function() {
					hideAlert($this);
				}, 3000);
			}
		});
		this.on("click", "div.alert > button.close", function () {
			hideAlert($(this).parent());
		});
	};

	jQuery(document).ready(function () {
		$('div.message-centre').CloseMessages();
	});
})(jQuery);
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
(function($) {
	$.fn.extend({
		tabify : function() {
			return this.each(function() {
				var TabGroup    = $(this);

				if(!TabGroup.hasClass("tab-group-perm")) {
					var TabLinks    = TabGroup.children("ul.nav-tabs").children("li");
					var TabContent  = TabGroup.children("div.tab-content").children("div.tab-pane");

					// Handler
					TabLinks.on("click", function() {
						if(!$(this).hasClass("active")) {
							var i = TabLinks.index($(this));
							TabLinks.removeClass("active");
							TabContent.removeClass("active");
							TabLinks.eq(i).addClass("active");
							TabContent.eq(i).addClass("active");
						}
						return false;
					});

					// Default
					var hash = window.location.hash.substr(1);
					if(hash && TabLinks.filter('#' + hash + 'Tab').length) {
						TabLinks.filter('#' + hash + 'Tab').eq(0).trigger("click");
					} else if(TabLinks.filter(".active").length) {
						TabLinks.filter(".active").eq(0).trigger("click");
					} else {
						TabLinks.eq(0).trigger("click");
					}
				}
			});
		}
	});
})(jQuery);
if(typeof(window.console) == 'undefined') {
	var console = {
		log: function (str) {
		}
	}
}