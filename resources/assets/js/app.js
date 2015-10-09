function processAjaxErrors(data, form) {
	if(typeof(data.responseJSON) == "object") {
		var errors = data.responseJSON;
		if(typeof(errors.error) == 'string') {
			alert("Sorry, an error occurred\n\n\"" + errors.error + "\"");
			return;
		} else if(typeof(form) == 'object') {
			form.find('input,textarea,select').each(function () {
				var input = $(this);
				var group = input.parents('.form-group');
				if(input.attr('name') in errors) {
					group.addClass('has-error');
					$('<p class="help-block errormsg">' + errors[input.attr('name')][0] + '</p>').insertAfter(input.parent().hasClass('input-group') ? input.parent() : input);
				} else {
					group.addClass('has-success');
				}
			});
			return;
		}
	}

	alert("Oops, an unknown error has occurred");
	console.log(data.responseText);
}
$.ajaxSetup({
	headers : {
		'X-XSRF-TOKEN': $('meta[name="xsrf-token"]').attr('content')
	},
	method  : "GET",
	dataType: "json"
});
$('select[select2]').each(function () {
	$(this).select2({placeholder: $(this).attr('select2') || ''})
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