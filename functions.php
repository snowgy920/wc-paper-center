<?php

add_action('wp_enqueue_scripts', 'porto_child_css', 1001);

// Load CSS
function porto_child_css()
{
	// porto child theme styles
	wp_deregister_style('styles-child');
	wp_register_style('styles-child', esc_url(get_stylesheet_directory_uri()) . '/style.css');
	wp_enqueue_style('styles-child');

	if (is_rtl()) {
		wp_deregister_style('styles-child-rtl');
		wp_register_style('styles-child-rtl', esc_url(get_stylesheet_directory_uri()) . '/style_rtl.css');
		wp_enqueue_style('styles-child-rtl');
	}
}

// Porto Product Category2
include_once('shortcodes/woo_shortcodes/porto_product_category2.php');



/***Shipping Costs free*/
function my_hide_shipping_when_free_is_available($rates)
{
	$free = array();
	foreach ($rates as $rate_id => $rate) {
		if ('free_shipping' === $rate->method_id) {
			$free[$rate_id] = $rate;
			break;
		}
	}
	return !empty($free) ? $free : $rates;
}
add_filter('woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100);


add_action( 'woocommerce_product_options_sku', 'porto_create_custom_field_simple' );
function porto_create_custom_field_simple() {
	$args = array(
		'id' => 'ean_code',
		'label' => __( 'EAN Code', 'porto' ),
		'class' => 'short',
	);
	woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_variation_options', 'porto_create_custom_field_variable', 10, 3 );
function porto_create_custom_field_variable($loop, $variation_data, $variation) {
	$args = array(
		'id'            => "variable_ean{$loop}",
		'name'          => "variable_ean[{$loop}]",
		'label'         => esc_html__( 'EAN Code', 'woocommerce' ),
		'value'			=> $variation_data['_ean'][0],
		'placeholder'   => get_post_meta($variation->post_parent, '_ean', true),
		'wrapper_class' => 'form-row form-row-last',
	);
	woocommerce_wp_text_input( $args );
}

add_action( 'woocommerce_process_product_meta', 'porto_save_custom_field_simple', 10, 1 );
function porto_save_custom_field_simple( $post_id ){
    if( isset($_POST['ean_code']) ) {
		update_post_meta( $post_id, '_ean', esc_attr( $_POST['ean_code'] ) );
	}
}
add_action( 'woocommerce_process_product_meta', 'porto_save_custom_field_variable', 10, 1 );
function porto_save_custom_field_variable( $post_id ){
	if (isset( $_POST['variable_post_id'] ) ) {
		$variable_post_id      = $_POST['variable_post_id'];

		$ean_code = $_POST['variable_ean'];
		for ( $i = 0; $i < sizeof( $variable_post_id ); $i++ ) {
			if ( isset( $ean_code[$i] ) ) {
				update_post_meta( $variable_post_id[$i], '_ean', stripslashes( $ean_code[$i] ) );
			}
		}
	}
}