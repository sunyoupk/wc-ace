/* global wc_ace_checkout_params */
/* global daum */
jQuery( function ( $ ) {

    if ( typeof wc_ace_checkout_params === 'undefined' ) {
        return false;
    }

    var wc_ace_checkout_form = {
        $checkout_form: $( 'form.checkout' ),
        init: function () {

            this.append_search_postcode();
            this.$checkout_form.on( 'click', '.btn-search-postcode', this.search_postcode );

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
                    if ( wc_ace_checkout_params.postcode_digit == '5' ) {
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

    wc_ace_checkout_form.init();
} );
