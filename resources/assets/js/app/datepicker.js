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