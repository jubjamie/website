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