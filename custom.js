(function($) {
    function move_nav_bar() {
        var $nav = $('.product-nav');
        $nav.appendTo($nav.closest('.product'));
        $nav.show();
    }

    function move_single_product_price() {
        var $stock = $('.single-product #content .product_meta .stock');
        if ($stock.length > 0) {
            $stock.prependTo($('#content .product .cart'));
        }

        var $price = $('.single-product #content .product-summary-wrap .entry-summary p.price');
        if ($price.length == 0) return;
        $price.prependTo($('#content .product .cart'));
    }

    function move_variation_property_logos() {
        $logos = $('.single_variation_wrap ~ .property-logos');
        if ($logos.length == 0) return;
        $logos.insertBefore($logos.prev());
    }

    function reorder_product_items() {
        move_nav_bar();
        move_single_product_price();
        move_variation_property_logos();
    }

    $(document).ready(function(){
        var def_ean_value = $('.product_meta .ean').text();
        $(document).on( 'reset_data', '.variations_form', function () {
            $('.product_meta .ean').text(def_ean_value);
        } );
        $(document).on( 'show_variation', '.variations_form', function ( event, variation ) {
            $('.product_meta .ean').text(variation.ean);
            console.log(variation);
        } );

        // move product-nav
        reorder_product_items();
    });

}).apply(this, [jQuery]);