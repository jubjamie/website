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