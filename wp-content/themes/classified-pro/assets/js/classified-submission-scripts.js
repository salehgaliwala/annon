(function ($) {
    'use strict';
    let $document = $(document), ad_submission_form = $('.cwp-user-form-submit'),
        ad_submission_preview = $('.classified-submission-form-sidebar'), number_fields = $('input[type=number]');

    if (number_fields.length > 0) {
        number_fields.on('input', function () {
            var $this = $(this), value = $this.val();
            if ((isNaN(value) || value < 0)) {
                $this.prop('value', 0).trigger('input');
            }
            if (/^[0-9]*$/.test(value) === false) {
                var _value = value.replace(/ /g, "");
                _value = _value.replace(/-/g, "");
                _value = _value.toLowerCase();
                _value = replaceSpecialCharacters(_value);
                if (value !== _value) {
                    $this.prop('value', value).trigger('input');
                }
            }
        });
    }

    if (ad_submission_preview.length > 0) {
        ad_submission_preview.on('click', function (event) {
            event.preventDefault();
        });
    }

    if (ad_submission_form.length > 0) {
        var ad_submission_form_actions = ad_submission_form.find('.classified-submission-form-actions button');
        if (ad_submission_form_actions.length > 0) {
            ad_submission_form_actions.on('click', function (event) {
                var $this = $(this), actions = $this.closest('.classified-submission-form-actions'),
                    action_next = actions.find('.classified-submission-form-action-next'),
                    action_back = actions.find('.classified-submission-form-action-back'),
                    sections = ad_submission_form.find('.classified-submission-form-section'),
                    active_section = ad_submission_form.find('.classified-submission-form-section-active'),
                    sections_count = sections.length, active_section_number;

                if (!$this.hasClass('cwp-from-submit')) {
                    event.preventDefault();
                    sections.removeClass('classified-submission-form-section-active');
                    jQuery('html, body').animate({
                        scrollTop: jQuery(".cwp-user-form-submit").offset().top - 100
                    }, 200);
                    if ($this.hasClass('classified-submission-form-action-next')) {
                        active_section.next('.classified-submission-form-section').addClass('classified-submission-form-section-active');
                    }
                    if ($this.hasClass('classified-submission-form-action-back')) {
                        active_section.prev('.classified-submission-form-section').addClass('classified-submission-form-section-active');
                    }
                    active_section = ad_submission_form.find('.classified-submission-form-section-active');
                    active_section_number = active_section.attr('data-section-counter');
                    if (active_section_number < sections_count) {
                        action_next.prop('disabled', false);
                    } else {
                        action_next.prop('disabled', true);
                    }
                    if (active_section_number <= sections_count && active_section_number > 1) {
                        action_back.prop('disabled', false);
                    } else {
                        action_back.prop('disabled', true);
                    }
                }
            });

            $document.ready(function () {
                /**
                 * Editor Description Live Preview
                setInterval(function () {
                    var the_content = $(tinymce.get("the_content").getContent()).text();
                    the_content = the_content.length > 130 ? the_content.substring(0, 130 - 3) + "..." : the_content;
                    if (the_content !== '') $('.classified-preview-the_content').text(the_content);
                }, 1000);
                */
                ad_submission_form.find('input,textarea').on('input', function () {
                    classified_build_preview($(this));
                });
                ad_submission_form.find('select').on('change', function () {
                    classified_build_preview($(this));
                });

                $(document).on('[data-id=classified_buyable] input', 'input', function(){
                    var $text = $("[data-id=classified_buyable] input:checked").val();
                    if ($text === 'yes') {
                        $('.classified-item-buyable').removeClass('opacity-0');
                    }else {
                        $('.classified-item-buyable').addClass('opacity-0');
                    }
                });

                ad_submission_form.on('change', function () {
                    if (classified_form_validation(ad_submission_form) === true) {
                        ad_submission_form.find('.classified-submission-form-action-submit').removeAttr('disabled', false);
                    } else {
                        ad_submission_form.find('.classified-submission-form-action-submit').prop('disabled', true);
                    }
                });

                function classified_build_preview($this) {
                    var name = $this.attr('name'), explode_name = name.split("["), target = $(this),
                        class_name = explode_name[explode_name.length - 1], tag_name = $this.prop("tagName"), $text;
                    if (class_name === ']') class_name = explode_name[explode_name.length - 2];
                    class_name = class_name.replace("]", "");

                    if (typeof class_name === "undefined" || class_name === '') return false;
                    class_name = '.classified-preview-' + class_name;
                    target = $(class_name);
                    if (target.length > 0) {
                        if (tag_name === "SELECT") {
                            $text = $this.find(":selected").text();
                            $text = $text.replace("-", "");
                        } else if ($this.attr('type') === "radio") {
                            $text = $("input[name='" + $this.attr('name') + "']:checked").parent('div').find('label').text();
                        } else if ($this.attr('type') === "file") {
                            var file = $this.prop('files')[0], previewTempUrl = new FileReader();
                            previewTempUrl.onload = function () {
                                target.attr('src', previewTempUrl.result);
                            };
                            previewTempUrl.readAsDataURL(file);
                        } else {
                            $text = $this.val();
                        }
                        target.text($text);
                    } else {
                        return false;
                    }
                }

                function classified_form_validation(form) {
                    var $return = true;
                    form.find(".is-required:not(:hidden)").each(function () {
                        if ($return === true) {
                            var _thisObj = jQuery(this);
                            _thisObj.find('.cwp-required-field-notice').remove();
                            var type = _thisObj.data('type'), id = _thisObj.data('id'), val = '';
                            if (type === 'wysiwyg_editor') {
                                val = tinymce.get(id).getContent();
                            } else if (type === 'checkbox') {
                                _thisObj.find('.required:checked').each(function () {
                                    val = 1;
                                });
                            } else if (type === 'radio') {
                                _thisObj.find('.required:checked').each(function () {
                                    val = 1;
                                });
                            } else {
                                val = _thisObj.find('.required').val();
                            }
                            if (val === '') {
                                $return = false;
                            }
                        }
                    });
                    return $return;
                }
            })

        }
    }
})(jQuery);