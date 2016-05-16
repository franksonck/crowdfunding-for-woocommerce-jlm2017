<?php
/*
Plugin Name: Crowdfunding for WooCommerce
Plugin URI: https://github.com/jlm2017/crowdfunding-for-woocommerce-jlm2017
Description: Crowdfunding Products for WooCommerce. (with adaptation from JLM 2017)
Version: 2.2.1-jlm2017
Author: Algoritmika Ltd
Author URI: http://www.algoritmika.com
Text Domain: alg-woocommerce-crowdfunding
Domain Path: /langs
Copyright: � 2016 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

// Check if Pro is active, if so then return
if ( in_array( 'crowdfunding-for-woocommerce-pro/crowdfunding-for-woocommerce-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

if ( ! class_exists( 'Alg_Woocommerce_Crowdfunding' ) ) :

/**
 * Main Alg_Woocommerce_Crowdfunding Class
 *
 * @class   Alg_Woocommerce_Crowdfunding
 * @version 2.2.0
 */

final class Alg_Woocommerce_Crowdfunding {

	/**
	 * @var Alg_Woocommerce_Crowdfunding The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Woocommerce_Crowdfunding Instance
	 *
	 * Ensures only one instance of Alg_Woocommerce_Crowdfunding is loaded or can be loaded.
	 *
	 * @static
	 * @return Alg_Woocommerce_Crowdfunding - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Alg_Woocommerce_Crowdfunding Constructor.
	 *
	 * @version 2.0.0
	 * @access public
	 */
	public function __construct() {

		add_filter( 'alg_crowdfunding_option', array( $this, 'crowdfunding_option' ) );

		// Include required files
		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );

		// Settings & Scripts
		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_init',            array( $this, 'register_admin_scripts' ) );
		}

		// Frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init',               array( $this, 'register_scripts' ) );
	}

	/**
	 * crowdfunding_option.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	public function crowdfunding_option( $option ) {
		return $option;
	}

	/**
	 * register_scripts.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 */
	public function register_scripts() {
		/* wp_register_script(
			'alg-progressbar',
			$this->plugin_url() . '/includes/js/alg-progressbar.js',
			array( 'jquery' ),
			false,
			true
		); */
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 */
	public function enqueue_scripts() {
//		wp_enqueue_style( 'alg-progressbar-css', $this->plugin_url() . '/includes/css/alg-progressbar.css' );
//		wp_enqueue_script( 'alg-progressbar' );
		wp_enqueue_script( 'alg-variations', $this->plugin_url() . '/includes/js/alg-variations-frontend.js', array( 'jquery' ) );
	}

	/**
	 * register_admin_scripts.
	 *
	 * @version 1.2.0
	 * @since   1.1.0
	 */
	public function register_admin_scripts() {
		wp_register_script(
			'jquery-ui-timepicker',
			$this->plugin_url() . '/includes/js/jquery.timepicker.min.js',
			array( 'jquery' ),
			false,
			true
		);
	}

	/**
	 * enqueue_admin_scripts.
	 *
	 * @version 1.2.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'alg-datepicker', $this->plugin_url() . '/includes/js/alg-datepicker.js' );
//		wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'alg-timepicker', $this->plugin_url() . '/includes/css/jquery.timepicker.min.css' );
		wp_enqueue_script( 'jquery-ui-dialog' );
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @version 2.2.0
	 * @param   mixed $links
	 * @return  array
	 */
	public function action_links( $links ) {
		$settings_link   = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_crowdfunding' )         . '">' . __( 'Settings', 'woocommerce' )   . '</a>';
		$unlock_all_link = '<a href="' . esc_url( 'http://coder.fm/item/crowdfunding-for-woocommerce-plugin/' ) . '">' . __( 'Unlock all', 'woocommerce' ) . '</a>';
		$custom_links    = ( PHP_INT_MAX === apply_filters( 'alg_crowdfunding_option', 1 ) ) ? array( $settings_link ) : array( $settings_link, $unlock_all_link );
		return array_merge( $custom_links, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 2.2.0
	 */
	private function includes() {

		require_once( 'includes/admin/class-wc-crowdfunding-admin.php' );

		$settings = array();
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-general.php' );
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-product-info.php' );
		$settings[] = require_once( 'includes/admin/class-wc-crowdfunding-settings-open-pricing.php' );
		if ( is_admin() ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						if ( isset ( $_GET['alg_woocommerce_crowdfunding_admin_options_reset'] ) ) {
							require_once( ABSPATH . 'wp-includes/pluggable.php' );
							if ( is_super_admin() ) {
								delete_option( $value['id'] );
							}
						}
						$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}

		require_once( 'includes/class-wc-crowdfunding.php' );
		require_once( 'includes/class-wc-crowdfunding-shortcodes.php' );
	}

	/**
	 * Add Woocommerce settings tab to WooCommerce settings.
	 */
	public function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/class-wc-settings-crowdfunding.php' );
		return $settings;
	}

	/**
	 * Init Alg_Woocommerce_Crowdfunding when WordPress initialises.
	 *
	 * @version 2.0.0
	 */
	public function init() {
		// Set up localisation
		load_plugin_textdomain( 'alg-woocommerce-crowdfunding', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

endif;

/**
 * Returns the main instance of Alg_Woocommerce_Crowdfunding to prevent the need to use globals.
 *
 * @return Alg_Woocommerce_Crowdfunding
 */
if ( ! function_exists( 'alg_wc_crowdfunding' ) ) {
	function alg_wc_crowdfunding() {
		return Alg_Woocommerce_Crowdfunding::instance();
	}
}

alg_wc_crowdfunding();
