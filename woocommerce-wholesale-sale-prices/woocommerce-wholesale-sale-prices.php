<?php
/*
Plugin Name: Woocommerce Wholesale Sale Prices
Description: Add Wholesale sale price functionality to Woocommerce
Version: 1.00
Author: Dentons
Author URI: https://dentonsdigital.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*Enqueue Scripts*/

// enqueue wholesale templates
if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/woocommerce-wholesale-sale_enqueue_templates.php') ) {
  require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/woocommerce-wholesale-sale_enqueue_templates.php');
}
// enqueue field drawing script
if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_draw_fields.php') ) {
  require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_draw_fields.php');
}
// variation price script
if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_variation_price.php') ) 
{
  require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_variation_price.php');
}

 add_action( 'admin_enqueue_scripts', 'EnqueueAdminScripts' );

function EnqueueAdminScripts(){
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-1.12.4.js');
    wp_enqueue_script('jquery-ui-core', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js');  
    if (get_current_screen()->id == "product"){
    wp_enqueue_script('run-datepicker',  plugin_dir_url( __FILE__ ) . "/js/datepicker.js"); 
    }    
    wp_enqueue_style('admin-css',  plugin_dir_url( __FILE__ ) . "/css/admin.css");
}

add_action('wp_enqueue_scripts', 'EnqueueScripts');

function EnqueueScripts(){

        wp_register_script('remove-sale',  plugin_dir_url( __FILE__ ) . "/js/remove_sale.js");        
        wp_register_script('remove-sale-wholesale',  plugin_dir_url( __FILE__ ) . "/js/remove_sale_wholesale.js");        
        wp_register_script('variation_prices',  plugin_dir_url( __FILE__ ) . "/js/add_variation_prices.js");
        wp_register_script('simple_sale',  plugin_dir_url( __FILE__ ) . "/js/add_single_salebanner.js");
        wp_register_script('remove-sale-loop',  plugin_dir_url( __FILE__ ) . "/js/remove_sale_loop.js");

}

// sale page query - shortcode

add_filter( 'woocommerce_shortcode_products_query', 'woocommerce_shortcode_products_orderby' );

function woocommerce_shortcode_products_orderby( $args ) {
    if (!is_front_page()){
        $current_user = getUserTypee(); 

        if ($current_user == "wholesale_customer"){
            $args['meta_key'] = '_wholesale_sale_price';
            $args['post_type'] = 'product';
        }else{
            $args['post_type'] = 'product';
            $args['meta_key'] = '_sale_price';
            $args['meta_value'] = 0;
            $args['meta_compare'] = '>';
        }

        return $args;
    }else{
        return $args;
    }
    
}

// sale products query - loop

function removeSaleOnLoop() {
    global $product;
    if ( is_woocommerce()) {
        $on_sale = [];
            while ( have_posts() ) {
                the_post();
                $id = get_the_ID();
                $on_sale[] = isProductWholeOnSale($id);
            }
    }else if(basename(get_permalink()) == "summer-sale" ){
                $on_sale = ['sale'];
    }
        
    wp_localize_script( 'remove-sale-loop', 'sale_script_loop',
        array('remove_sale' => $on_sale));
    wp_enqueue_script('remove-sale-loop');
}
add_action( 'wp_footer', 'removeSaleOnLoop' );

function isProductWholeOnSale($id){
    $product = new WC_Product_Variable( $id );
    $variations = $product->get_available_variations();
        if (getUserTypee() == 'wholesale_customer'){
            // for wholesale
            if (count($variations) == 0){
               $wholesale_sale = get_post_meta($id, '_wholesale_sale_price', true);
                if ($wholesale_sale != ""){
                    return $id;
                }
            }else{
                $count = 0;
                for($i=0;$i<count($variations);$i++){
                     $wholesale_sale = get_post_meta($variations[$i]['variation_id'], '_wholesale_sale_price', true);
                    if ($wholesale_sale == ""){
                        $count += 1;
                    }
                }
                if ($count == 0){
                    return $id;
                }

            }
        }else{
            // for everyone else
            $_product = wc_get_product( $id );
            if($_product->is_on_sale()){
                return $id;
            } 
        }
    
}



function getUserTypee(){
        $user = wp_get_current_user();
        // return retail user if no role
        if (count($user->roles) == 0){
            return "retail";
        }else{
            foreach($user->roles as $role){
                // return admin user if admin role detected at any point
                if ($role == "administrator"){
                    return "admin";
                }else if($role == "wholesale_customer"){
                    return "wholesale_customer";
                }else{
                    return "retail";
                }
            }
        }
    }

/*Change cart totals*/

add_action( 'woocommerce_before_calculate_totals', 'add_custom_price' );

function add_custom_price( $cart_object ) {
    // variation price script
    if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_price_class.php') ) 
    {
      require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_price_class.php');
    }
    
    $wholesale = new wholesalePrice();
    
    $test = $wholesale->getSalePriceForCart($cart_object);
    
    return;     
}

 //    echo "<script>console.log('test');</script>";
 //echo "<script>console.log(".json_encode($variations).");</script>"; ?>