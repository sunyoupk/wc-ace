<?php
/**
 * Created by PhpStorm.
 * User: bangrang
 * Date: 15/11/2018
 * Time: 6:47 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}
?>

<?php
$is_visible = $product && $product->is_visible();
$thumbnail  = $product->get_image( 'thumbnail' );
?>

<li class="product">
    <?php echo $thumbnail; ?>
    <h2 class="">
        <?php
        echo wp_kses_post( $product->get_name() );
        ?>
    </h2>
</li>
