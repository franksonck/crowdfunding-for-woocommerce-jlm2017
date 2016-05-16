<?php
/**
 * Crowdfunding for WooCommerce
 *
 * @version 2.2.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding' ) ) :

class Alg_WC_Crowdfunding {

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled' ) ) {

			/* // Author
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_author_enabled', 'no' ) ) {
				add_action( 'init',                   array( $this, 'add_author_woocommerce' ), PHP_INT_MAX );
				add_action( 'pre_get_posts',          array( $this, 'add_author_woocommerce_pre_get_posts' ) );
				add_action( 'generate_rewrite_rules', array( $this, 'custom_rewrite_rules' ) );
			} */

			// General - Buttons
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_button_text_single' ),  PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_product_add_to_cart_text',        array( $this, 'change_add_to_cart_button_text_archive' ), PHP_INT_MAX, 2 );

			// General - Messages
			add_action( 'woocommerce_single_product_summary', array( $this, 'is_purchasable_html' ), 15 );

			// General - Variable Add to Cart Template
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled', 'no' ) ) {
				add_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );
			}

			// Product Info - Tab
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_tab_enabled', 'yes' ) ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_crowdfunding_product_tab' ), 98 );
			}

			// Product Info - Single Page
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_info_enabled' ) ) {
				add_action(
					get_option( 'alg_woocommerce_crowdfunding_product_info_filter', 'woocommerce_before_single_product_summary' ),
					array( $this, 'add_crowdfunding_product_info' ),
					get_option( 'alg_woocommerce_crowdfunding_product_info_filter_priority', 10 )
				);
			}

			// Product Info - Archives
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_info_archives_enabled' ) ) {
				add_action(
					get_option( 'alg_woocommerce_crowdfunding_product_info_archives_filter', 'woocommerce_after_shop_loop_item' ),
					array( $this, 'add_crowdfunding_product_info_archives' ),
					get_option( 'alg_woocommerce_crowdfunding_product_info_arch_filter_priority', 10 )
				);
			}

			// Product Info -  Price - Hide Variable Price
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_hide_variable_price', 'no' ) ) {
				add_action( 'woocommerce_get_price_html', array( $this, 'hide_variable_price' ), PHP_INT_MAX, 2 );
			}

			// is_purchasable
			add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), PHP_INT_MAX, 2 );

			// Open Pricing
			require_once( 'class-wc-crowdfunding-open-pricing.php' );
		}
	}

	/**
	 * custom_rewrite_rules.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	/* function custom_rewrite_rules( $rewrite ) {
		$rewrite->rules = array( 'author-products/([^/]+)/?$' => 'index.php?author_name=$matches[1]&post_type=product' ) + $rewrite->rules;
	} */


	/**
	 * add_author_woocommerce_pre_get_posts.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	/* function add_author_woocommerce_pre_get_posts( $query ) {
		if ( $query->is_author() && $query->is_main_query() ) {
			$query->set( 'post_type', 'product' );
//			set_query_var( 'post_type', 'product' );
		}
	} */

	/**
	 * add_author_woocommerce.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	/* function add_author_woocommerce() {
		add_post_type_support( 'product', 'author' );
	} */

	/**
	 * hide_variable_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function hide_variable_price( $price, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) return $price;
		if ( ! $_product->is_type( 'variable' ) ) return $price;
		return '';
	}

	/**
	 * change_variable_add_to_cart_template.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function change_variable_add_to_cart_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( ! $this->is_crowdfunding_product() ) return $located;
		if ( 'single-product/add-to-cart/variable.php' == $template_name ) {
			$located = untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/..' ) ) . '/includes/templates/alg-add-to-cart-variable.php';
		}
		return $located;
	}

	/**
	 * Returns false if already finished.
	 *
	 * @version 2.0.0
	 * @return bool
	 */
	function is_active( $_product ) {
		$end_date_str = get_post_meta( $_product->id, '_' . 'alg_crowdfunding_deadline', true );
		$end_time_str = get_post_meta( $_product->id, '_' . 'alg_crowdfunding_deadline_time', true );
		$end_datetime = ( '' != $end_date_str ) ? strtotime( trim( $end_date_str . ' ' . $end_time_str, ' ' ) ) : 0;
		if ( $end_datetime > 0 && ( $end_datetime - current_time( 'timestamp' ) ) < 0 ) return false;
		return true;
	}

	/**
	 * Returns false if not started yet.
	 *
	 * @version 2.0.0
	 * @return bool
	 */
	function is_started( $_product ) {
		$start_date_str = get_post_meta( $_product->id, '_' . 'alg_crowdfunding_startdate', true );
		$start_time_str = get_post_meta( $_product->id, '_' . 'alg_crowdfunding_starttime', true );
		$start_datetime = ( '' != $start_date_str ) ? strtotime( trim( $start_date_str . ' ' . $start_time_str, ' ' ) ) : 0;
		if ( $start_datetime > 0 && ( $start_datetime - current_time( 'timestamp' ) ) > 0 ) return false;
		return true;
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @version 2.0.0
	 * @return  bool
	 */
	public function is_purchasable( $purchasable, $_product ) {
		if ( $this->is_crowdfunding_product( $_product ) && ( ! $this->is_started( $_product ) || ! $this->is_active( $_product ) ) ) {
			$purchasable = false;
		}
		return $purchasable;
	}

	/**
	 * is_purchasable_html.
	 *
	 * @version 2.0.0
	 * @return  bool
	 */
	public function is_purchasable_html() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) && ! $this->is_purchasable ( true, $_product ) ) {
			if ( ! $this->is_started( $_product ) ) echo get_option( 'alg_woocommerce_crowdfunding_message_not_started' );
			if ( ! $this->is_active( $_product ) )  echo get_option( 'alg_woocommerce_crowdfunding_message_ended' );
		}
	}

	/**
	 * is_crowdfunding_product.
	 *
	 * @version 2.1.0
	 * @since   2.0.0
	 */
	function is_crowdfunding_product( $_product = '' ) {
		if ( '' == $_product ) $_product = wc_get_product();
		if ( '' == $_product ) return '';
//		return ( get_post_meta( $_product->id, '_' . 'alg_crowdfunding_goal_sum', true ) > 0 ) ? true : false;
		return ( 'yes' === get_post_meta( $_product->id, '_' . 'alg_crowdfunding_enabled', true ) ) ? true : false;
	}

	/**
	 * change_add_to_cart_button_text_single.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function change_add_to_cart_button_text_single( $add_to_cart_text, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) return $add_to_cart_text;
		$the_text = get_post_meta( $_product->id, '_alg_crowdfunding_button_label_single', true );
		$the_text = ( '' != $the_text ) ? $the_text : get_option( 'alg_woocommerce_crowdfunding_button_single' );
		return $the_text;
	}

	/**
	 * change_add_to_cart_button_text_archive.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function change_add_to_cart_button_text_archive( $add_to_cart_text, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) return $add_to_cart_text;
		$the_text = get_post_meta( $_product->id, '_alg_crowdfunding_button_label_loop', true );
		$the_text = ( '' != $the_text ) ? $the_text : get_option( 'alg_woocommerce_crowdfunding_button_archives' );
		return $the_text;
	}

	/**
	 * add_crowdfunding_product_tab.
	 */
	function add_crowdfunding_product_tab( $tabs ) {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$tabs['crowdfunding'] = array(
				'title'    => get_option( 'alg_woocommerce_crowdfunding_product_tab_title' ),
				'priority' => get_option( 'alg_woocommerce_crowdfunding_product_tab_priority' ),
				'callback' => array( $this, 'crowdfunding_product_tab_callback' ),
			);
		}
		return $tabs;
	}

	/**
	 * crowdfunding_product_tab_callback.
	 */
	function crowdfunding_product_tab_callback() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_tab' );
			echo do_shortcode( $info_template );
		}
	}

	/**
	 * add_crowdfunding_product_info_archives.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function add_crowdfunding_product_info_archives() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_info_archives' );
//			$info_template = str_replace( PHP_EOL, '<br>', $info_template );
			echo do_shortcode( $info_template );
		}
	}

	/**
	 * add_crowdfunding_product_info.
	 */
	function add_crowdfunding_product_info() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_info' );
//			$info_template = str_replace( PHP_EOL, '<br>', $info_template );
			echo do_shortcode( $info_template );
		}
	}

}

endif;

if ( ! function_exists( 'alg_variation_radio_button' ) ) {
	/**
	 * alg_variation_radio_button.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function alg_variation_radio_button( $_product, $variation ) {
		$attributes_html = '';
		$variation_attributes_display_values = array();
		$is_checked = true;
		foreach ( $variation['attributes'] as $attribute_full_name => $attribute_value ) {

			$attributes_html .= ' ' . $attribute_full_name . '="' . $attribute_value . '"';

			$attribute_name = $attribute_full_name;
			$prefix = 'attribute_';
			if ( substr( $attribute_full_name, 0, strlen( $prefix ) ) === $prefix ) {
				$attribute_name = substr( $attribute_full_name, strlen( $prefix ) );
			}

			$checked = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $_product->get_variation_default_attribute( $attribute_name );
			if ( $checked != $attribute_value ) $is_checked = false;

			$terms = get_terms( $attribute_name );
			foreach ( $terms as $term ) {
				if ( is_object( $term ) && isset( $term->slug ) && $term->slug === $attribute_value && isset( $term->name ) ) {
					$attribute_value = $term->name;
				}
			}

			$variation_attributes_display_values[] = $attribute_value;

		}
		$variation_title = implode( ', ', $variation_attributes_display_values ) . ' (' . wc_price( $variation['display_price'] ) . ')';
		$variation_id    = $variation['variation_id'];
		$is_checked = checked( $is_checked, true, false );

		echo '<p>';
		echo '<input name="alg_variations" type="radio"' . $attributes_html . ' variation_id="' . $variation_id . '"' . $is_checked . '>' . ' ' . $variation_title;
		echo '<br>';
//		echo '<small>' . $variation['variation_description'] . '</small>';
		echo '<small>' . get_post_meta( $variation_id, '_variation_description', true )  . '</small>';
		echo '</p>';
	}
}

return new Alg_WC_Crowdfunding();
