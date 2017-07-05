if (typeof(window.console) == 'undefined') {
    var console = {
        log: function (str) {
        }
    }
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'TZ-OFFSET': (new Date).getTimezoneOffset(),
    },
    method: 'GET',
    dataType: 'json',
});

function processAjaxErrors(data) {
    var error = {
        code: 500,
        key: '',
        message: 'Oops, an unknown error has occurred',
        isList: false,
    };

    // Check for a detailed error message
    if (typeof(data.responseJSON) == 'object') {
        var response_error = data.responseJSON;

        // Status
        error.code = data.status;

        // Error code
        if (typeof(response_error.error_code) != 'undefined') {
            error.key = response_error.error_code;
        }

        // Error message
        if (typeof(response_error.__error) != 'undefined') {
            error.message = response_error.error;
        } else {
            error.message = response_error;
            error.isList = true;
        }
    }

    return error;
}
new Clipboard('[data-clipboard-target]');
$('body').on('click', '[data-clipboard-target]', function () {
    $.notification({
        level: 'info',
        message: 'Copied to clipboard'
    });
});
function datetimepicker(input, options) {
    var format = input.data('dateFormat') ? input.data('dateFormat') : 'YYYY-MM-DD';
    var parent = input.parent();
    var datetimepickerOptions = {
        format: format,
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-bullseye',
            clear: 'fa fa-trash',
            close: 'fa fa-remove'
        },
        showTodayButton: true,
    };
    if (typeof(options) == 'object') {
        datetimepickerOptions = $.extend({}, datetimepickerOptions, options);
    }

    if (parent.hasClass('input-group')) {
        return parent.addClass('date').datetimepicker(datetimepickerOptions);
    } else {
        return input.datetimepicker(datetimepickerOptions);
    }
}
$('input[data-input-type="datetimepicker"]').each(function (i, obj) {
    datetimepicker($(obj));
});
$('body').on('submit', 'form', function (event) {
    var form = $(event.target);
    form.append('<input type="hidden" name="TZ-OFFSET" value="' + (new Date).getTimezoneOffset() + '">');
});
window.$modal = new Modal('#modal');
$modal.errorProcessor = processAjaxErrors;

$modal.onShow(function() {
    // Reload datetimepicker widget
    $modal.find('input[data-input-type="datetimepicker"]').each(function (i, obj) {
        var el = datetimepicker($(obj), {
            widgetParent: $('body'),
        });

        el.on('dp.show', function () {
            $('.bootstrap-datetimepicker-widget').offset({
                left: el.offset().left,
                top: el.offset().top + el.outerHeight()
            });
        });
    });

    // Re-trigger any visibility toggles
    $('[data-type="toggle-visibility"]').trigger('change');
});
$('select[select2]').each(function () {
    $(this).select2({
        placeholder: $(this).attr('select2') || '',
        theme: 'bootstrap',
    })
});