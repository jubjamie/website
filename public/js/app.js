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
(function ($) {
	$.fn.SimpleMDE = function () {
		this.each(function() {
			var simplemde = new SimpleMDE({
				element: this,
				status: false
			});
		});
	};
	
	jQuery(document).ready(function () {
		$('[data-type="simplemde"]').SimpleMDE();
	});
})(jQuery);
var $modal = $('#modal');
var $modalDialog = $modal.children('div.modal-dialog');
var $modalContent = $modalDialog.children('div.modal-content');
var $form;
var $btns;
var $xhttp2 = typeof(FormData) != 'undefined';
if(!$xhttp2) {
	console.log('ALERT: Your browser does not support XMLHttpRequest2. AJAX forms will work but you will be unable to upload files by AJAX.');
}

(function ($) {
	function form() {
		return $modal.find('form');
	}
	
	function btns() {
		return $modal.find('button');
	}
	
	$modal.on('show.bs.modal', function (e) {
		// Get the modal template
		var target = $(e.relatedTarget);
		var template = $('div[data-type="modal-template"][data-id="' + target.data('modalTemplate') + '"]');
		
		if(template.length > 0) {
			// Set the content of the modal dialog
			$modalContent.html(template.html());
			if(target.data('modalClass')) {
				$modalDialog.addClass(target.data('modalClass'));
			}
			if(target.data('modalTitle')) {
				if($modalContent.children('div.modal-header').length == 0) {
					$modalContent.prepend('<div class="modal-header"></div>');
				}
				$modalContent.children('div.modal-header').html('<h1>' + target.data('modalTitle') + '</h1>');
			}
			
			// Set any default form values
			var formData = target.data('formData');
			$form = form();
			if(typeof(formData) == 'object') {
				var formControl;
				for(var key in formData) {
					formControl = $form.find('[name="' + key + '"]');
					if(formControl.attr('type') == 'checkbox') {
						formControl.prop('checked', !!formData[key]);
					} else {
						formControl.val(formData[key]);
					}
				}
			}
			
			// Set the form action
			if(target.data('formAction')) {
				$form.attr('action', target.data('formAction'));
			}
			
			// Reload widgets
			$form.find('input[data-input-type="datetimepicker"]').each(function (i, obj) {
				var el = datetimepicker($(obj), {
					widgetParent: $('body'),
				});
				
				el.on('dp.show', function () {
					$('.bootstrap-datetimepicker-widget').offset({
						left: el.offset().left,
						top : el.offset().top + el.outerHeight()
					});
				});
			});
		} else {
			$modal.one('shown.bs.modal', function () {
				$modal.modal('hide');
			});
		}
	});
	// Clear the modal dialog when closed
	$modal.on('hidden.bs.modal', function () {
		$modalDialog.attr('class', 'modal-dialog');
		$modalContent.html('');
	});
	// Clear the modal form of the validation state
	$modal.on('clearform', function () {
		$form = form();
		$form.find('.has-error').removeClass('has-error').find('p.errormsg').remove();
		$form.find('.has-success').removeClass('has-success');
		$form.find('.alert.form-error').remove();
	});
	// Trigger the form submission when <button data-type="submit-modal"> is clicked
	$modal.on('click', '[data-type="submit-modal"]', function () {
		if(!$(this).data('submitConfirm') || confirm($(this).data('submitConfirm'))) {
			$modal.trigger('ajaxsubmit', {
				btn: $(this)
			});
		}
	});
	$modal.on('ajaxsubmit', function (event, data) {
		var $btn = data.btn;
		$form = form();
		$btns = btns();
		
		if($btn.data('formAction')) {
			$form.attr('action', $btn.data('formAction'));
		}
		$btns.attr('disabled', 'disabled');
		
		var settings = {
			data      : $xhttp2 ? new FormData($form[0]) : $form.serialize(),
			url       : $form.attr('action'),
			type      : "post",
			success   : function () {
				$btn.off('click');
				location.reload();
			},
			error     : function (data) {
				$modal.trigger('clearform');
				$btns.attr('disabled', false);
				error = processAjaxErrors(data, $form);
				
				if(error.list) {
					$form.find('input,textarea,select').each(function () {
						var input = $(this);
						var group = input.parents('.form-group');
						if(input.attr('name') in error.message) {
							group.addClass('has-error');
							$('<p class="help-block errormsg">' + error.message[input.attr('name')][0] + '</p>').insertAfter(
								input.parent().hasClass('input-group') ? input.parent() : input);
						} else {
							group.addClass('has-success');
						}
					});
				} else {
					$('<div class="alert alert-warning form-error"><span class="fa fa-exclamation"></span><span>' + error.message
					  + '</span></div>').insertBefore($modal.find('.modal-body').children().first());
				}
			},
			beforeSend: function () {
				$modal.trigger('clearform');
			}
		}
		// XMLHttpRequest2 settings
		if($xhttp2) {
			settings.cache = false;
			settings.contentType = false;
			settings.processData = false;
			settings.mimeType = "multipart/form-data";
		}
		
		$.ajax(settings);
	});
	// When submitting the form, trigger the 'click' of the first button
	$modal.on('submit', 'form', function (event) {
		var btn = $(this).find('button,input[type="button"],input[type="submit"]').first();
		if(btn.data('type') == 'submit-modal') {
			event.preventDefault();
			event.stopPropagation();
			btn.trigger('click');
		}
	});
	
})(jQuery);
(function ($) {
	$.fn.OtherInputs = function () {
		this.each(function() {
			var input = $(this);
			var other_input = $('[name="' + input.data('otherInput') + '"]');
			var other_value = input.data('otherValue') || 'other';
			
			if(other_input.length) {
				other_input.addClass('input-other');
				input.on('change', function() {
					if(input.val() == other_value) {
						other_input.show();
					} else {
						other_input.hide();
					}
				});
				
				other_input.css('display', input.val() == other_value ? 'block' : 'none');
			}
		});
	};

	jQuery(document).ready(function () {
		$('[data-other-input]').OtherInputs();
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
function processAjaxErrors(data) {
	var error = {
		code   : 500,
		key    : '',
		message: 'Oops, an unknown error has occurred',
		list   : false
	};
	
	// Check for a detailed error message
	if(typeof(data.responseJSON) == "object") {
		var response_error = data.responseJSON;
		
		// Status
		error.code = data.status;
		
		// Error code
		if(typeof(response_error.error_code) != 'undefined') {
			error.key = response_error.error_code;
		}
		
		// Error message
		if(typeof(response_error.__error) != 'undefined') {
			error.message = response_error.error;
		} else {
			error.message = response_error;
			error.list = true;
		}
	}
	
	return error;
}
function datetimepicker(input, options) {
	var format = input.data('dateFormat') ? input.data('dateFormat') : 'YYYY-MM-DD';
	var parent = input.parent();
	var datetimepickerOptions = {
		format         : format,
		icons          : {
			time    : 'fa fa-clock-o',
			date    : 'fa fa-calendar',
			up      : 'fa fa-chevron-up',
			down    : 'fa fa-chevron-down',
			previous: 'fa fa-chevron-left',
			next    : 'fa fa-chevron-right',
			today   : 'fa fa-bullseye',
			clear   : 'fa fa-trash',
			close   : 'fa fa-remove'
		},
		showTodayButton: true,
	};
	if(typeof(options) == 'object') {
		datetimepickerOptions = $.extend({}, datetimepickerOptions, options);
	}
	
	if(parent.hasClass('input-group')) {
		return parent.addClass('date').datetimepicker(datetimepickerOptions);
	} else {
		return input.datetimepicker(datetimepickerOptions);
	}
}
$.ajaxSetup({
	headers : {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
		'TZ-OFFSET'   : (new Date).getTimezoneOffset()
	},
	method  : "GET",
	dataType: "json"
});
$('select[select2]').each(function () {
	$(this).select2({
		placeholder: $(this).attr('select2') || '',
		theme      : 'bootstrap'
	})
});
$('[data-type="filter-select"]').on('change', function () {
	var select = $(this);
	window.location = select.data('urlBase') + (select.val() ? '/filter/' + select.val() : '');
});
$('[data-type="search-input"]').on('keypress', function (event) {
	if(event.which == 13) {
		var input = $(this);
		var value = encodeURI(input.val()).trim();
		window.location = input.data('urlBase') + (value ? '/search/' + value : '');
	}
});
$('input[data-input-type="datetimepicker"]').each(function (i, obj) {
	datetimepicker($(obj));
});
$('[data-type="toggle-visibility"]').on('change', function () {
	var form = $(this.form);
	form.find('[data-visibility-id]').addClass('hidden');
	form.find('[data-visibility-id="' + $(this).val() + '"]').removeClass('hidden');
});
if(typeof(window.console) == 'undefined') {
	var console = {
		log: function (str) {
		}
	}
}