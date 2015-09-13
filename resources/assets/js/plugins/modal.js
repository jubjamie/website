var $modal = $('#modal');
var $modalDialog = $modal.children('div.modal-dialog');
var $modalContent = $modalDialog.children('div.modal-content');
var $form;
var $btns;

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
			// Set the modal content
			$modalContent.html(template.html());
			if(target.data('modalClass')) $modalDialog.addClass(target.data('modalClass'));
			if(target.data('modalTitle')) {
				if($modalContent.children('div.modal-header').length == 0) {
					$modalContent.prepend('<div class="modal-header"></div>');
				}
				$modalContent.children('div.modal-header').html('<h1>' + target.data('modalTitle') + '</h1>');
			}

			// Set any default form values
			var formData = target.data('formData');
			if(typeof(formData) == 'object') {
				$form = form();
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
		} else {
			$modal.one('shown.bs.modal', function () {
				$modal.modal('hide');
			});
		}
	});
	$modal.on('hidden.bs.modal', function () {
		$modalDialog.attr('class', 'modal-dialog');
		$modalContent.html('');
	});
	$modal.on('clearform', function () {
		$form = form();
		$form.find('.has-error').removeClass('has-error').find('p.errormsg').remove();
		$form.find('.has-success').removeClass('has-success');
	});
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

		$form.attr('action', $btn.data('formAction'));
		$btns.attr('disabled', 'disabled');
		$.ajax({
			data      : $form.serialize(),
			url       : $form.attr('action'),
			type      : "post",
			success   : function () {
				$btn.off('click');
				location.reload();
			},
			error     : function (data) {
				$modal.trigger('clearform');
				$btns.attr('disabled', false);
				processAjaxErrors(data, $form);
			},
			beforeSend: function () {
				$modal.trigger('clearform');
			}
		});
	});
	$modal.on('submit', 'form', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(this).find('button,input[type="button"],input[type="submit"]').first().trigger('click');
	});

})(jQuery);