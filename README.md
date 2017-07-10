# WooCommerce Free Shipping

A simple plugin that allows you to set free shipping for individual products.

# Installation

Upload this plugin to your `/plugins` folder in Wordpress.

# Usage

In WooCommerce Settings, add a new Shipping Class with a slug of `/free`. Add this class to any products that are eligible for free shipping.

Plugin settings can be modified by going to `WooCommerce > Settings > Shipping > Free Shipping Options` in your Wordpress admin panel.

Current available options are `Enabling / Disabling` the plugin as well as adjusting the label in the cart.

If you'd like to use different options than the default of UPS BASIC shipping methods:

```
if ( $regular_items ) {
        $packages[] = array(
            'ship_via'        =>  array( 'wf_shipping_ups' ), // change wtf_shipping_ups to the shipping methods you would like to use
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
```

