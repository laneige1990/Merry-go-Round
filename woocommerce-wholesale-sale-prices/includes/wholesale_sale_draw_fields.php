 <?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Display Fields - Variable products

add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );

function variation_settings_fields($loop, $variation_data, $variation) {

  global $woocommerce, $post;
  
  echo '<div class="form-row">';
  
    // wholesale sale price - Text Field
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale', 
            'label'       => __( 'Wholesale sale price (£)', 'woocommerce' ), 
            'desc_tip'    => 'true',
            'description' => __( 'Only applies to users with the role of Wholesale Customer', 'woocommerce' ),
            'value'       => get_post_meta( $variation->ID, '_wholesale_sale_price', true ),
            'name' => 'wholesale_sale_' . $variation->ID
        )
    );
  
  echo '</div>';  
    
  echo '<div class="form-row form-row-first gl_wholesale_sale_row">';
  
    // wholesale sale price - Start Date, added as text input. Date picker added via JS
    $date = get_post_meta( $variation->ID, '_wholesale_sale_start_time', true );
    $date = date("Y-m-d",$date);
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale_start_date_' . $variation->ID, 
            'label'       => __( 'Wholesale sale start date', 'woocommerce' ),
            'placeholder'       => "From... YYYY-MM-DD",
            'value' => $date,
            'name' => 'start_date_' . $variation->ID,
            'class' => 'start_picker'
        )
    );  
      echo '</div>';
      echo '<div class="form-row form-row-last gl_wholesale_sale_row">';
    
    // wholesale sale price - End Date
    $date = get_post_meta( $variation->ID, '_wholesale_sale_end_time', true );
    $date = date("Y-m-d",$date);
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale_end_date_' . $variation->ID, 
            'label'       => __( 'Wholesale sale end date', 'woocommerce' ),
            'placeholder'       => "To... YYYY-MM-DD",
            'value' => $date,
            'name' => 'end_date_' . $variation->ID,
            'class' => 'end_picker'
        )
    );
  
  echo '</div>';
	
}

// Save Variation Settings
add_action( 'woocommerce_save_product_variation', 'save_wholesale_variation_sale_price', 10, 2 );

function save_wholesale_variation_sale_price( $post_id ) {
	// wholesale sale price
    $text_field = $_POST['wholesale_sale_' . $post_id];
    if (trim($text_field) !== ""){
		update_post_meta( $post_id, '_wholesale_sale_price', esc_attr( $text_field ) );
     }else{
        delete_post_meta( $post_id, '_wholesale_sale_price');
    }
       
    // wholesale start sale
	$text_field = $_POST['start_date_' . $post_id];
    if (trim($text_field) !== ""){
        $text_field = strtotime($text_field);
        update_post_meta( $post_id, '_wholesale_sale_start_time', esc_attr( $text_field ) );
    }else{
        delete_post_meta( $post_id, '_wholesale_sale_start_time');
    }
        
    // wholesale end sale
	$text_field = $_POST['end_date_' . $post_id];
     if (trim($text_field) !== ""){
        $text_field = strtotime($text_field);
        update_post_meta( $post_id, '_wholesale_sale_end_time', esc_attr( $text_field ) );
    }else{
        delete_post_meta( $post_id, '_wholesale_sale_end_time');
    }
}

// Display Fields - Simple products

add_action( 'woocommerce_product_options_general_product_data', 'gl_wholesale_sale_products' );

function gl_wholesale_sale_products(){
     global $woocommerce, $post;
  echo '<div class="form-row">';
  
    // wholesale sale price - Text Field
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale', 
            'label'       => __( 'Wholesale sale price (£)', 'woocommerce' ), 
            'desc_tip'    => 'true',
            'description' => __( 'Only applies to users with the role of Wholesale Customer', 'woocommerce' ),
            'value'       => get_post_meta( get_the_ID(), '_wholesale_sale_price', true )
            
        )
    );
  
  echo '</div>';  
    
  echo '<div class="form-row form-row-first gl_wholesale_sale_row">';
  
    // wholesale sale price - Start Date, added as text input. Date picker added via JS
    $date = get_post_meta( get_the_ID(), '_wholesale_sale_start_time', true );
    if ($date != ""){ $date = date("Y-m-d",$date);}
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale_start_date_' . get_the_ID(), 
            'label'       => __( 'Wholesale sale start date', 'woocommerce' ),
            'placeholder'       => "From... YYYY-MM-DD",
            'value' => $date,
            'name' => 'start_date',
            'class' => 'start_picker'
        )
    );  
      echo '</div>';
      echo '<div class="form-row form-row-last gl_wholesale_sale_row">';
    
    // wholesale sale price - End Date
    $date = get_post_meta( get_the_ID(), '_wholesale_sale_end_time', true );
     if ($date != ""){ $date = date("Y-m-d",$date);}
    woocommerce_wp_text_input( 
        array( 
            'id'          => 'wholesale_sale_end_date_'  . get_the_ID(), 
            'label'       => __( 'Wholesale sale end date', 'woocommerce' ),
            'placeholder'       => "To... YYYY-MM-DD",
            'value' => $date,
            'name' => 'end_date',
            'class' => 'end_picker'
        )
    );
    
  echo '</div>';
        
}

// Save Variation Settings
add_action( 'woocommerce_process_product_meta', 'save_wholesale_sale_price', 10, 1);

function save_wholesale_sale_price( $post_id ) {
    $product = wc_get_product($post_id);
    if($product->is_type( 'simple' )){
        // wholesale sale price
        $text_field = $_POST['wholesale_sale'];
        if (trim($text_field) !== ""){
            update_post_meta( $post_id, '_wholesale_sale_price', esc_attr( $text_field ) );
        }else{
            delete_post_meta( $post_id, '_wholesale_sale_price');
        }
        // wholesale start sale
        $text_field = $_POST['start_date'];
        if (trim($text_field) !== ""){
            $text_field = strtotime($text_field);
            update_post_meta( $post_id, '_wholesale_sale_start_time', esc_attr( $text_field ) );
        }else{
            delete_post_meta( $post_id, '_wholesale_sale_start_time');
        }

        // wholesale end sale
        $text_field = $_POST['end_date'];
         if (trim($text_field) !== ""){
            $text_field = strtotime($text_field);
            update_post_meta( $post_id, '_wholesale_sale_end_time', esc_attr( $text_field ) );
        }else{
            delete_post_meta( $post_id, '_wholesale_sale_end_time');
        }
    }else{
        // this flags the master product for views
        $variation_sale = isPrimaryOnSale();
        if ($variation_sale == 1){
            update_post_meta( $post_id, '_wholesale_sale_price', -1 );
        }else if ($variation_sale == 0){
             delete_post_meta( $post_id, '_wholesale_sale_price');
        }
    }
}

function isPrimaryOnSale(){
     $variation_ids = $_POST['variable_post_id'];
     if(!isset($variation_ids)){ return 2; }
        foreach($variation_ids as $variation_id){
            if ($_POST['wholesale_sale_'. $variation_id] != ""){
                if (isProductScheduled($variation_id) == 1){
                    return 1;
                }
            }
        }
    return 0;
}

   function isProductScheduled($variation_id){
        $current_timestamp = time();
        $start_timestamp = strtotime($_POST['start_date_'. $variation_id]) ;
        $end_timestamp = strtotime($_POST['end_date_'. $variation_id]);
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

?>