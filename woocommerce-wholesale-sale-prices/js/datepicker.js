jQuery(document).on('woocommerce_variations_loaded', function (event) {
datePickers();});

jQuery(document).ready(function($) {
datePickers();});

function datePickers(){
        var test = jQuery('.start_picker').datepicker({
        dateFormat: "yy-mm-dd"
    }).on("change", function () {
        var start_id = jQuery(this).attr('id');
        var id = start_id.split('wholesale_sale_start_date_');
        var id = id[1];
        var start_date_current = jQuery(this).val();
        jQuery('#wholesale_sale_end_date_' + id).datepicker(
            'option', 'minDate', new Date(start_date_current)
        );
    });
    jQuery(test).each(function (index) {
        var minDate = jQuery(this).datepicker("getDate");
        var start_id = jQuery(this).attr('id');
        var id = start_id.split('wholesale_sale_start_date_');
        var id = id[1];
        if (minDate != null) {
            var day = minDate.getDate();
            var monthIndex = minDate.getMonth() +1;
            var year = minDate.getFullYear();
            var minDate = year + "-" + monthIndex + "-" + day;
     
        }
        jQuery('#wholesale_sale_end_date_' + id).datepicker({
            minDate: minDate,
            dateFormat: "yy-mm-dd"
        })
    });
}
