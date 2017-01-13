$(document).ready(function() {
	// disable submit button on form submit
	$('form').submit(function (event) {
		if ($(this).hasClass('submitted')) {
			event.preventDefault();
		}
		else {
			$(this).find('[type="submit"]').each(function() {
				$(this).html('<i class="fa fa-spinner fa-spin"></i> '+$(this).html());
			});

			$(this).addClass('submitted');
		}
	});

	// populate each datatable which exists
	var datatable = $('[data-datatable]');

	if (datatable.length) {
		datatable.each(function () {
			$(this).DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					url: $(this).data('datatable'),
					type: 'POST'
				},
				stateSave: true,
				stateDuration: 0,
				stateSaveParams: function (settings, data) {
					if ($(this).data('datatable-state') == 'partial') {
						data.search.search = '';
						data.start = 0;
					}
				},
				columnDefs: [{
					targets: 'actions-column',
					className: 'actions-column',
					orderable: false
				}],
				responsive: true,
				lengthMenu: [10, 25, 50, 100, 200, 500],
				pageLength: 50
			});
		});
	}

	// bootstrap tooltips
	$('body').tooltip({
		selector: '[data-toggle="tooltip"]',
		trigger: 'hover'
	});

	// confirm action
	$(document).on('click', '[data-confirm]', function (event) {
		if (!confirm($(this).data('confirm')) == true) {
			event.preventDefault();
		}
	});

	// check all checkboxes with name
	$('[data-check]').click(function () {
		$(this).closest('form').find('[name="'+$(this).data('check')+'"]').each(function () {
			$(this).prop('checked', true).change();
		});
	});

	// uncheck all checkboxes with name
	$('[data-uncheck]').click(function () {
		$(this).closest('form').find('[name="'+$(this).data('uncheck')+'"]').each(function () {
			$(this).prop('checked', false).change();
		});
	});

	// show/hide div when select changed
	$('[data-showhide]').change(function () {
		var showhide = $(this).data('showhide');
		var show = $(this).find(':selected').data('show');

		$(showhide).hide();
		$(show).show();
	});

	// show modal with dynamic content
	$(document).on('click', '[data-modal-target]', function () {
		var modal_target = $(this).data('modal-target');

		$.get($(this).data('modal-href'), function (data) {
			$(modal_target+' .modal-body').html(data);
			$(modal_target).modal('show');
		});
	});

	// make adminlte treeviews clickable if href is not #
	$('.treeview a').click(function (e) {
		if ($(this).attr('href') != '#') {
			e.stopPropagation();
		}
	});
});