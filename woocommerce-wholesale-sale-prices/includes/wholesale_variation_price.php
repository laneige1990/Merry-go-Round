<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function gl_custom_variable_price(){
    if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_price_class.php') ) 
{
  require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-sale-prices/includes/wholesale_sale_price_class.php');
}
    
    global $product;
    $wholesale = new wholesalePrice();
    
    if ($product->is_type( 'variable' )){
        wp_localize_script( 'variation_prices', 'variation_script',
            array('prices' => $wholesale->getWholesaleVariablePricehtml($product)));
        wp_enqueue_script('variation_prices');
    }else{
           wp_localize_script( 'simple_sale', 'simple_sale_script',
            array('onsale' => $wholesale->simpleOnSale($product)));
        wp_enqueue_script('simple_sale');
    }
}

add_action('woocommerce_before_add_to_cart_button','gl_custom_variable_price', 10, 2 );
?>