<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:58 PM
 */
?>
<?php
$is_editable = in_array( $order->get_status(), array( 'on-hold', 'gift-addressing' ) );
$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
?>

<h4>
	<?php
	//		error_log( print_r( $order, true ) );
	echo sprintf( esc_html( '%1$s님으로부터 %2$s 받은 선물 입니다.', 'wc-ace' ),
		$order->get_billing_first_name(), esc_html( wc_format_datetime( $order->get_date_created(), 'Y\년 m\월 d\일' ) )
	);
	?>
</h4>

<p>
    주소를 입력하시면 선물을 배송받을 수 있습니다.
</p>

<div class="gift-items">
    <ul class="products columns-4">
		<?php
		foreach ( $order_items as $item_id => $item ) {
			$product = $item->get_product();

			wc_ace_get_template( 'gift/gift-item.php', array(
				'order'   => $order,
				'item_id' => $item_id,
				'item'    => $item,
				'product' => $product,
			) );
		}
		?>
    </ul>
</div>

<p>
    현재 처리상태: <?php echo get_post_status_object( 'wc-' . $order->get_status() )->label; ?><br>
    처리상태가 선물 배송주소 입력 중인 경우에만 주소를 변경 할 수 있습니다.<br>
</p>

<form name="gift" method="post" class="gift wc_ace_shipping_form" enctype="multipart/form-data">

    <div class="woocommerce-shipping-fields">

        <div class="shipping_address">

            <div class="woocommerce-shipping-fields__field-wrapper">
				<?php

				$shipping_fields = apply_filters( 'wc_ace_gift_shipping_address_fields', array(
					'shipping' => WC()->countries->get_address_fields( '', 'shipping_' ),
				) );

				foreach ( $shipping_fields['shipping'] as $key => $field ) {
					if ( is_callable( array( $order, 'get_' . $key ) ) ) {
						$value = $order->{"get_$key"}();
					} else {
						$value = $order->get_meta( '_' . $key );
					}
					if ( ! $is_editable ) {
						$field['custom_attributes']['readonly'] = 'readonly';
					}
					woocommerce_form_field( $key, $field, $value );
				}
				?>
				<?php
				if ( $is_editable ) {
					echo '<button type="submit" name="" value="" class="button alt">주소저장 및 선물배송요청</button>';
				} else {
					echo '<a href="/" class="button alt">홈페이지가기</a>';
				}
				?>

            </div>

        </div>

    </div>

</form>