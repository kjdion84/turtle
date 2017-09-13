// default datatable settings
$.extend(true, $.fn.dataTable.defaults, {
    columnDefs: [
        {
            targets: 'actions',
            className: 'actions',
            searchable: false,
            sortable: false
        }
    ],
    lengthMenu: [5, 10, 25, 50, 100, 250, 500],
    pageLength: 25,
    language: { search: '' },
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
    initComplete: function (settings, json) {
        var self = this.api();
        var filter_input = $('#' + settings.nTable.id + '_filter input').unbind();
        var search_button = $('<button type="button" class="btn btn-primary btn-sm ml-1 mb-1" data-toggle="tooltip" title="Search"><i class="fa fa-search"></i></button>').click(function () {
            self.search(filter_input.val()).draw();
        });
        var reset_button = $('<button type="button" class="btn btn-light btn-sm ml-1 mb-1" data-toggle="tooltip" title="Reset"><i class="fa fa-undo"></i></button>').click(function () {
            filter_input.val('');
            search_button.click();
        });

        $(document).keypress(function (event) {
            if (event.which === 13) {
                search_button.click();
            }
        });

        $('#' + settings.nTable.id + '_filter').append(search_button, reset_button);
    }
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

    // perform on ajax complete
    $(document).ajaxComplete(function () {
        // hide tooltips
        $('.tooltip').tooltip('hide');

        // re-enable form submits
        var submitted = $('.submitted');

        submitted.find(':submit').each(function () {
            $(this).html($(this).data('original-html'));
            $(this).removeAttr('data-original-html');
            $(this).css('width', 'auto');
        });
        submitted.removeClass('submitted');
    });

    // ajax form processing
    $(document).on('submit', 'form', function (event) {
        event.preventDefault();

        var form = $(this);

        if (!form.hasClass('submitted')) {
            // disable extra form submits
            form.addClass('submitted');
            form.find(':submit').each(function () {
                $(this).css('width', $(this).outerWidth());
                $(this).attr('data-original-html', $(this).html());
                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            });

            // remove existing alert & invalid field info
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

                    // reload page
                    if (data.hasOwnProperty('reload_page')) {
                        location.reload();
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
        }
    });

    // remove invalid highlight on input
    $(document).on('keyup change', '.is-invalid', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.is-invalid-message').remove();
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