/* global wc_ace_gift_params */
/* global Kakao */
jQuery( function ( $ ) {

    if ( typeof wc_ace_gift_params === 'undefined' ) {
        return false;
    }

    $.blockUI.defaults.overlayCSS.cursor = 'default';

    var wc_ace_gift_form = {
        updateTimer: false,
        dirtyInput: false,
        selectedPaymentMethod: false,
        xhr: false,
        $order_review: $( '#order_review' ),
        $gift_form: $( 'form.wc_ace_shipping_form' ),

        init: function () {
            this.shipping_address_form_required();

            // Postcode search
            if ( wc_ace_gift_params.is_editable )  this.append_search_postcode();
            this.$gift_form.on( 'click', '.btn-search-postcode', this.search_postcode );

            // Form submission
            this.$gift_form.on( 'submit', this.submit );

            this.$gift_form.on( 'input validate change', '.input-text, select, input:checkbox', this.validate_field );
        },

        shipping_address_form_required: function () {
            $( '.woocommerce-shipping-fields' ).find( 'p.address-field' ).each( function ( i, field ) {
                if ( $( field ).find( 'label .required' ).length === 0 ) {
                    // todo 필수 translate or parameter required!
                    // $( field ).find( 'label' ).append( '&nbsp;<abbr class="required" title="' + '필수' + '">*</abbr>' );
                }
                $( field ).find( 'label .optional' ).remove();
            } );
        },

        reset_update_gift_timer: function () {
            clearTimeout( wc_ace_gift_form.updateTimer );
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

            var $required_inputs = $( wc_ace_gift_form.$gift_form ).find( '.address-field.validate-required:visible' ),
                has_full_address = true;

            if ( $required_inputs.length ) {
                $required_inputs.each( function () {
                    if ( $( this ).find( ':input' ).val() === '' ) {
                        has_full_address = false;
                    }
                } );
            }

            var data = {
                security: wc_ace_gift_params.gift_update_shipping_address_nonce,
                order_id: wc_ace_gift_params.order_id,
                is_recipient: wc_ace_gift_params.is_gift,
                is_gift: $( '#ship-to-different-address' ).find( 'input' ).is( ':checked' ) || wc_ace_gift_params.is_gift,
                shipping_first_name: $( 'input#shipping_first_name' ).val(),
                shipping_phone: $( 'input#shipping_phone' ).val(),
                shipping_postcode: $( 'input#shipping_postcode' ).val(),
                shipping_address_1: $( 'input#shipping_address_1' ).val(),
                shipping_address_2: $( 'input#shipping_address_2' ).val(),
                shipping_address_method: $( '#ship-to-different-address' ).find( 'input' ).is( ':checked' ) ? $form.find( 'input[name="shipping_address_method"]:checked' ).val() : '',
                has_full_address: has_full_address
            };

            $.ajax( {
                type: 'POST',
                url: wc_ace_gift_params.wc_ace_ajax_url.toString().replace( '%%endpoint%%', 'gift_update_shipping_address' ),
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
                            wc_ace_gift_form.submit_error( result.messages );
                        } else {
                            wc_ace_gift_form.submit_error( '<div class="woocommerce-error">' + wc_ace_gift_params.i18n_checkout_error + '</div>' );
                        }

                        // Lose focus for all fields
                        $form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();

                        wc_ace_gift_form.scroll_to_notices();
                    }
                },
                error: function ( jqXHR, textStatus, errorThrown ) {
                    wc_ace_gift_form.submit_error( '<div class="woocommerce-error">' + errorThrown + '</div>' );
                }

            } );

            return false;
        },

        submit_error: function ( error_message ) {
            $( '.woocommerce-NoticeGroup-gift, .woocommerce-error, .woocommerce-message' ).remove();
            wc_ace_gift_form.$gift_form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-gift">' + error_message + '</div>' );
            wc_ace_gift_form.$gift_form.removeClass( 'processing' ).unblock();
            wc_ace_gift_form.$gift_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
            wc_ace_gift_form.scroll_to_notices();
            $( document.body ).trigger( 'gift_error' );
        },

        scroll_to_notices: function () {
            var scrollElement = $( '.woocommerce-NoticeGroup-updateShippingAddress, .woocommerce-NoticeGroup-gift' );

            if ( !scrollElement.length ) {
                scrollElement = $( '.form.checkout' );
            }
            $.scroll_to_notices( scrollElement );
        },

        append_search_postcode: function() {
            $( '#billing_address_1_field > label' ).append(
                '<a data-type="billing" class="btn-search-postcode" style="cursor:pointer;"><i class="fa fa-search"></i></a>'
            );
            $( '#shipping_address_1_field > label' ).append(
                '<a data-type="shipping" class="btn-search-postcode" style="cursor:pointer;"><i class="fa fa-search"></i></a>'
            );
        },

        search_postcode: function () {
            var type = $( this ).data( 'type' );
            new daum.Postcode( {
                oncomplete: function ( data ) {
                    if ( wc_ace_gift_params.postcode_digit == '5' ) {
                        $( '#' + type + '_postcode' ).val( data.zonecode );
                    } else {
                        $( '#' + type + '_postcode' ).val( data.postcode );
                    }
                    $( '#' + type + '_address_1' ).val( data.address );

                    $( '#' + type + '_address_2' ).focus();
                }
            } ).open();
        }

    };

    var kakao_api = {
        $gift_form: $( 'form.gift' ),
        init: function () {
            Kakao.init( '68dc5c89516e337ab29f7589d679f850' );
            this.$gift_form.on( 'click', '#btn_send_kakao', this.send_shipping_address_form );
        },

        send_shipping_address_form: function () {
            // Kakao.Link.sendScrap({
            //     requestUrl: 'http://wc.local:8080/gift/161/',
            //     templateId: 11361,
            //     templateArgs: {
            //         'title': '홍길동님께서 선물을 보내셨습니다.',
            //         'description': '주소를 입력하시면 선물을 배송받을 수 있습니다.',
            //         'url': 'http://wc.local:8080/gift/161/'
            //     }
            // });
            // Kakao.Link.sendCustom({
            //     templateId: 11361,
            //     templateArgs: {
            //         'title': '홍길동님께서 선물을 보내셨습니다.',
            //         'description': '주소를 입력하시면 선물을 배송받을 수 있습니다.',
            //         'url': 'http://wc.local:8080/gift/161/'
            //     }
            // });
        }

    };

    wc_ace_gift_form.init();
    kakao_api.init();
} );
