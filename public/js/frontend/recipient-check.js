/* global wc_ace_gift_recipient_params */

jQuery( function ( $ ) {
    if ( typeof wc_ace_gift_recipient_params === 'undefined' ) {
        return false;
    }
    $.blockUI.defaults.overlayCSS.cursor = 'default';

    var wc_ace_gift_recipient_check = {
        xhr: false,
        $recipient_form: $( 'form.wc-ace-gift-recipient-check' ),

        init: function () {
            this.$recipient_form.on( 'submit', this.submit );
            this.$recipient_form.on( 'input validate change', '.input-text, select, input:checkbox', this.validate_field );
        },

        validate_field: function ( e ) {
            var $this = $( this ),
                $parent = $this.closest( '.form-row' ),
                validated = true,
                validate_required = $parent.is( '.validate-required' ),
                validate_email = $parent.is( '.validate-email' ),
                event_type = e.type;

            if ( 'input' === event_type ) {
                $parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email woocommerce-validated' );
            }

            if ( 'validate' === event_type || 'change' === event_type ) {

                if ( validate_required ) {
                    if ( 'checkbox' === $this.attr( 'type' ) && !$this.is( ':checked' ) ) {
                        $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                        validated = false;
                    } else if ( $this.val() === '' ) {
                        $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                        validated = false;
                    }
                }

                if ( validate_email ) {
                    if ( $this.val() ) {
                        /* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
                        var pattern = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );

                        if ( !pattern.test( $this.val() ) ) {
                            $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-email' );
                            validated = false;
                        }
                    }
                }

                if ( validated ) {
                    $parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email' ).addClass( 'woocommerce-validated' );
                }
            }
        },

        submit: function () {
            var $form = $( this );

            if ( $form.is( '.processing' ) ) {
                return false;
            }

            $form.addClass( 'processing' );
            $form.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );

            var $required_inputs = $( wc_ace_gift_recipient_check.$recipient_form ).find( '.address-field.validate-required:visible' ),
                has_full_address = true;

            if ( $required_inputs.length ) {
                $required_inputs.each( function () {
                    if ( $( this ).find( ':input' ).val() === '' ) {
                        has_full_address = false;
                    }
                } );
            }

            var data = {
                security: wc_ace_gift_recipient_params.gift_recipient_auth_check_nonce,
                order_id: wc_ace_gift_recipient_params.order_id,
                is_gift: wc_ace_gift_recipient_params.is_gift,
                recipient_phone: $( 'input#recipient_phone' ).val(),
            };

            $.ajax( {
                type: 'POST',
                url: wc_ace_gift_recipient_params.wc_ace_ajax_url.toString().replace( '%%endpoint%%', 'gift_recipient_check' ),
                data: data,
                success: function ( result ) {
                    try {
                        if ( 'success' === result.result ) {
                            // Reload the page if requested
                            if ( true === result.reload ) {
                                window.location.reload();
                                return;
                            }

                        } else if ( 'failure' === result.result ) {
                            throw 'Result failure';

                        } else {
                            throw 'Invalid response';
                        }

                    } catch ( err ) {
                        // Remove notices from all sources
                        $( '.woocommerce-error, .woocommerce-message' ).remove();

                        // Add new errors
                        if ( result.messages ) {
                            wc_ace_gift_recipient_check.submit_error( result.messages );
                        } else {
                            wc_ace_gift_recipient_check.submit_error( '<div class="woocommerce-error">' + wc_ace_gift_recipient_params.i18n_error + '</div>' );
                        }

                        // Lose focus for all fields
                        $form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();

                        wc_ace_gift_recipient_check.scroll_to_notices();
                    }
                },
                error: function ( jqXHR, textStatus, errorThrown ) {
                    wc_ace_gift_recipient_check.submit_error( '<div class="woocommerce-error">' + errorThrown + '</div>' );
                }

            } );

            return false;
        },

        submit_error: function ( error_message ) {
            $( '.woocommerce-NoticeGroup-gift, .woocommerce-error, .woocommerce-message' ).remove();
            wc_ace_gift_recipient_check.$recipient_form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-gift">' + error_message + '</div>' );
            wc_ace_gift_recipient_check.$recipient_form.removeClass( 'processing' ).unblock();
            wc_ace_gift_recipient_check.$recipient_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
            wc_ace_gift_recipient_check.scroll_to_notices();
            $( document.body ).trigger( 'recipient_error' );
        },

        scroll_to_notices: function () {
            var scrollElement = $( '.woocommerce-NoticeGroup-updateShippingAddress, .woocommerce-NoticeGroup-gift' );

            if ( !scrollElement.length ) {
                scrollElement = $( '.form.checkout' );
            }
            $.scroll_to_notices( scrollElement );
        }

    };


    wc_ace_recipient_auth_check.init();

} );
