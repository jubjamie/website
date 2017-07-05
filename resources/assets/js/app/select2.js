$('select[select2]').each(function () {
    $(this).select2({
        placeholder: $(this).attr('select2') || '',
        theme: 'bootstrap',
    })
});