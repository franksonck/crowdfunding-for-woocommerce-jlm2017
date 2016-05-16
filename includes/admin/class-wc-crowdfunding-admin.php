<?php
/**
 * Crowdfunding for WooCommerce - Admin
 *
 * @version 2.2.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Admin' ) ) :

class Alg_WC_Crowdfunding_Admin {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 */
	public function __construct() {

		$this->id = 'crowdfunding_admin';

		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled' ) ) {
			add_action( 'add_meta_boxes',    array( $this, 'add_meta_box' ) );
			add_action( 'save_post_product', array( $this, 'save_meta_box' ), PHP_INT_MAX, 2 );
			add_action( 'admin_notices',     array( $this, 'admin_notices' ) );
			// Admin "Crowdfunding" column
			add_filter( 'manage_edit-product_columns', array( $this, 'add_product_column_is_crowdfunding' ), PHP_INT_MAX );
			add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_column_is_crowdfunding' ), PHP_INT_MAX );
		}
	}

	/**
	 * Add new "is crowdfunding" column to products list
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function add_product_column_is_crowdfunding( $columns ) {
		$columns['is_crowdfunding'] = __( 'Crowdfunding', 'alg-woocommerce-crowdfunding' );
		return $columns;
		/* $modified_columns = array();
		foreach ( $columns as $column_key => $column_label ) {
			$modified_columns[ $column_key ] = $column_label;
			if ( 'cb' == $column_key ) {
				$modified_columns['is_crowdfunding'] = __( 'Crowdfunding', 'alg-woocommerce-crowdfunding' );
			}
		}
		return $modified_columns; */
	}
	/**
	 * Output "is crowdfunding" column
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 */
	function render_product_column_is_crowdfunding( $column ) {
		if ( 'is_crowdfunding' != $column ) {
			return;
		}
		$is_crowdfunding =( 'yes' === get_post_meta( get_the_ID(), '_' . 'alg_crowdfunding_enabled', true ) ) ? '<span style="font-weight:bold;color:green;">&check;</span>' : '';
		echo $is_crowdfunding;
	}

	/**
	 * count_crowdfunding_products.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 */
	function count_crowdfunding_products( $post_id ) {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => 3,
			'meta_key'       => '_alg_crowdfunding_enabled',
			'meta_value'     => 'yes',
			'post__not_in'   => array( $post_id ),
		);
		$loop = new WP_Query( $args );
		return $loop->found_posts;
	}

	/**
	 * save_meta_box.
	 *
	 * @version 2.2.1
	 * @since   2.0.0
	 */
	function save_meta_box( $post_id, $post ) {
		// Check that we are saving with current metabox displayed.
		if ( ! isset( $_POST[ 'alg_' . $this->id . '_save_post' ] ) ) return;
		// Save options
		foreach ( $this->get_crowdfunding_options() as $option ) {
			if ( 'title' === $option['type'] ) {
				continue;
			}
			$option_value = isset( $_POST[ $option['name'] ] ) ? $_POST[ $option['name'] ] : '';
			if ( 'checkbox' === $option['type'] ) {
				$option_value = ( '' != $option_value ) ? 'yes' : 'no';
			}
			if ( $option['required'] && '' == $option_value ) {
//				wc_add_notice( $option['title'] . ' ' . __( 'is required', 'alg-woocommerce-crowdfunding' ) );
			} else {
				update_post_meta( $post_id, '_' . $option['name'], $option_value );
			}
		}
		// V1 convert done (message removal)
		if ( isset( $_POST['alg_crowdfunding_v1_convert_done'] ) ) {
			$variations_v1 = get_post_meta( $post_id, '_' . 'alg_crowdfunding_variations', true );
			delete_post_meta( $post_id, '_' . 'alg_crowdfunding_variations' );
			for ( $i = 1; $i <= $variations_v1; $i++ ) {
				delete_post_meta( $post_id, '_' . 'alg_crowdfunding_variations_title_' . $i );
				delete_post_meta( $post_id, '_' . 'alg_crowdfunding_variations_price_' . $i );
				delete_post_meta( $post_id, '_' . 'alg_crowdfunding_variations_desc_'  . $i );
			}
		}
	}

	/**
	 * add_notice_query_var.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_notice_query_var( $location ) {
		remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
		return add_query_arg( array( 'alg_admin_notice' => true ), $location );
	}

	/**
	 * admin_notices.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function admin_notices() {
		if ( ! isset( $_GET['alg_admin_notice'] ) ) {
			return;
		}
		?><div class="error"><p><?php echo '<div class="message">' . __( 'Free plugin\'s version is limited to 3 crowdfunding products enabled at the same time. Please visit <a href="http://coder.fm/item/crowdfunding-for-woocommerce-plugin/" target="_blank">plugin\'s page</a> for more information.', 'alg-woocommerce-crowdfunding' ) . '</div>'; ?></p></div><?php
	}

	/**
	 * add_meta_box.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function add_meta_box() {
		$screen   = ( isset( $this->meta_box_screen ) )   ? $this->meta_box_screen   : 'product';
		$context  = ( isset( $this->meta_box_context ) )  ? $this->meta_box_context  : 'normal';
		$priority = ( isset( $this->meta_box_priority ) ) ? $this->meta_box_priority : 'high';
		add_meta_box(
			'alg-' . $this->id,
			__( 'Crowdfunding', 'alg-woocommerce-crowdfunding' ),
			array( $this, 'create_meta_box' ),
			$screen,
			$context,
			$priority
		);
	}

	/**
	 * create_meta_box.
	 *
	 * @version 2.2.0
	 * @since   2.0.0
	 */
	function create_meta_box() {
		global $admin_notices;
		$current_post_id = get_the_ID();
		$html = '';
		$html .= $admin_notices;
		$html .= '<table class="widefat striped">';
		foreach ( $this->get_crowdfunding_options() as $option ) {
			if ( 'title' === $option['type'] ) {
				$html .= '<tr>';
				$html .= '<th colspan="2" style="font-weight:bold;">' . $option['title'] . '</th>';
				$html .= '</tr>';
			} else {
				$option_value = get_post_meta( $current_post_id, '_' . $option['name'], true );
				$input_ending = ' id="' . $option['name'] . '" name="' . $option['name'] . '" value="' . $option_value . '" placeholder="' . $option['placeholder'] . '">';
				if ( 'checkbox' === $option['type'] && 'yes' === $option_value ) $input_ending = ' checked="checked"' . $input_ending;
				$field_html = '';
				switch ( $option['type'] ) {
					case 'checkbox':
					case 'number':
					case 'text':
						$field_html = '<input class="short" type="' . $option['type'] . '"' . $input_ending;
						break;
					case 'price':
						$field_html = '<input class="short wc_input_price" type="number" step="0.0001"' . $input_ending;
						break;
					case 'date':
						$field_html = '<input class="input-text" display="date" type="text"' . $input_ending;
						break;
					case 'time':
						$field_html = '<input class="input-text" display="time" type="text"' . $input_ending;
						break;
					case 'textarea':
						$field_html = '<textarea style="min-width:300px;"' . ' id="' . $option['name'] . '" name="' . $option['name'] . '">' . $option_value . '</textarea>';
						break;
				}
				$html .= '<tr>';
				$html .= '<th style="width:25%;">' . $option['title'] . '</th>';
				$html .= '<td>' . $field_html . '</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		$html .= '<input type="hidden" name="alg_' . $this->id . '_save_post" value="alg_' . $this->id . '_save_post">';
		echo $html;

		$html_v1_convert = '';
		$variations_v1 = get_post_meta( $current_post_id, '_' . 'alg_crowdfunding_variations', true );
		if ( '' != $variations_v1 ) {
			$html_v1_convert .= '<div style="border:1px dashed red;padding:5px;">';
			$html_v1_convert .= '<h4 style="color:red;">' . __( 'Convert to Crowdfunding Product Version 2', 'alg-woocommerce-crowdfunding' ) . '</h4>';
			$html_v1_convert .= '<p>';
			$html_v1_convert .= __( '"Crowdfunding product" type is removed since "Crowdfunding for WooCommerce" plugin version 2.x.x. To continue using the plugin, you will need to maually change products type to variable and create product variations. You can always return back to <a href="https://downloads.wordpress.org/plugin/crowdfunding-for-woocommerce.1.2.0.zip">1.2.0 version</a>, however we do not recommed doing so, as you won\'t be able to get new updates. Please visit <a href="http://coder.fm/item/crowdfunding-for-woocommerce-plugin/" target="_blank">plugin\'s page</a> for more information.', 'alg-woocommerce-crowdfunding' );
			$html_v1_convert .= '</p>';

			$html_v1_convert .= '<p>';
			$html_v1_convert .= __( 'Old (version 1.x.x) variations (only for your reference)', 'alg-woocommerce-crowdfunding' ) . ':';
			$html_v1_convert .= '<table class="widefat">';
			$html_v1_convert .= '<tr>';
			$html_v1_convert .= '<th>' . __( 'Title',       'alg-woocommerce-crowdfunding' ) . '</th>';
			$html_v1_convert .= '<th>' . __( 'Price',       'alg-woocommerce-crowdfunding' ) . '</th>';
			$html_v1_convert .= '<th>' . __( 'Description', 'alg-woocommerce-crowdfunding' ) . '</th>';
			$html_v1_convert .= '</tr>';
			$html_v1_convert .= '</p>';

			for ( $i = 1; $i <= $variations_v1; $i++ ) {
				$html_v1_convert .= '<tr>';
				$html_v1_convert .= '<td>' . '<em>' . get_post_meta( $current_post_id, '_' . 'alg_crowdfunding_variations_title_' . $i, true ) . '</em>' . '</td>';
				$html_v1_convert .= '<td>' . wc_price( get_post_meta( $current_post_id, '_' . 'alg_crowdfunding_variations_price_' . $i, true ) ) . '</td>';
				$html_v1_convert .= '<td>' . '<small>' . get_post_meta( $current_post_id, '_' . 'alg_crowdfunding_variations_desc_' . $i, true ) . '</small>' . '</td>';
				$html_v1_convert .= '</tr>';
			}
			$html_v1_convert .= '</table>';
			$html_v1_convert .= '<p><input class="short" type="checkbox" id="alg_crowdfunding_v1_convert_done" name="alg_crowdfunding_v1_convert_done" value="" placeholder="">' . __( 'Remove this message (check and Save the product)', 'alg-woocommerce-crowdfunding' ) . '</a></p>';
			$html_v1_convert .= '</div>';
		}
		echo $html_v1_convert;
	}

	/**
	 * get_crowdfunding_options.
	 *
	 * @version 2.2.0
	 */
	function get_crowdfunding_options() {
		// NB: 'required' is not used at the moment...
		return array(
			array(
				'name'     => 'alg_crowdfunding_enabled',
				'placeholder' => '',
				'type'     => 'checkbox',
				'title'    => __( 'Enable', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'type'     => 'title',
				'title'    => __( 'Goals', 'alg-woocommerce-crowdfunding' ),
			),
			array(
				'name'     => 'alg_crowdfunding_goal_sum',
				'placeholder' => '',
				'type'     => 'price',
				'title'    => __( 'Goal', 'alg-woocommerce-crowdfunding' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_goal_backers',
				'placeholder' => '',
				'type'     => 'number',
				'title'    => __( 'Goal', 'alg-woocommerce-crowdfunding' ) . ' (' . __( 'Backers', 'alg-woocommerce-crowdfunding' ) . ')',
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_goal_items',
				'placeholder' => '',
				'type'     => 'number',
				'title'    => __( 'Goal', 'alg-woocommerce-crowdfunding' ) . ' (' . __( 'Items', 'alg-woocommerce-crowdfunding' ) . ')',
				'required' => false,
			),
			array(
				'type'     => 'title',
				'title'    => __( 'Time', 'alg-woocommerce-crowdfunding' ),
			),
			array(
				'name'     => 'alg_crowdfunding_startdate',
				'placeholder' => '',
				'type'     => 'date',
				'title'    => __( 'Start Date', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_starttime',
				'placeholder' => '00:00:00',
				'type'     => 'time',
				'title'    => __( 'Start Time', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_deadline',
				'placeholder' => '',
				'type'     => 'date',
				'title'    => __( 'End Date', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_deadline_time',
				'placeholder' => '00:00:00',
				'type'     => 'time',
				'title'    => __( 'End Time', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'type'     => 'title',
				'title'    => __( 'Labels', 'alg-woocommerce-crowdfunding' ),
			),
			array(
				'name'     => 'alg_crowdfunding_button_label_single',
				'placeholder' => get_option( 'alg_woocommerce_crowdfunding_button_single' ),
				'type'     => 'text',
				'title'    => __( 'Add to Cart Button Text (Single)', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_button_label_loop',
				'placeholder' => get_option( 'alg_woocommerce_crowdfunding_button_archives' ),
				'type'     => 'text',
				'title'    => __( 'Add to Cart Button Text (Archive/Category)', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'type'     => 'title',
				'title'    => __( 'Open Price (Name Your Price)', 'alg-woocommerce-crowdfunding' ),
			),
			array(
				'name'     => 'alg_crowdfunding_product_open_price_enabled',
				'placeholder' => '',
				'type'     => 'checkbox',
				'title'    => __( 'Enable Open Pricing', 'alg-woocommerce-crowdfunding' ),
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_product_open_price_default_price',
				'placeholder' => '',
				'type'     => 'price',
				'title'    => __( 'Default Price', 'alg-woocommerce-crowdfunding' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_product_open_price_min_price',
				'placeholder' => '',
				'type'     => 'price',
				'title'    => __( 'Min Price', 'alg-woocommerce-crowdfunding' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'required' => false,
			),
			array(
				'name'     => 'alg_crowdfunding_product_open_price_max_price',
				'placeholder' => '',
				'type'     => 'price',
				'title'    => __( 'Max Price', 'alg-woocommerce-crowdfunding' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'required' => false,
			),
		);
	}

}

endif;

return new Alg_WC_Crowdfunding_Admin();
