<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Woocommerce_Onsale_Badge
 * @subpackage Tmsm_Woocommerce_Onsale_Badge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tmsm_Woocommerce_Onsale_Badge
 * @subpackage Tmsm_Woocommerce_Onsale_Badge/public
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Woocommerce_Onsale_Badge_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Onsale_Badge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Onsale_Badge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-onsale-badge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tmsm_Woocommerce_Onsale_Badge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tmsm_Woocommerce_Onsale_Badge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-onsale-badge-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Check if discounts have been applied
	 */
	public function checkdiscounts(){


		error_log('checkdiscounts()');
		error_log('date: '.date('Y-m-d'));

		$tmsm_woocommerce_onsale_badge_lastcheck = get_option('tmsm_woocommerce_onsale_badge_lastcheck', false);

		// Check if the last checked value was created
		if($tmsm_woocommerce_onsale_badge_lastcheck == false){
			error_log('check not initiated yet');
			return;
		}

		// Check if last check was already done today
		if($tmsm_woocommerce_onsale_badge_lastcheck == date('Y-m-d')){
			error_log('check already done today');
			return;
		}

		// Update last check value
		update_option( 'tmsm_woocommerce_onsale_badge_lastcheck', date('Y-m-d'), false);
	}

}
