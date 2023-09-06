function Classified_confirm(modal_texts, classified_modal_confirm_callback) {
    let body_content = modal_texts.body ?? '';
    if (body_content !== '') {
        body_content = '<div class="modal-body">' + body_content + '</div>';
    }
    let modal_id = 'classified-confirm-modal',
        modal_html = `<div class="modal fade" id="${modal_id}" tabindex="-1" aria-labelledby="${modal_id}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <i class="fa-solid fa-xmark classified-close-modal" aria-hidden="true" data-bs-dismiss="modal" aria-label="Close"></i>
                    <div class="modal-header">
                        <h4>${modal_texts.heading}</h4>
                    </div>
                    ${body_content}
                    <div class="modal-footer">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <button class="classified-not-filled-btn ${modal_id}-confirm">
                                ${modal_texts.confirm}
                            </button>
                            <button class="classified-filled-btn ${modal_id}-cancel">
                                ${modal_texts.cancel}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    jQuery('#' + modal_id).remove();
    let modal_element = jQuery(modal_html);
    modal_element.modal('show');

    modal_element.find('.' + modal_id + '-confirm').on('click', function (event) {
        event.preventDefault();
        classified_modal_confirm_callback( modal_element, jQuery(this) );
    });

    modal_element.find('.' + modal_id + '-cancel').on('click',  function (event) {
        event.preventDefault();
        modal_element.modal('hide', function () {
            jQuery('#' + modal_id).remove();
        });
    });
}
