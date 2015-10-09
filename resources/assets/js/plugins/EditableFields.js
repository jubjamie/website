(function ($) {
	function handleValueChange(event, staticObj, formObj) {
		// Only process if the value hasn't changed
		if(staticObj.data('oldValue') != formObj.val()) {
			var editType = staticObj.data('editType');
			// Remove all handlers
			formObj.off('blur')
				.off('change')
				.off('keypress');

			// Get the format to use for the text
			var text_format = '#text';
			var text_format_obj = $([]);
			if(staticObj.data('textFormat')) {
				text_format_obj = $('[data-type="data-text-format"][data-name="' + staticObj.data('textFormat') + '"]');
				if(text_format_obj.length > 0) {
					text_format = text_format_obj.html().trim();
				}
			}

			// Set the text and value variables
			var value = formObj.val();
			var text = value;
			if(editType == 'toggle') {
				var text = value ? 'Yes' : 'No';
				if(staticObj.data('toggleTemplate')) {
					var template = $('[data-type="data-toggle-template"][data-toggle-id="' + staticObj.data('toggleTemplate') + '"][data-value="' + value
					                 + '"]');
					if(template.length > 0) {
						text = template.html().trim();
						text_format = '#text';
					}
				}
			} else if(editType == 'select') {
				text = formObj.find('option[value="' + value + '"]').text();
			}

			// Get any configuration values
			var config = {'text': text, 'value': value};
			if(staticObj.data('config')) {
				config = getConfig(staticObj.data('config'), value, text)
			} else if(text_format_obj.length > 0 && text_format_obj.data('config')) {
				config = getConfig(text_format_obj.data('config'), value, text)
			} else if(editType == 'select') {
				var src = $('[data-type="data-select-source"][data-select-name="' + staticObj.data('editSource') + '"]');
				if(src.data('config')) {
					config = getConfig(src.data('config'), value, text);
				}
			}

			// Set the value text using the config and format
			staticObj.data('value', String(config['value']));
			text = text_format;
			for(var key in config) {
				text = text.replace(new RegExp('#' + key, 'g'), config[key]);
			}

			// Set the new static value
			if(editType == 'textarea') {
				staticObj.html(text.replace(new RegExp("\n", 'g'), '<br>'));
			} else {
				staticObj.html(text);
			}

			// Send the update request
			$.ajax({
				data    : $.param({
					'field': staticObj.data('controlName'),
					'value': value
				}),
				url     : staticObj.data('editUrl'),
				type    : "post",
				complete: function () {
					staticObj.data('oldValue', null)
						.data('oldText', null);
				},
				error   : function (response) {
					staticObj.data('value', staticObj.data('oldValue'));
					staticObj.html(staticObj.data('oldText'));
					processAjaxErrors(response);
				}
			});
		} else {
			staticObj.data('oldValue', null)
				.data('oldText', null);
		}

		// Remove the form object
		formObj.remove();
		staticObj.show();
	}

	/**
	 * Get the values to use when setting the text, from a given configuration
	 * @param cfgHtmlObj
	 * @param value
	 * @param text
	 * @returns {{}}
	 */
	function getConfig(htmlCfg, value, text) {
		if(typeof(htmlCfg) != 'object') {
			htmlCfg = {};
		}

		// Set up the configuration
		var config = {
			'text' : {},
			'value': {}
		};
		config['text'][value] = text;
		config['value'][value] = value;
		for(var key in htmlCfg) {
			config[key] = htmlCfg[key];
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
	$('body').on('click', '[data-editable][data-edit-type]', function (event) {
		var staticObj = $(this);
		var editType = staticObj.data('editType');
		var formObj;
		event.preventDefault();
		event.stopPropagation();

		// Store the current values and text
		var oldValue;
		var oldText;
		if(editType == 'text' || editType == 'textarea') {
			var data = staticObj.data();
			oldValue = data['value'] !== undefined ? staticObj.data('value') : (editType == '' ? staticObj.text() : staticObj.html());
			oldText = editType == 'text' ? staticObj.text() : staticObj.html();
		} else if(editType == 'toggle') {
			oldValue = staticObj.data('value') && staticObj.data('value') != 'false';
			oldText = staticObj.html();

			console.log('Raw: ' + staticObj.data('value'));
			console.log('Parsed: ' + String(oldValue));
		} else if(editType == 'select') {
			oldValue = staticObj.data('value');
			oldText = staticObj.text();
		}
		oldValue = typeof(oldValue) == 'string' ? oldValue.trim() : oldValue;
		oldText = oldText.trim();
		staticObj.data('oldValue', oldValue).data('oldText', oldText);

		// Set up the form control
		if(editType == 'text') {
			formObj = $('<input type="text" value="' + oldValue + '">');
		} else if(editType == 'textarea') {
			formObj = $('<textarea rows="4">' +
			            oldValue.replace(new RegExp("\n", 'g'), "").replace(new RegExp('<br>', 'g'), "\n").replace(/<.*?>/g, '') +
			            '</textarea>');
		} else if(editType == 'select') {
			var src = $('[data-type="data-select-source"][data-select-name="' + staticObj.data('editSource') + '"]');
			if(src.length == 0) {
				console.log('No source found for control \'' + staticObj.data('controlName') + '\'');
				return;
			}
			formObj = $(src.html()).val(staticObj.data('value'));
		} else if(editType == 'toggle') {
			formObj = $('<input type="hidden" value="' + !oldValue + '">');
		} else {
			return;
		}

		// Replace the static object with the form control
		formObj.insertAfter(staticObj).attr('class', 'form-control').attr('name', staticObj.data('controlName')).focus();
		staticObj.hide();

		// Register the event handler
		if(editType == 'toggle') {
			handleValueChange(event, staticObj, formObj);
		} else {
			formObj.on('blur', function (event) {
				handleValueChange(event, staticObj, formObj);
			});
			if(editType == 'text' || editType == 'textarea') {
				formObj.on('keypress', function (event) {
					if(event.which == 13 && (event.shiftKey || editType == 'text')) {
						event.preventDefault();
						event.stopPropagation();
						handleValueChange(event, staticObj, formObj);
					}
				});
			} else if(editType == 'select') {
				formObj.on('change', function () {
					handleValueChange(event, staticObj, formObj);
				});
			}
		}
	});
})(jQuery);