jQuery(document).ready(function ($) {+
    if (sale_script.sale) {
        $(".original-computed-price ins").hide();
        $(".wholesale_price_container").wrap("<del></del>");
        $(".wholesale_price_container span").addClass("wholesale_sale");
        $(".price ins").css("margin-left", "-10px");
      
    } else {
        $(".onsale").hide();
        $(".images.twist-wrap span.onsale.gl_sale").remove();
    }
});
