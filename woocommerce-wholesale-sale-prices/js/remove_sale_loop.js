jQuery(document).ready(function ($) {
    if (sale_script_loop.remove_sale != null) {
        if (sale_script_loop.remove_sale[0] == "sale") {
            // for sale page
            $("ul.products li a").each(function () {
                $(this).append("<span class='onsale'>Sale!</sale>");
            });
        } else if (sale_script_loop.remove_sale.length > 0) {
            // for woocommerce loop pages
            for (var i = 0; i < sale_script_loop.remove_sale.length; i++) {
                if (sale_script_loop.remove_sale[i] != null) {
                    var test = $("body").find(".post-" + sale_script_loop.remove_sale[i]);
                    var test2 = $(test).find('span.onsale');
                    $(test2).show();
                }
            }
        }
    }
});
