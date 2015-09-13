(function ($) {
	/**
	 * Function to submit an ajax request for a 'data-editable' field.
	 *
	 * This removes the editing control and shows the original entry before sending
	 * the request. When an error occurs it does it's best to restore the original
	 * value but fnError can be used to specify what to do.
	 * @param url
	 * @param data
	 * @param originalControl
	 * @param editControl
	 * @param fnError
	 */
	function sendEditableRequest(url, data, originalControl, editControl, fnError) {
		originalControl.show();
		if(typeof(editControl) == 'object') {
			editControl.remove();
		}

		$.ajax({
			data : data,
			url  : url,
			type : "post",
			error: function (response) {
				if(typeof(fnError) == 'function') {
					fnError(originalControl, editControl);
				} else if(!originalControl.attr('data-toggle-template')) {
					originalControl.text(originalControl.data('originalValue'));
				} else {
					originalControl.data('value', originalControl.data('originalValue'));
					setToggleText(originalControl);
				}
				originalControl.data('originalValue', false);
				processAjaxErrors(response);
			}
		});
	}

	/**
	 * Set the text of a toggle field using the current value and a template, if one exists.
	 * @param control
	 */
	function setToggleText(control) {
		var template = $('[data-type="data-toggle-template"][data-toggle-id="' + control.data('toggleTemplate') + '"][data-value="'
		                 + control.data('value').toString() + '"]');
		template = template.length > 0 ? template.eq(0).html() : (control.data('value') ? 'Yes' : 'No');
		control.html(template);
	}

	/**
	 * Set the value from a text or textarea field, allowing for configuration
	 * @param original
	 * @param editType
	 * @param text
	 */
	function setTextText(original, editType, text) {
		var settings = getTextConfig(original.data('config'), text, text);
		original.html(editType == 'textarea' ? settings.text.replace(new RegExp("\n", 'g'), '<br>') : settings.text);
	}

	/**
	 * Set the text from a select, using formats and configuration.
	 * @param original
	 * @param value
	 * @param text
	 */
	function setSelectText(original, value, text) {
		var source = $('[data-type="data-select-source"][data-select-name="' + original.data('editSource') + '"]').eq(0);
		var textFormat = $('[data-type="data-text-format"][data-name="' + original.data('textFormat') + '"]').eq(0);
		var settings = getTextConfig(typeof(textFormat.data('config')) == 'object' ? textFormat.data('config') : source.data('config'), value, text)

		// Get the html/text to set
		var html = textFormat.length > 0 ? textFormat.html() : '#text';

		// Set each value
		for(var key in settings) {
			html = html.replace(new RegExp('#' + key, 'g'), settings[key]);
		}
		original.html(html);
	}

	/**
	 * Get the values to use when setting the text, from a given configuration
	 * @param cfgHtmlObj
	 * @param value
	 * @param text
	 * @returns {{}}
	 */
	function getTextConfig(cfgHtmlObj, value, text) {
		if(typeof(cfgHtmlObj) != 'object') {
			cfgHtmlObj = {};
		}

		// Set up the configuration
		var config = {
			'text' : {},
			'value': {}
		};
		config['text'][value] = text;
		config['value'][value] = value;
		for(var key in cfgHtmlObj) {
			config[key] = cfgHtmlObj[key];
		}

		// Set the config values
		var settings = {};
		for(var key in config) {
			settings[key] = typeof(config[key][value]) != 'undefined' ? config[key][value] : (key == 'text' ? text : value);
		}

		return settings;
	}

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

	/**
	 * Allow any element to be edited by producing an in-line form element
	 * and then submitting the request by AJAX.
	 */
	$('body').on('click', '[data-editable="true"][data-edit-type]', function (event) {
		var original = $(this);
		var editType = original.data('editType');
		event.preventDefault();
		event.stopPropagation();

		// If the form type is a text field or textarea
		// simply produce the field and set the value
		if(editType == 'text' || editType == 'textarea') {
			var formControl = editType == 'text' ?
			                  $('<input type="text" value="' + original.text() + '">') :
			                  $('<textarea rows="4">' + original.html().replace(new RegExp("\n", 'g'), "").replace(new RegExp('<br>', 'g'), "\n") + '</textarea>');
			original.data('originalValue', original.text()).hide();
			formControl.insertAfter(original).attr('class', 'form-control').attr('name', original.data('controlName')).focus();

			// On blur / pressing enter set the new value and submit the ajax request.
			formControl.on('blur keypress', function (event) {
				if(event.type == 'blur' || (event.type == 'keypress' && event.which == 13 && (event.shiftKey || editType == 'text'))) {
					formControl.off('blur keypress');
					setTextText(original, editType, formControl.val());
					sendEditableRequest(original.data('editUrl'), $.param({
						'field': original.data('controlName'),
						'value': formControl.val()
					}), original, formControl, function (original) {
						setTextText(original, original.data('editType'), original.data('originalValue'));
					});
				}
			});
		}
		// If the form type is toggle use an optional template
		// to specify what the values should be
		else if(editType == 'toggle') {
			// Toggle the value and store the current value
			original.data('originalValue', !!original.data('value'));
			original.data('value', !original.data('value'));
			setToggleText(original);
			// Send the request
			sendEditableRequest(original.data('editUrl'), $.param({
				'field': original.data('key'),
				'value': original.data('value')
			}), original);
		}
		// If the form type is select then create the <select> element
		// from the specified source. The format of the new text value
		// can be set using data-text-format and / or data-config
		else if(editType == 'select') {
			// Look for the source
			var source = $('[data-type="data-select-source"][data-select-name="' + original.data('editSource') + '"]');
			if(source.length > 0) {
				// Create the <select> element
				var formControl = $(source.html());
				original.data('originalValue', original.data('value')).data('originalText', original.text()).hide();
				formControl.insertAfter(original).val(original.data('value')).focus();

				// On blur / change send the request
				formControl.on('blur change', function () {
					formControl.off('blur change');
					// Change the value
					var value = formControl.val();
					var text = formControl.find('option[value="' + value + '"]').text();
					original.data('value', value);
					// Set the new text
					setSelectText(original, value, text);
					// Send the request
					sendEditableRequest(original.data('editUrl'), $.param({
						'field': original.data('controlName'),
						'value': value
					}), original, formControl, function (originalControl) {
						originalControl.data('value', originalControl.data('originalValue'))
							.data('originalValue', false)
							.text(originalControl.data('originalText'))
							.data('originalText', false);
						setSelectText(originalControl, originalControl.data('value'), originalControl.text());
					});
				});
			}
		}
	});
})(jQuery);