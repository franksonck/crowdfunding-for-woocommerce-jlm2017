<?php
/**
 * Crowdfunding for WooCommerce - Open Pricing Section Settings
 *
 * @version 2.2.0
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Open_Pricing' ) ) :

class Alg_WC_Crowdfunding_Settings_Open_Pricing {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	public function __construct() {

		$this->id   = 'open_pricing';
		$this->desc = __( 'Open Pricing (Name Your Price)', 'alg-woocommerce-crowdfunding' );

		add_filter( 'woocommerce_get_sections_alg_crowdfunding',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_crowdfunding_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_settings.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Labels and Messages', 'alg-woocommerce-crowdfunding' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
			array(
				'title'    => __( 'Frontend Label', 'alg-woocommerce-crowdfunding' ),
				'id'       => 'alg_crowdfunding_product_open_price_label_frontend',
				'default'  => __( 'Name Your Price', 'alg-woocommerce-crowdfunding' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Empty Price', 'alg-woocommerce-crowdfunding' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_required',
				'default'  => __( 'Price is required!', 'alg-woocommerce-crowdfunding' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Price to Small', 'alg-woocommerce-crowdfunding' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_small',
				'default'  => __( 'Entered price is to small!', 'alg-woocommerce-crowdfunding' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'title'    => __( 'Message on Price to Big', 'alg-woocommerce-crowdfunding' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_big',
				'default'  => __( 'Entered price is to big!', 'alg-woocommerce-crowdfunding' ),
				'type'     => 'text',
				'css'      => 'width:250px;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_Open_Pricing();
