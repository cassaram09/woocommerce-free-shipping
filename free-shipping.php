<?php
 
/**
 * Plugin Name: WooCommerce Individual Product Free Shipping
 * Plugin URI: github.com/cassaram09/woocommerce-free-shipping
 * Description: Free Shipping for Individual Products for WooCommerce
 * Version: 1.0.0
 * Author: Matt Cassara
 * Author URI: http://mattcassara.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: free-shipping
 * Domain Path: /languages
 */

if ( ! defined( 'WPINC' ) ) {
  die;
}
 
/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 
    function woocommerce_individual_product_free_shipping_method() {
        if ( ! class_exists( 'WooCommerce_Individual_Product_Free_Shipping_Method' ) ) {
            class WooCommerce_Individual_Product_Free_Shipping_Method extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id                 = 'free_shipping'; 
                    $this->method_title       = __( 'Free Shipping Options', 'free_shipping' );  
                    $this->method_description = __( 'Free Shipping for Individual Products for WooCommerce', 'free_shipping' ); 
 
                    $this->init();
 
                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'WooCommerce Individual Product Free Shipping', 'free_shipping' );
                }
 
                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    $this->init_form_fields(); 
                    $this->init_settings(); 
 
                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }
 
                /**
                 * Define settings field for this shipping
                 * @return void 
                 */
                function init_form_fields() { 
             
                    $this->form_fields = array(
             
                     'enabled' => array(
                          'title' => __( 'Enable', 'free_shipping' ),
                          'type' => 'checkbox',
                          'description' => __( 'Enable this shipping.', 'free_shipping' ),
                          'default' => 'yes'
                          ),
             
                     'title' => array(
                        'title' => __( 'Title', 'free_shipping' ),
                          'type' => 'text',
                          'description' => __( 'Title to be display on site', 'free_shipping' ),
                          'default' => __( 'Free Shipping', 'free_shipping' )
                          ),
             
                     );

             
                }
 
                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package ) {
               
                  $rate = array(
                      'id' => $this->id,
                      'label' => $this->title,
                      'cost' => 0
                  );
               
                  $this->add_rate( $rate );
                  
                }

            }
        }
    }
 
    add_action( 'woocommerce_shipping_init', 'woocommerce_individual_product_free_shipping_method' );
 
    function add_woocommerce_individual_product_free_shipping_method( $methods ) {
        $methods['free_shipping'] = 'WooCommerce_Individual_Product_Free_Shipping_Method';
        return $methods;
    }
 
    add_filter( 'woocommerce_shipping_methods', 'add_woocommerce_individual_product_free_shipping_method' );


}

add_filter( 'woocommerce_cart_shipping_packages', 'sort_free_and_regular_products' );

function sort_free_and_regular_products( $packages ) {
    // Reset the packages
    $packages = array();
  
    // free items
    $free_items   = array();
    $regular_items = array();
    
    foreach ( WC()->cart->get_cart() as $item ) {
        if ( $item['data']->needs_shipping() ) {
            if ( $item['data']->get_shipping_class() == 'free' ) {
                $free_items[] = $item;
            } else {
                $regular_items[] = $item;
            }
        }
    }
    
    if ( $free_items ) {
        $packages[] = array(
            'ship_via'        => array( 'free_shipping' ),
            'contents'        => $free_items,
            'contents_cost'   => array_sum( wp_list_pluck( $free_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }
    if ( $regular_items ) {
        $packages[] = array(
            'ship_via'        =>  array( 'wf_shipping_ups' ),
            'contents'        => $regular_items,
            'contents_cost'   => array_sum( wp_list_pluck( $regular_items, 'line_total' ) ),
            'applied_coupons' => WC()->cart->applied_coupons,
            'destination'     => array(
                'country'   => WC()->customer->get_shipping_country(),
                'state'     => WC()->customer->get_shipping_state(),
                'postcode'  => WC()->customer->get_shipping_postcode(),
                'city'      => WC()->customer->get_shipping_city(),
                'address'   => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2()
            )
        );
    }  

  return $packages;

}


?>