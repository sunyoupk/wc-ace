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
        selectedShippingAddressMethod: false,
        selectedPaymentMethod: false,
        xhr: false,
        $order_review: $( '#order_review' ),
        $ace_form: ( wc_ace_gift_params.is_checkout == 1 ) ? $( 'form.checkout' ) : $( 'form.wc_ace_shipping_form' ),

        init: function () {
            this.shipping_address_form_required();

            this.$ace_form.on( 'click', 'input[name="shipping_address_method"]', this.shipping_address_method_selected );

            this.$ace_form.on( 'change', '#ship-to-different-address input', this.ship_to_different_address );

            if ( wc_ace_gift_params.is_editable || wc_ace_gift_params.is_checkout ) this.append_search_postcode();
            this.$ace_form.on( 'click', '.btn-search-postcode', this.search_postcode );

            this.$ace_form.on( 'submit', this.submit );

            this.$ace_form.on( 'input validate change', '.input-text, select, input:checkbox', this.validate_field );

            // this.$ace_form.find( '#ship-to-different-address input' ).change();

            this.init_shipping_address_methods();
        },

        init_shipping_address_methods: function () {
            var $shipping_address_methods = this.$ace_form.find( 'input[name="shipping_address_method"]' );

            // If there is one method, we can hide the radio input
            if ( 1 === $shipping_address_methods.length ) {
                $shipping_address_methods.eq( 0 ).hide();
            }

            // If there was a previously selected method, check that one.
            if ( wc_ace_gift_form.selectedShippingAddressMethod ) {
                $( '#' + wc_ace_gift_form.selectedShippingAddressMethod ).prop( 'checked', true );
            }

            // If there are none selected, select the first.
            if ( 0 === $shipping_address_methods.filter( ':checked' ).length ) {
                $shipping_address_methods.eq( 0 ).prop( 'checked', true );
            }

            if ( $shipping_address_methods.length > 1 ) {
                // Hide open descriptions.
                $( 'div.shipping_address_box' ).filter( ':visible' ).slideUp( 0 );
            }

            // Trigger click event for selected method
            $shipping_address_methods.filter( ':checked' ).eq( 0 ).trigger( 'click' );
        },

        ship_to_different_address: function () {
            $( 'div.shipping-address-method-fields' ).hide();
            var is_checked = $( this ).is( ':checked' );
            if ( is_checked ) {
                $( 'div.shipping-address-method-fields' ).slideDown();
            }

            $( '.woocommerce-billing-fields' ).find( 'p.address-field' ).each( function ( i, field ) {
                if ( is_checked ) {
                    // Hide billing address fields.
                    $( field ).filter( ':visible' ).slideUp( 0 );
                    $( field ).removeClass( 'validate-required' );
                } else {
                    $( field ).slideDown( 230 );
                    $( field ).addClass( 'validate-required' );
                    if ( $( field ).find( 'label .required' ).length === 0 ) {
                        // todo 필수 translate or parameter required!
                        $( field ).find( 'label' ).append( '<abbr class="required" title="' + '필수' + '">*</abbr>' );
                    }
                }
                $( field ).find( 'label .optional' ).hide();
            } );
        },

        shipping_address_method_selected: function ( e ) {
            e.stopPropagation();

            if ( $( '.shipping_address_methods input.input-radio' ).length > 1 ) {
                var target_shipping_address_box = $( 'div.shipping_address_box.' + $( this ).attr( 'ID' ) ),
                    is_checked = $( this ).is( ':checked' );

                if ( is_checked && !target_shipping_address_box.is( ':visible' ) ) {
                    $( 'div.shipping_address_box' ).filter( ':visible' ).slideUp( 230 );

                    if ( is_checked ) {
                        target_shipping_address_box.slideDown( 230 );
                    }
                }
            } else {
                $( 'div.shipping_address_box' ).show();
            }

            // Show shipping address fields(only direct).
            var selected_value = $( this ).val();
            if ( selected_value === 'email' ) {
                var $field = $( 'p#shipping_email_field' );
                $field.addClass( 'validate-required' );
                if ( $field.find( 'label .required' ).length === 0 ) {
                    // todo-namepace 필수
                    $field.find( 'label' ).append( '<abbr class="required" title="' + '필수' + '">*</abbr>' );
                    $field.find( 'label .optional' ).hide();
                }

            } else {
                var $field = $( 'p#shipping_email_field' );
                $field.removeClass( 'validate-required' );
                $field.find( 'label .optional' ).show();
                $field.find( 'label .required' ).remove();

                $( '.shipping_address' ).find( 'p.address-field' ).each( function ( i, field ) {
                    if ( selected_value === 'direct' ) {
                        $( field ).slideDown( 230 );
                        $( field ).addClass( 'validate-required' );
                        if ( $( field ).find( 'label .required' ).length === 0 ) {
                            $( field ).find( 'label' ).append( '<abbr class="required" title="' + '필수' + '">*</abbr>' );
                        }

                    } else {
                        if ( wc_ace_gift_params.is_checkout == 1 ) {
                            $( field ).filter( ':visible' ).slideUp( 0 );
                        }
                        $( field ).removeClass( 'validate-required' );
                    }
                    $( field ).find( 'label .optional' ).hide();
                } );
            }

            var selectedShippingAddressMethod = $( '.woocommerce-checkout input[name="shipping_address_method"]:checked' ).attr( 'id' );

            if ( selectedShippingAddressMethod !== wc_ace_gift_form.selectedShippingAddressMethod ) {
                $( document.body ).trigger( 'shipping_address_method_selected' );
            }

            wc_ace_gift_form.selectedShippingAddressMethod = selectedShippingAddressMethod;
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

            var $required_inputs = $( wc_ace_gift_form.$ace_form ).find( '.address-field.validate-required:visible' ),
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
            wc_ace_gift_form.$ace_form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-gift">' + error_message + '</div>' );
            wc_ace_gift_form.$ace_form.removeClass( 'processing' ).unblock();
            wc_ace_gift_form.$ace_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
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
        $ace_form: $( 'form.gift' ),
        init: function () {
            Kakao.init( '68dc5c89516e337ab29f7589d679f850' );
            this.$ace_form.on( 'click', '#btn_send_kakao', this.send_shipping_address_form );
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
