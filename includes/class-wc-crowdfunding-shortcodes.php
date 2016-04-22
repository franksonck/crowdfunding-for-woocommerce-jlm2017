<?php
/**
 * Crowdfunding for WooCommerce - Shortcodes
 *
 * @version 2.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes' ) ) :

class Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 */
	function __construct() {
		add_shortcode( 'product_total_orders',                         array( $this, 'alg_product_total_orders' ) );
		add_shortcode( 'product_total_orders_sum',                     array( $this, 'alg_product_total_orders_sum' ) );
		add_shortcode( 'product_crowdfunding_goal',                    array( $this, 'alg_product_crowdfunding_goal' ) );
		add_shortcode( 'product_crowdfunding_goal_remaining',          array( $this, 'alg_product_crowdfunding_goal_remaining' ) );
		add_shortcode( 'product_crowdfunding_startdate',               array( $this, 'alg_product_crowdfunding_startdate' ) );
		add_shortcode( 'product_crowdfunding_starttime',               array( $this, 'alg_product_crowdfunding_starttime' ) );
		add_shortcode( 'product_crowdfunding_startdatetime',           array( $this, 'alg_product_crowdfunding_startdatetime' ) );
		add_shortcode( 'product_crowdfunding_deadline',                array( $this, 'alg_product_crowdfunding_deadline' ) );
		add_shortcode( 'product_crowdfunding_deadline_time',           array( $this, 'alg_product_crowdfunding_deadline_time' ) );
		add_shortcode( 'product_crowdfunding_deadline_datetime',       array( $this, 'alg_product_crowdfunding_deadline_datetime' ) );
		add_shortcode( 'product_crowdfunding_time_remaining',          array( $this, 'alg_product_crowdfunding_time_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_progress_bar',       array( $this, 'alg_product_crowdfunding_goal_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_time_progress_bar',       array( $this, 'alg_product_crowdfunding_time_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_add_to_cart_form',        array( $this, 'alg_product_crowdfunding_add_to_cart_form' ) );
	}

	/**
	 * alg_product_crowdfunding_add_to_cart_form.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 */
	function alg_product_crowdfunding_add_to_cart_form( $atts ) {
//		remove_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX );
		$the_product = isset( $atts['product_id'] ) ? wc_get_product( $atts['product_id'] ) : wc_get_product();
		$return = ( $the_product->is_type( 'variable' ) ) ? woocommerce_variable_add_to_cart() : woocommerce_simple_add_to_cart();
//		add_filter(    'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );
		return $return;
	}

	/**
	 * alg_product_crowdfunding_time_progress_bar.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 */
	function alg_product_crowdfunding_time_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$seconds_remaining =
			strtotime( trim(
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) .
				' ' .
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ) ) -
			current_time( 'timestamp' );
		$seconds_total =
			strtotime( trim(
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) .
				' ' .
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ) ) -
			strtotime( trim(
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) .
				' ' .
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' ) );

		$current_value = $seconds_remaining;
		$max_value     = $seconds_total;
		$return = '<progress value="' . $current_value . '" max="' . $max_value . '"></progress>';
		return $this->output_shortcode( $return, $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_progress_bar.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 */
	function alg_product_crowdfunding_goal_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$current_value = $this->get_product_orders_data( 'orders_sum', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		if ( ! $max_value ) return '';
		$return = '<progress value="' . $current_value . '" max="' . $max_value . '"></progress>';
		return $this->output_shortcode( $return, $atts );
//		return '<div class="meter"><span style="width: 33.3%"></span></div>';
	}

	/**
	 * output_shortcode.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function output_shortcode( $value, $atts ) {
		if ( '' != $value ) {
			if ( ! isset( $atts['before'] ) ) $atts['before'] = '';
			if ( ! isset( $atts['after'] ) )  $atts['after'] = '';
			$value = ( isset( $atts['type'] ) && 'price' === $atts['type'] ) ? wc_price( $value ) : $value;
			return $atts['before'] . $value . $atts['after'];
		}
		return '';
	}

	/**
	 * get_product_orders_data.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function get_product_orders_data( $return_value = 'total_orders', $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';

		$the_product = wc_get_product( $product_id );

		global $woocommerce_loop;
		$saved_wc_loop = $woocommerce_loop;
		//global $loop, $woocommerce_loop, $product, $post, $wp_query, $woocommerce;
		/* $saved_loop = $loop;
		$saved_product = $product;
		$saved_post = $post;
		$saved_wp_query = $wp_query;
		$saved_woocommerce = $woocommerce; */

		$total_orders = 0;
		$total_sum = 0;
		$order_statuses = ( isset( $atts['order_status'] ) ) ? explode( ',', str_replace( ' ', '', $atts['order_status'] ) ) : array( 'wc-completed' );
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => $order_statuses,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'date_query'     => array(
				array(
					'after'     => trim(
						get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) .
						' ' .
						get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' ),
					'inclusive' => true,
				),
			),
		);
		$product_ids = array();
		if ( $the_product->is_type( 'grouped' ) ) {
			$product_ids = $the_product->get_children();
		} else {
			$product_ids = array( $product_id );
		}
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				$order_id = $loop->post->ID;
				$the_order = wc_get_order( $order_id );
				$the_items = $the_order->get_items();
				foreach( $the_items as $item ) {
					if ( in_array( $item['product_id'], $product_ids ) ) {
						$total_sum += $item['line_total'] + $item['line_tax'];
						$total_orders++;
					}
				}
			endwhile;
			woocommerce_reset_loop();
			wp_reset_postdata();
			//wp_reset_query();

		}

		$woocommerce_loop = $saved_wc_loop;

		global $product;
		$product = wc_get_product();

		/* $loop = $saved_loop;
		$product = $saved_product;
		$post = $saved_post;
		$wp_query = $saved_wp_query;
		$woocommerce = $saved_woocommerce; */

		return ( 'orders_sum' === $return_value ) ? $total_sum : $total_orders;
	}

	/**
	 * alg_product_total_orders.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_total_orders( $atts ) {
		return $this->output_shortcode( $this->get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders_sum.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_total_orders_sum( $atts ) {
		$atts['type'] = 'price';
		return $this->output_shortcode( $this->get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline_datetime.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_datetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( trim(
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) .
			' ' .
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline_time.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_time( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdatetime.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_startdatetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( trim(
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) .
			' ' .
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' ), $atts );
	}

	/**
	 * alg_product_crowdfunding_starttime.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_starttime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdate.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_startdate( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_deadline( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_time_remaining.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_time_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$seconds_remaining =
			strtotime( trim(
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) .
				' ' .
				get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ) ) -
			current_time( 'timestamp' );
		$days_remaining    = floor( $seconds_remaining / ( 24 * 60 * 60 ) );
		$hours_remaining   = floor( $seconds_remaining / (      60 * 60 ) );
		$minutes_remaining = floor( $seconds_remaining /             60   );
		if ( $seconds_remaining <= 0 ) return '';

		if ( ! isset( $atts['day'] ) )     $atts['day']     = __( ' day left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['days'] ) )    $atts['days']    = __( ' days left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['hour'] ) )    $atts['hour']    = __( ' hour left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['hours'] ) )   $atts['hours']   = __( ' hours left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['minute'] ) )  $atts['minute']  = __( ' minute left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['minutes'] ) ) $atts['minutes'] = __( ' minutes left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['second'] ) )  $atts['second']  = __( ' second left', 'alg-woocommerce-crowdfunding' );
		if ( ! isset( $atts['seconds'] ) ) $atts['seconds'] = __( ' seconds left', 'alg-woocommerce-crowdfunding' );

		     if ( $days_remaining    >  0 ) $return = ( 1 == $days_remaining    ) ? $days_remaining    . $atts['day']    : $days_remaining    . $atts['days'];
		else if ( $hours_remaining   >  0 ) $return = ( 1 == $hours_remaining   ) ? $hours_remaining   . $atts['hour']   : $hours_remaining   . $atts['hours'];
		else if ( $minutes_remaining >  0 ) $return = ( 1 == $minutes_remaining ) ? $minutes_remaining . $atts['minute'] : $minutes_remaining . $atts['minutes'];
		else                                $return = ( 1 == $seconds_remaining ) ? $seconds_remaining . $atts['second'] : $seconds_remaining . $atts['seconds'];

		return $this->output_shortcode( $return, $atts );
	}

	/**
	 * alg_product_crowdfunding_goal.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal_remaining( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) return '';
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ) - $this->get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes();