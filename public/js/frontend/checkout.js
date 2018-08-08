/* global wc_ace_checkout_params */
/* global daum */
jQuery( function ( $ ) {

    if ( typeof wc_ace_checkout_params === 'undefined' ) {
        return false;
    }

    var wc_ace_checkout_form = {
        $checkout_form: $( 'form.wc_ace_shipping_form' ),
        init: function () {
        }
    };

    wc_ace_checkout_form.init();
} );
