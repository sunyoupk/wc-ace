<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:58 PM
 */
?>

<h4><?php esc_html_e( '%s님으로부터 받은 선물 입니다.', 'wc-ace' ); ?></h4>
<p>주소를 입력하시면 선물을 배송받을 수 있습니다.</p>

<div class="wc-ace-gift-recieve-address-form">

    <div class="woocommerce-shipping-fields">

        <div class="shipping_address">

            <div class="woocommerce-shipping-fields__field-wrapper">
				<?php

				$shipping_fields = apply_filters( 'woocommerce_checkout_fields', array(
					'shipping' => WC()->countries->get_address_fields(
						'',
						'shipping_'
					),
				) );

				foreach ( $shipping_fields['shipping'] as $key => $field ) {
					woocommerce_form_field( $key, $field, null );
				}
				?>
            </div>

        </div>

    </div>

</div>