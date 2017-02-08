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
		format: format,
		icons : {
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