jQuery(document).ready(function ($) {

    $(".rrp_class").remove();
    $(".original-computed-price").prepend("<span class='rrp_class'> RRP: </span>");

    function add_sale_variation_price_html() {
        var $variations_form = $(".variations_form"),
            variation_id = $variations_form.find(".single_variation_wrap .variation_id").attr('value');
        var variations = variation_script.prices;
        $(".rrp_class").remove();
        //$(".product-type-variable .original-computed-price").prepend("<span class='rrp_class'> RRP: </span>");

        if (variations != null) {
            for (var i = 0; i < variations.length; i++) {
                if (variations[i].id == variation_id) {
                    $(".onsale").show();
                           //for sale banner if Twist installed   
                     $(".images.twist-wrap span.onsale.gl_sale").remove();
                    if (variations[i].onsale == true){     
                          $(".images.twist-wrap ").append("<span class='onsale gl_sale'>Sale!</span>");
                    }
                    $("form.variations_form .wholesale_price_container ins span").wrap("<del></del>");
                    $(".wholesale_sale_price").remove();
                    if ($(".woocommerce-variation.single_variation").has('div').length ? "Yes" : "No" == "Yes") {
                               
                        $(".woocommerce-variation-price").empty();
                        $(".woocommerce-variation-price").append("<span class='price'>"+variations[i].html+"</span>");

                    } else {

                        $(".woocommerce-variation-price").append("<span class='price'><div class='wholesale_price_container'>" + variations[i].html + "</div></span>");
                    }
                }
            }
        }

        // if (variation_id == $single_variation.find(".price").length <= 0)
        // $single_variation.prepend(WWPVariableProductPageVars.variations[i]['price_html']);

    }

    $("body").on("found_variation", ".variations_form", add_sale_variation_price_html); // Only triggered on ajax complete

    add_sale_variation_price_html();
});
