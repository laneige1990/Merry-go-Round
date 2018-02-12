<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// enqueue wholesale price class
if( file_exists(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-prices/includes/class-wwp-wholesale-prices.php') ) {
  require_once(ABSPATH . 'wp-content/plugins/woocommerce-wholesale-prices/includes/class-wwp-wholesale-prices.php');
}
class wholesalePrice{
    
    function getWholesaleVariablePricehtml_bulk($variation){
        $user_role = $this->getUserType();
        $wholesale = new WWP_Wholesale_Prices();
        if ($user_role == "wholesale_customer"){
                $retail_price = number_format($variation['display_regular_price'], 2);
                $user_role = array($user_role);
                $wholesale_normalprice = $wholesale->get_product_wholesale_price_on_shop($variation['variation_id'],$user_role);
                $wholesale_normalprice = number_format($wholesale_normalprice, 2);
                $sale_wholesale_price = $this->getWholesaleSalePrice($variation['variation_id']);
                $sale_wholesale_price = number_format($sale_wholesale_price, 2);

                $variation_id = $variation['variation_id'];
                $variation_object['id'] = $variation_id;
                if ($wholesale_normalprice != ""){
                    //not on sale
                    if ($sale_wholesale_price == 0){
                            $price_string = '<p class="price">';
                            $price_string .= '<del class="original-computed-price">';
                            $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                            $price_string .= $retail_price;
                            $price_string .= '</span>';
                            $price_string .= '</del>';
                            $price_string .= "<ins style='display: block;'>";
                            $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'><span class='woocommerce-Price-currencySymbol'>£</span>";
                            $price_string .= $wholesale_normalprice;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                            $price_string .= '</p>';
                        $variation_object['html'] = $price_string;
                    }else{
                        // is product scheduled?
                        if ($this->isProductScheduled($variation['variation_id']) == 1){        //on sale or scheduled
                             $price_string = '<p class="price">';
                            $price_string .= '<del class="original-computed-price">';
                            $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                            $price_string .= $retail_price;
                            $price_string .= '</span>';
                            $price_string .= '</del>';
                            $price_string .= "<ins style='display: block;'>";
                            $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                            $price_string .= "<span class='woocommerce-Price-currencySymbol'>SALE: &#163;<span>";
                            $price_string .= $sale_wholesale_price;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                            $price_string .= '</p>';

                            $variation_object['html'] = $price_string;

                        }else{ //on sale and schedule set, but outside set dates
                             $price_string = '<p class="price">';
                            $price_string .= '<del class="original-computed-price">';
                            $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                            $price_string .= $retail_price;
                            $price_string .= '</span>';
                            $price_string .= '</del>';
                            $price_string .= "<ins style='display: block;'>";
                            $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                            $price_string .= $wholesale_normalprice;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                            $price_string .= '</p>';

                           $variation_object['html'] = $variation['price_html'];
                        }
                    }
                }else{
                            $price_string = '<p class="price">';
                            $price_string .= '<ins class="original-computed-price">';
                            $price_string .= '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">£</span>';
                            $price_string .= $retail_price;
                            $price_string .= '</span>';
                            $price_string .= '</ins>';
                            $price_string .= '</p>';
                        $variation_object['html'] = $price_string;
                }
        return $variation_object['html'];
    }
    }
    
    function simpleOnSale($product){
        $user_role = $this->getUserType();
        $wholesale = new WWP_Wholesale_Prices();
        $final = "";
        $onsale = false;
        
        if ($user_role == "wholesale_customer"){
            $sale_wholesale_price = $this->getWholesaleSalePrice($product->get_id());
            // is on sale?
            if ($sale_wholesale_price != 0){
                // is product scheduled?
                if ($this->isProductScheduled($product->get_id()) == 1){ 
                        $onsale = true;
                    } 
            }
        }else{ // retail customers
            if($product->get_sale_price() != ""){
                    $onsale = true;
            }
        }
        return $onsale;
    }
    
    function getWholesaleVariablePricehtml($product){
       // global $product;
        $available_variations = $product->get_available_variations();
        $user_role = $this->getUserType();
        $wholesale = new WWP_Wholesale_Prices();
        $final = "";
        if ($user_role == "wholesale_customer"){
            // is on sale? 
            $wholesaleprice_class = new WWP_Wholesale_Prices();
            $user_role = array($user_role);
            
            for($i=0;$i<count($available_variations);$i++){
                $wholesale_normalprice = $wholesaleprice_class->get_product_wholesale_price_on_shop( $available_variations[$i]['variation_id'],$user_role);
                
                    $variation_id = $available_variations[$i]['variation_id'];
                    $variation_object['onsale'] = false;

                    $variation_object['id'] = $variation_id;
                if ($wholesale_normalprice != null){
                     $wholesale_normalprice = number_format($wholesale_normalprice, 2);
                    $sale_wholesale_price = $this->getWholesaleSalePrice($available_variations[$i]['variation_id']);
                    $sale_wholesale_price = number_format($sale_wholesale_price, 2);



                    $price_string = "";
                    if ($sale_wholesale_price == 0){
                            $price_string .= "<ins class='wholesale_sale_price'>";
                            $price_string .= "<span class='woocommerce-Price-amount amount'>";
                            $price_string .= "<span class='woocommerce-Price-currencySymbol'>£</span>";
                            $price_string .= $wholesale_normalprice;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                    }else{
                        // is product scheduled?
                        if ($this->isProductScheduled($available_variations[$i]['variation_id']) == 1){    

                            $price_string .= "<ins class='wholesale_sale_price'>";
                            $price_string .= "<span class='woocommerce-Price-amount amount'>";
                            $price_string .= "<span class='woocommerce-Price-currencySymbol'>SALE: &#163;<span>";
                            $price_string .= $sale_wholesale_price;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                            $variation_object['onsale'] = true;

                        }else{
                            $price_string .= "<ins class='wholesale_sale_price'>";
                            $price_string .= "<span class='woocommerce-Price-amount amount'>";
                            $price_string .= "<span class='woocommerce-Price-currencySymbol'>£</span>";
                            $price_string .= $wholesale_normalprice;
                            $price_string .= "</span>";
                            $price_string .= "</ins>";
                        }
                    }
                    $variation_object['html'] = $price_string;
                  }else{
                    $temp = $available_variations[$i];
                    $variation_object['html'] = $temp['price_html'];
                }
                
                $final[] = $variation_object;
        }

        return $final;
            
        }else if($user_role == "retail" || $user_role == "admin"){
               for($i=0;$i<count($available_variations);$i++){
                   $product = new WC_Product_Variation($available_variations[$i]['variation_id']);
                   $variation_object['onsale'] = false;
                   $variation_object['html'] = $product->get_price_html();
    
                   if ($product->get_sale_price() != ""){
                     $variation_object['onsale'] = true; 
                   }
                   $variation_object['id'] = $available_variations[$i]['variation_id'];
                   $final[] = $variation_object;
               }
            return $final;
        }
    }
    
    function getSalePriceForCart($cart_object){
       $current_user = $this->getUserType();
       if ($current_user == "wholesale_customer"){
            foreach ( $cart_object->cart_contents as  $value ) {
                // first check if variable of normal then isolate id
                if ($value['variation_id'] != 0){
                     // variable product
                    $product_id = $value['variation_id'];
                }else{
                    // normal product
                    $product_id = $value['product_id'];
                }
                $sale_wholesale_price = $this->getWholesaleSalePrice($product_id);
                
                // is on sale?
                if ($sale_wholesale_price != 0){
                // is product scheduled?
                if ($this->isProductScheduled($product_id) == 1){ 
       
                    $value['data']->set_price($sale_wholesale_price);
                }
            } 
       }
    }
    }
    
     function getWholesalePricehtmlSimple($product){
        $user_role = $this->getUserType();
        $wholesale = new WWP_Wholesale_Prices();
        $final = "";
        $non_sale = str_replace('<del class="original-computed-price"><span class="woocommerce-Price-amount amount">','<del class="original-computed-price"><span class="woocommerce-Price-amount amount">RRP: ',$product->get_price_html());  

         
        if ($user_role == "wholesale_customer"){
            $sale_wholesale_price = $this->getWholesaleSalePrice($product->get_id());
            // is on sale?
            if ($sale_wholesale_price == 0){
                $final = $non_sale;
            }else{
                // is product scheduled?
                if ($this->isProductScheduled($product->get_id()) == 1){ 

                    $sale_wholesale_price = number_format($sale_wholesale_price, 2);

                    $price_string = $non_sale;
                    //$price_string = str_replace("<ins>","<del>",$price_string);                    
                   // $price_string = str_replace("</ins>","</del>",$price_string);

                    if ( $product->is_type( 'simple' ) ){
                        $price_string .= "<ins>";
                        $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>SALE: &#163;<span>";
                        $price_string .= $sale_wholesale_price;
                        $price_string .= "</span>";
                        $price_string .= "</ins>";
                    } 
                    
                    
                    $final = $price_string;
                    
                }else{
                    $final = $non_sale;
                }
            }
        return $final;
            
        }else if($user_role == "retail" || $user_role == "admin"){

            return $product->get_price_html();
            
        }

    }    

    function getWholesalePricehtml($product){
        $user_role = $this->getUserType();
     //   global $product;
        $final = $product->get_price_html();
         $final = str_replace('<del class="original-computed-price">','<ins class="original-computed-price">',$final);
          $final = str_replace('<ins class="original-computed-price"><span class="woocommerce-Price-amount amount">','<ins class="original-computed-price"><span class="woocommerce-Price-amount amount">RRP: ',$final); 
              
       
        if ($user_role == "wholesale_customer"){
            // remove sale from retail
           if ( $product->is_type( 'simple' ) ){  
           add_filter( 'woocommerce_product_get_sale_price', function($sale_price, $product_int){return $product_int->get_regular_price();}, 50, 2 );
           $sale_wholesale_price = $this->getWholesaleSalePrice($product->get_id());
               
           $retail_price = $product->get_regular_price();
        $retail_price = number_format($retail_price, 2);  
               
            $wholesaleprice_class = new WWP_Wholesale_Prices();
            $user_role = array($user_role);
            $wholesale_normalprice = $wholesaleprice_class->get_product_wholesale_price_on_shop($product->get_id(),$user_role);
            if ($wholesale_normalprice == ""){ return $product->get_price_html();}
            $wholesale_normalprice = number_format($wholesale_normalprice, 2);
            // is on sale?
            if ($sale_wholesale_price != 0){
                 $sale_wholesale_price = number_format($sale_wholesale_price, 2);
                // is product scheduled?
                if ($this->isProductScheduled($product->get_id()) == 1){ 

                        $price_string = '<p class="price">';
                     if ($retail_price != -1){
                        $price_string .= '<del class="original-computed-price">';
                        $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $retail_price;
                        $price_string .= '</span>';
                        $price_string .= '</del>';
                         }
                        $price_string .= "<ins style='display: block;'>";
                        $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>SALE: &#163;<span>";
                        $price_string .= $sale_wholesale_price;
                        $price_string .= "</span>";
                        $price_string .= "</ins>";
                        $price_string .= '</p>';
                    }
                else{ // simple, on sale but not scheduled

                        $price_string = '<p class="price">';
                    if ($retail_price != -1){
                        $price_string .= '<del class="original-computed-price">';
                        $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $retail_price;
                        $price_string .= '</span>';
                        $price_string .= '</del>';
                    }
                        $price_string .= "<ins style='display: block;'>";
                        $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>&#163;<span>";
                        $price_string .= $wholesale_normalprice;
                        $price_string .= "</span>";
                        $price_string .= "</ins>";
                        $price_string .= '</p>';
                } 
                }
               else{ // simple, but not on sale
                       
                   
                        $price_string = '<p class="price">';
                                  if ($retail_price != -1){
                        $price_string .= '<del class="original-computed-price">';
                        $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $retail_price;
                        $price_string .= '</span>';
                        $price_string .= '</del>';
                                  }
                        $price_string .= "<ins style='display: block;'>";
                        $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>&#163;<span>";
                        $price_string .= $wholesale_normalprice;
                        $price_string .= "</span>";
                        $price_string .= "</ins>";
                        $price_string .= '</p>';
            }
            }else{ // deal with variable product in a seperate function.
                $price_string = $this->getWholesaleVariableStartPricehtml($product); //
        }
            
        $final = $price_string;
             
        return $final;
            
        }else if($user_role == "retail" || $user_role == "admin"){

            return $final;
            
        }

    }
    
    function getWholesaleVariableStartPricehtml($product){
        $available_variations = $product->get_available_variations();
        $user_role = $this->getUserType();
        $wholesale = new WWP_Wholesale_Prices();
        $final = "";
       
        if ($user_role == "wholesale_customer"){
            $user_role = array($user_role);
            // find out highest and lowest retail price (normal)
            $max_retail_price = $product->get_variation_regular_price( 'max', true );
            $min_retail_price = $product->get_variation_regular_price('min', true );
            // calculate highest and lowest wholesale price
            foreach($available_variations as $available_variation){
                        $variation_prices[] = $wholesale->get_product_wholesale_price_on_shop($available_variation['variation_id'],$user_role);
                        $temp = $this->getWholesaleSalePrice($available_variation['variation_id']);
                if ($temp != 0){
                    $variation_sale_prices[] = $temp;
                }
            }
            
            if (count($variation_sale_prices) > 0){
                $variation_prices = array_merge($variation_prices, $variation_sale_prices);
                 while (($key = array_search(0, $variation_prices)) !== false) {
                    unset($variation_prices[$key]);
                }

                $max_wholesale_price = max($variation_prices);
                $min_wholesale_price = min($variation_prices);
                $sale_wholesale_price = $this->getWholesaleSalePrice($product->get_id());
                 $sale_wholesale_price = number_format($sale_wholesale_price, 2);
                 $min_retail_price = number_format($min_retail_price, 2);
                 $max_retail_price = number_format($max_retail_price, 2);
                 $min_wholesale_price = number_format($min_wholesale_price, 2);
                 $max_wholesale_price = number_format($max_wholesale_price, 2);
                    // is on sale?
                    $price_string = '<p class="price">';
                    $price_string .= '<del class="original-computed-price">';
                    if ($min_retail_price == $max_retail_price){
                        $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $min_retail_price;
                        $price_string .= '</span>';
                    }else{
                        $price_string .= '<span class="woocommerce-Price-amount amount">RRP: <span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $min_retail_price;
                        $price_string .= ' - ';
                        $price_string .= '<span class="woocommerce-Price-currencySymbol">£</span>';
                        $price_string .= $max_retail_price;
                        $price_string .= '</span>';
                    }
                    $price_string .= '</del>';
                    $price_string .= "<ins style='display: block;'>";
                    $price_string .= " " . "<span class='sale_wholesale woocommerce-Price-amount amount'>";
                    if ($min_wholesale_price == $max_wholesale_price){
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>&#163;<span>";
                        $price_string .= $min_wholesale_price;
                        $price_string .= "</span>";
                    }else{
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>&#163;<span>";
                        $price_string .= $min_wholesale_price;
                        $price_string .= "</span>";
                        $price_string .= " - ";
                        $price_string .= "<span class='woocommerce-Price-currencySymbol'>&#163;<span>";
                        $price_string .= $max_wholesale_price;
                        $price_string .= "</span>";
                        $price_string .= "</ins>";
                        $price_string .= '</p>';
                    }
                   
            
                    return $price_string;
                }else{
                  $final = $product->get_price_html();
                  $final = str_replace('<del class="original-computed-price"><span class="woocommerce-Price-amount amount">','<del class="original-computed-price"><span class="woocommerce-Price-amount amount">RRP: ',$final); 
               return $final;
            }

        }else if($user_role == "retail" || $user_role == "admin"){
            return;
        }
        
    }
    
    protected function isProductScheduled($product_id){
        $current_timestamp = time();
        $start_timestamp = get_post_meta($product_id, '_wholesale_sale_start_time', true);
        $end_timestamp = get_post_meta($product_id, '_wholesale_sale_end_time', true);
        $issale = 0;
        // allow sale if neither parameters are set
        if ($start_timestamp == "" && $end_timestamp == ""){
            return 1;
        }
        if ($start_timestamp != ""){
            if ($current_timestamp >= $start_timestamp){
                $issale = 1;
            }
            else{
                $issale = 0;
            }
        }
        if ($end_timestamp != ""){
            if ($current_timestamp <= $end_timestamp){
                $issale = 1;
            }else{
                $issale = 0;
            }
        } 
         if ($end_timestamp != "" && $start_timestamp != ""){
             if ($current_timestamp >= $start_timestamp && $current_timestamp <= $end_timestamp){
                $issale = 1; 
             }else if($current_timestamp < $start_timestamp){
                 $issale = 0;
             }else if($current_timestamp > $end_timestamp){
                 $issale = 0;
             }
         }
        return $issale;
    }
    
    function getWholesaleSalePrice($product_id){
        // then find out if that product is on sale
        $wholesale_sale = get_post_meta($product_id, '_wholesale_sale_price', true);

        if ($wholesale_sale == ""){
            // not on wholesale sale 
            $wholesale_sale = 0;
        }
        return $wholesale_sale;        
    }    
    protected function getUserType(){
        // first find out if user is retail or wholesale
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
}

?>


    <?php //    echo "<script>console.log('test');</script>";
 //echo "<script>console.log(".json_encode($variations).");</script>"; ?>
