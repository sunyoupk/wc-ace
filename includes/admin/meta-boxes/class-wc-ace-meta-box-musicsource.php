<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 13/07/2018
 * Time: 11:10 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Meta_Box_Musicsource
 */
class WC_Ace_Meta_Box_Musicsource {

	/**
	 * Output musicsource data meta box.
     *
     * @param WP_Post $post
	 */
	public static function output_data( $post ) {
		?>
        <div class="panel-wrap woocommerce">
            <div id="musicsource_data" class="panel wc-ace-musicsource-data">
                <div class="musicsource_data_column_container">
                    <div class="musicsource_data_column">
                        <h3>음원 정보</h3>
                        <p class="form-field form-field-wide">
                            <label for="order_date">아티스트:</label>
                            <input type="text" class="" name="order_date" maxlength="10" value=""/>
                        </p>
                    </div>
                    <div class="musicsource_data_column">
                        <h3>장르</h3>
                        <p class="form-field form-field-wide">
                            <label for="order_date">장르선택:</label>
                            <input type="text" class="" name="order_date" maxlength="10" value=""/>
                        </p>
                    </div>
                    <div class="musicsource_data_column">
                        <h3>라이선스</h3>
                        <p class="form-field form-field-wide">
                            <label for="order_date">Premium:</label>
                            <input type="text" class="" name="order_date" maxlength="10" value=""/>
                        </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
		<?php
	}

	/**
	 * Output musicsource license meta box.
	 */
	public static function output_license() {

	}

	/**
	 * Output musicsource files meta box.
	 */
	public static function output_files() {

	}

	/**
	 * Output musicsource action meta box.
	 */
	public static function output_action() {

	}
}