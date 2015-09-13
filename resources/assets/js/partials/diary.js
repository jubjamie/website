$modal.on('click', '#submitDateModal', function () {
	$modal.find('button').attr('disabled', 'disabled');
	var form = $modal.find('form');
	window.location = $(this).data('url')
		.replace('%year', form.find('select[name="year"]').val())
		.replace('%month', form.find('select[name="month"]').val());
});
//$modal.on('show.bs.modal', function(event) {
//	var btn = $(event.relatedTarget);
//	if(btn.data('modalTemplate') == 'date_gantt') {
//		var header = $modal.find('div.modal-header').children('h1');
//		header.text(btn.data('date'));
//
//
//	}
//	console.log(btn);
//});
$('#diary').on('click', '.cell a', function(event) {
	event.stopPropagation();
});