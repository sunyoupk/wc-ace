<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:58 PM
 */
?>

<h4>
	<?php
//	error_log( print_r( $order, true ) );

	echo sprintf( esc_html( '%1$s님으로부터 %2$s 받은 선물 입니다.', 'wc-ace' ),
		$order->get_billing_first_name(), esc_html( wc_format_datetime( $order->get_date_created() ) )
	);
	?>
</h4>
<p>
    주소를 입력하시면 선물을 배송받을 수 있습니다.
</p>
<p>
    처리상태: 배송주소 입력 대기중<br>
    처리상태가 배송주소 입력 대기중 인 경우 배송주로를 변경 할 수 있습니다.
</p>

<form name="gift" method="post" class="gift wc-ace-gift"
      enctype="multipart/form-data">

    <div class="woocommerce-shipping-fields">

        <div class="shipping_address">

            <div class="woocommerce-shipping-fields__field-wrapper">
				<?php

				$shipping_fields = apply_filters( 'wc_ace_gift_shipping_address_fields', array(
					'shipping' => WC()->countries->get_address_fields(
						'',
						'shipping_'
					),
				) );

				foreach ( $shipping_fields['shipping'] as $key => $field ) {
                    $value = '';
					if ( is_callable( array( $order, 'get_' . $key ) ) ) {
						$value = $order->{"get_$key"}();
					}
					woocommerce_form_field( $key, $field, $value );
				}
				?>

                <button type="submit" name="" value="" class="button alt">주소저장</button>
            </div>

        </div>

    </div>

</form>