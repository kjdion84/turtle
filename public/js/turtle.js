// default datatable settings
$.extend(true, $.fn.dataTable.defaults, {
    lengthMenu: [5, 10, 25, 50, 100, 250, 500],
    pageLength: 25,
    processing: true,
    serverSide: true,
    stateSave: true,
    stateDuration: 0,
    stateSaveParams: function (settings, data) {
        data.search.search = '';
        data.start = 0;
    },
    stateSaveCallback: function (settings, data) {
        localStorage.setItem($(this).attr('id'), JSON.stringify(data));
    },
    stateLoadCallback: function () {
        return JSON.parse(localStorage.getItem($(this).attr('id')));
    },
    columnDefs: [
        {
            targets: 'actions',
            className: 'actions',
            searchable: false,
            sortable: false
        }
    ]
});

$(document).ready(function () {
    // flash success message if present
    var body = $('body');

    if (body.attr('data-flash-class')) {
        flash(body.attr('data-flash-class'), body.attr('data-flash-message'));
        body.removeAttr('data-flash-class').removeAttr('data-flash-message');
    }

    // init tooltips
    body.tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    // hide tooltips on ajax complete
    $(document).ajaxComplete(function () {
        $('.tooltip').tooltip('hide');
    });

    // ajax form processing
    $(document).on('submit', 'form', function (event) {
        event.preventDefault();

        var form = $(this);

        $('.alert-fixed').remove();
        $('.is-invalid').removeClass('is-invalid');
        $('.is-invalid-message').remove();

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: new FormData(form[0]),
            success: function (data) {
                // perform redirect
                if (data.hasOwnProperty('redirect')) {
                    $(location).attr('href', data.redirect);
                }

                // flash success message
                if (data.hasOwnProperty('flash')) {
                    flash(data.flash[0], data.flash[1]);
                }

                // dismiss modal
                if (data.hasOwnProperty('dismiss_modal')) {
                    form.closest('.modal').modal('toggle');
                }

                // reload datatables
                if (data.hasOwnProperty('reload_datatables')) {
                    $($.fn.dataTable.tables()).DataTable().ajax.reload();
                }
            },
            error: function (data) {
                var element;

                // show error for each element
                $.each(data.responseJSON.errors, function (key, value) {
                    element = (key === 'g-recaptcha-response') ? $('.g-recaptcha') : $('#' + key);
                    element.addClass('is-invalid');
                    element.after('<div class="is-invalid-message">' + value[0] + '</div>');
                });

                // reset recaptcha if present
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }

                // flash error message
                flash('danger', 'Errors have occurred.');
            }
        });
    });

    // show ajax modal with content
    $(document).on('click', '[data-modal]', function (event) {
        event.preventDefault();

        $.get($(this).data('modal'), function (data) {
            $(data).modal('show');
        });
    });

    // remove ajax modal when hidden
    $(document).on('hidden.bs.modal', '.modal-ajax', function () {
        $(this).remove();
    });

    // check/uncheck all checkboxes
    $(document).on('click', '[data-check]', function () {
        var checked = $(this).prop('checked');

        $(this).closest('form').find('[name="' + $(this).data('check') + '"]').each(function () {
            $(this).prop('checked', checked).change();
        });
    });
});

function flash(alert_class, alert_message) {
    var html = '<div class="alert alert-' + alert_class + ' alert-fixed mt-3 mb-0">' + alert_message + '</div>';

    $(html).appendTo('body').delay(3000).queue(function () {
        $(this).remove();
    });
}