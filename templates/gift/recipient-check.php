<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:58 PM
 */
?>

<h4>
	선물 수령 본인확인
</h4>
<p>
    휴대전화 번호를 입력해 주세요,<br>
    선물을 보내시는 분이 입력한 번호와 일치해야 합니다.
</p>

<form name="recipient-check" method="post" class="gift wc-ace-gift-recipient-check" enctype="multipart/form-data">

    <div class="woocommerce-shipping-fields__field-wrapper">
        <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <label for="order_shipping_phone">휴대전화번호</label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="order_shipping_phone" id="order_shipping_phone" >
        </p>
        <div class="clear"></div>
        <p>
            <button type="submit" class="button alt">본인인증</button>
        </p>
    </div>

</form>