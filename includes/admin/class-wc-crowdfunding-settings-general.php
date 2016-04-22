<?php
/**
 * Crowdfunding for WooCommerce - General Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_General' ) ) :

class Alg_WC_Crowdfunding_Settings_General {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id   = 'general';
		$this->desc = __( 'General', 'alg-woocommerce-crowdfunding' );

		add_filter( 'woocommerce_get_sections_alg_crowdfunding',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_crowdfunding_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 */
	function get_settings() {

		$settings = array(

			array(
				'title'     => __( 'Crowdfunding Options', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),

			array(
				'title'     => __( 'WooCommerce Crowdfunding', 'alg-woocommerce-crowdfunding' ),
				'desc'      => '<strong>' . __( 'Enable', 'alg-woocommerce-crowdfunding' ) . '</strong>',
				'desc_tip'  => __( 'Crowdfunding Products for WooCommerce.', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),

			array(
				'title'     => __( 'Crowdfunding Buttons Options', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),

			array(
				'title'     => __( 'Default Button Label on Single Product Page', 'alg-woocommerce-crowdfunding' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_single',
				'default'   => __( 'Back This Project', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),

			array(
				'title'     => __( 'Default Button Label on Archive Pages', 'alg-woocommerce-crowdfunding' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_archives',
				'default'   => __( 'Read More', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),

			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),

			array(
				'title'     => __( 'Crowdfunding Messages Options', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),

			array(
				'title'     => __( 'Default Message on Product Not Yet Started', 'alg-woocommerce-crowdfunding' ),
//				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_not_started',
				'default'   => __( '<strong>Not yet started!</strong>', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),

			array(
				'title'     => __( 'Default Message on Product Ended', 'alg-woocommerce-crowdfunding' ),
//				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_ended',
				'default'   => __( '<strong>Ended!</strong>', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'textarea',
				'css'       => 'width:300px;',
			),

			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),

			array(
				'title'     => __( 'Variable Add to Cart Form Options', 'alg-woocommerce-crowdfunding' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),

			array(
				'title'     => __( 'Radio Buttons for Variable Products', 'alg-woocommerce-crowdfunding' ),
				'desc'      => __( 'Enable', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			/* array(
				'title'     => __( 'Template', 'alg-woocommerce-crowdfunding' ),
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_template',
				'default'   => file_get_contents( untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/../..' ) ) . '/includes/alg-add-to-cart-variable.php' ),
				'type'      => 'custom_textarea',
				'css'       => 'width:90%;height:300px;',
			), */

			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_General();
