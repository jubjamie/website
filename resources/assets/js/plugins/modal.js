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