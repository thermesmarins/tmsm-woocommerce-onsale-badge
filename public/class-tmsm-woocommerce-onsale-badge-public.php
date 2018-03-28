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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-woocommerce-onsale-badge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-woocommerce-onsale-badge-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Check if discounts have been applied
	 */
	public function checkdiscounts(){

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Function checkdiscounts()');
			error_log('Date: '.date('Y-m-d'));
		}

		$tmsm_woocommerce_onsale_badge_lastcheck = get_option('tmsm_woocommerce_onsale_badge_lastcheck', false);
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Last check date: '.$tmsm_woocommerce_onsale_badge_lastcheck);
		}

		// Update last check value
		$result = update_option( 'tmsm_woocommerce_onsale_badge_lastcheck', date('Y-m-d'), true);
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Result saving new date: '.$result);
		}

		// Check if the last checked value was created
		if($tmsm_woocommerce_onsale_badge_lastcheck === false){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Check not initiated yet');
			}
		}

		// Check if last check was already done today
		if($tmsm_woocommerce_onsale_badge_lastcheck === date('Y-m-d')){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Check already done today');
			}
			return;
		}

		self::removesales();
		self::createsales();

	}

	/**
	 * Remove sales
	 */
	private function removesales(){

		// Get Products with sale special
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Products currenctly with a special sale:');
		}
		$args = array(
			'post_type' => 'product',
			'fields'          => 'ids', // Only get post IDs
			'posts_per_page'  => -1,
			'meta_query' => array(
				array(
					'key' => '_tmsm_woocommerce_onsale_badge',
					'value' => '', //The value of the field.
					'compare' => '!=', //Conditional statement used on the value.
				)
			)
		);
		$products_withspecialsale_ids = get_posts( $args );

		// Update transient "wc_products_onsale"
		if(is_array($products_withspecialsale_ids) && count($products_withspecialsale_ids) > 0){
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Products IDs to remove:');
				error_log(var_export($products_withspecialsale_ids, true));
			}
			$product_ids_on_sale = get_transient( 'wc_products_onsale' );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Products IDs in sale:');
				error_log(var_export($product_ids_on_sale, true));
			}
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Products IDs in sale after remove:');
				$product_ids_on_sale = array_diff($product_ids_on_sale, $products_withspecialsale_ids);
				error_log(var_export($product_ids_on_sale, true));
			}
			set_transient( 'wc_products_onsale', $product_ids_on_sale, DAY_IN_SECONDS * 30 );

		}

		// Delete post metas
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Deleting post metas');
		}
		delete_post_meta_by_key( '_tmsm_woocommerce_onsale_badge' );
		delete_post_meta_by_key( '_tmsm_woocommerce_onsale_alert' );

		// Clear cache
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}

	}

	/**
	 * Find all products
	 */
	private function createsales(){
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('Function createproductmeta()');
		}

		// Find all products
		$active_products = get_posts([
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		]);

		// Browse all products
		if(is_array($active_products)){
			foreach($active_products as $product){
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('('.$product->ID.') '.$product->post_title );
				}
				self::createproductmeta($product->ID);
			}
		}

		// Cache preloading
		if ( function_exists( 'run_rocket_sitemap_preload' ) ) {
			run_rocket_sitemap_preload();
		}
	}

	/**
	 * Create meta for badge and alert
	 *
	 * @param $product_id
	 */
	private function createproductmeta($product_id){

		$wcdpd_settings = get_option('rp_wcdpd_settings', false);

		// WooCommerce Dynamic Pricing and Discounts found
		if($wcdpd_settings !== false && !empty($product_id)) {

			$wcdpd_rules = @$wcdpd_settings[1]['product_pricing'];

			$wcpd_discount_active = false;

			if ( is_array( $wcdpd_rules ) ) {
				foreach ( $wcdpd_rules as $wcdpd_rule ) {
					$wcpd_discount_rule_active = true;

					$wcdpd_conditions = @$wcdpd_rule['conditions'];
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log('Rule: '.$wcdpd_rule['public_note'] );
					}
					if ( @$wcdpd_rule['exclusivity'] !== 'disabled' ) {


						print_r( $wcdpd_rule );
						if ( is_array( $wcdpd_conditions ) ) {
							foreach ( $wcdpd_conditions as $wcdpd_condition ) {
								//print_r($wcdpd_condition);

								// Condition Product in list
								if ( @$wcdpd_condition['type'] == 'product__product' && @$wcdpd_condition['method_option'] == 'in_list' ) {
									if ( is_array( @$wcdpd_condition['products'] )
									     && in_array( $product_id, @$wcdpd_condition['products'] ) ) {
										if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
											error_log("Product " . $product_id . " is in list");
										}
									} else {
										if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
											error_log("Product " . $product_id . " is NOT in list");
										}
										$wcpd_discount_rule_active = false;
									}
								}

								// Condition Date From
								if ( @$wcdpd_condition['type'] == 'time__date' && @$wcdpd_condition['method_option'] == 'from' ) {
									if ( ! empty( @$wcdpd_condition['date'] ) ) {
										if ( date( 'Y-m-d' ) >= $wcdpd_condition['date'] ) {
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log("Today after begin");
											}
										} else {
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log("Today before begin");
											}
											$wcpd_discount_rule_active = false;
										}
									}
								}

								// Condition Date To
								if ( @$wcdpd_condition['type'] == 'time__date' && @$wcdpd_condition['method_option'] == 'to' ) {

									if ( ! empty( @$wcdpd_condition['date'] ) ) {
										if ( date( 'Y-m-d' ) < $wcdpd_condition['date'] ) {
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log("Today before end");
											}
										} else {
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log("Today after end");
											}
											$wcpd_discount_rule_active = false;
										}
									}

								}

							}

						}

						//  Discount type
						if ( @$wcdpd_rule['method'] == 'bogo_xx_repeat' ) {
							if ( @$wcdpd_rule['quantities_based_on'] == 'individual__product'
							     && @$wcdpd_rule['bogo_pricing_value'] == '100'
							     && @$wcdpd_rule['bogo_pricing_method'] == 'discount__percentage'
							     && @$wcdpd_rule['bogo_purchase_quantity'] == 1
							     && @$wcdpd_rule['bogo_receive_quantity'] == 1 ) {

								$wcdpd_rule['badge'] = __( '2 for 1', 'tmsm-woocommerce-onsale-badge' );
								$wcdpd_rule['alert'] = $wcdpd_rule['public_note'].'<br>'.__( 'Simply put 2 products in your cart, and the discount will apply automatically.', 'tmsm-woocommerce-onsale-badge' );
							}
						}

					} else {
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log("Rule inactive");
						}
						$wcpd_discount_rule_active = false;
					}
					if ( $wcpd_discount_rule_active == true ) {
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log("Discount rule active for this product");
						}

						$wcpd_discount_active = true;
						break;
					}

				}
			}


			if ( $wcpd_discount_active == true ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log("Discount active for this product");
				}
				update_post_meta( $product_id, '_tmsm_woocommerce_onsale_badge', @$wcdpd_rule['badge'] );
				update_post_meta( $product_id, '_tmsm_woocommerce_onsale_alert', @$wcdpd_rule['alert'] );

				// Update transient "wc_products_onsale"
				$product_ids_on_sale = get_transient( 'wc_products_onsale' );
				if(!in_array($product_id, $product_ids_on_sale)){
					$product_ids_on_sale[] = $product_id;
					set_transient( 'wc_products_onsale', $product_ids_on_sale, DAY_IN_SECONDS * 30 );
			    }

			}

		}
	}

	/**
	 * Product is on sale?
	 *
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
	private function specialrule_is_on_sale($product){
		$badge = $product->get_meta('_tmsm_woocommerce_onsale_badge', true);
		return !empty($badge);
	}

	/**
	 * Product sale badge message
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	private function get_badge($product){
		$badge = $product->get_meta('_tmsm_woocommerce_onsale_badge', true);
		return $badge;
	}

	/**
	 * Product sale alert message
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	private function get_alert($product){
		$alert = $product->get_meta('_tmsm_woocommerce_onsale_alert', true);
		return $alert;
	}

	/**
	 * Display badge if is on sale with rule
	 *
	 * @param bool $on_sale
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
	public function display_badge($on_sale, $product){

		$specialrule_is_on_sale = self::specialrule_is_on_sale($product);
		if($specialrule_is_on_sale === true){
			$on_sale = true;
		}
		return $on_sale;

	}

	/**
	 * Adds extra post classes for products.
	 *
	 * @param array        $classes Current classes.
	 * @param string|array $class Additional class.
	 * @param int          $post_id Post ID.
	 * @return array
	 */
	public function product_post_class( $classes, $class = '', $post_id = 0){

		$product = wc_get_product($post_id);
		if( $product instanceof WC_Product && self::specialrule_is_on_sale($product) === true){
			$classes[] = 'special-sale';
		}

		return $classes;
	}

	/**
	 * Display alert if BOGO sale
	 */
	public function display_alert(){
		global $product;

		$specialrule_is_on_sale = self::specialrule_is_on_sale($product);

		if($specialrule_is_on_sale) {
			$alert = self::get_alert( $product );
			if ( ! empty( $alert ) ) {
				echo sprintf( '<p class="woocommerce-info woocommerce-specialsale">%s</p>', $alert );
			}
		}

	}


	/**
	 * Custom badge, change "On sale" badge with custom badge
	 *
	 * @param string $text
	 * @param WP_Post $post
	 * @param WC_Product $product
	 *
	 * @return string
	 */
	public function custom_badge($text, $post, $product){

		if(self::specialrule_is_on_sale($product)){
			$text = sprintf('<span class="onsale">%s</span>', self::get_badge($product));
		}
		return $text;
	}
}
