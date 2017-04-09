(function ($) {
    var search_input = $('.search-tools .search input[type="text"]');
    var base_url = $('meta[name="filter-base-url"]').attr('content');
    var base_query = $('meta[name="filter-base-query"]').attr('content');
    
    // Add the placeholder
    search_input.attr('placeholder', 'Search ...');
    
    // Listen for the submit
    search_input.on('keyup', function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            e.preventDefault();
            window.location = base_url + '?' + $.param($.extend(base_query, {search: search_input.val()}));
        }
    });
})(jQuery);