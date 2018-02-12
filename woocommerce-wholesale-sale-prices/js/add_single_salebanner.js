jQuery(document).ready(function ($) {
    //for sale banner if Twist installed   
    $(".images.twist-wrap span.onsale.gl_sale").remove();
        if (simple_sale_script.onsale == true){     
              $(".images.twist-wrap ").append("<span class='onsale gl_sale'>Sale!</span>");
        }
});