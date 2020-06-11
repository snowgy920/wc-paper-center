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

add_action( 'wp_enqueue_scripts', 'porto_child_js', 1002 );
function porto_child_js() {
	wp_register_script( 'porto-child-js', esc_url( get_stylesheet_directory_uri() ) . '/custom.js', array('jquery'), '', true);
	wp_enqueue_script( 'porto-child-js' );
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


/* EAN */
add_action( 'woocommerce_product_options_sku', 'porto_create_custom_field_simple' );
function porto_create_custom_field_simple() {
	$product_id = intval( $_REQUEST['post'] );
	$ean = get_post_meta( $product_id, '_ean', true );
	$args = array(
		'id' => 'ean_code',
		'label' => __( 'EAN Code', 'porto' ),
		'value' => $ean,
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
// display ean codes
add_action( 'woocommerce_product_meta_end', 'porto_meta_ean');
function porto_meta_ean() {
	global $product;
	$ean = get_post_meta( $product->get_id(), '_ean', true );
?>
	<span class="sku_wrapper"><?php esc_html_e( 'EAN-CODE:', 'woocommerce' ); ?> <span class="ean"><?php echo ! empty( $ean ) ? esc_html( $ean ) : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
<?php
}

add_filter( 'woocommerce_available_variation', 'porto_variation_ean', 100, 3 );
function porto_variation_ean($vars, $product, $variation) {
	$ean = get_post_meta( $variation->get_id(), '_ean', true );
	if (empty($ean)) {
		$ean = get_post_meta( $product->get_id(), '_ean', true );
	}
	$vars['ean'] = !empty($ean) ? esc_html( $ean ) : esc_html__( 'N/A', 'woocommerce' );
	return $vars;
}


/* property logos */
// register
require_once('register_taxonomy.php');
function porto_show_property_logo() {
	global $product;
	$terms = get_the_terms($product->get_id(), 'property_logo');
	if (empty($terms)) return;
?>
	<div class="property-logos">
		<label><?php echo __('Attributes:', 'porto')?></label>
	<?php
	foreach ($terms as $t) {
		$image_id = get_term_meta($t->term_id, 'property-logo-image-id', true);
		echo wp_get_attachment_image($image_id, array('32', '32'), '', array('class'=>'property-logo', 'title'=>$t->name));
	}
?>
	</div>
<?php
}
// display logos on shop page
add_action( 'woocommerce_after_shop_loop_item', 'porto_show_property_logo', 20 );


/* product page */
add_action( 'woocommerce_after_single_product_summary', 'porto_woocommerce_product_nav_back', 5 );
function porto_woocommerce_product_nav_back() {
	if ( porto_is_product() ) {
		global $product;
		$terms = get_the_terms( $product->get_id(), 'product_cat');
		$cat_url = get_term_link($terms[0]->term_id, 'product_cat');
		?>
		<div class="product-nav-back">
			<a class="button" href="<?php echo $cat_url?>"><i class="fa fa-angle-double-left" aria-hidden="true"></i><?php echo __('Back', 'woocommerce')?></a>
		</div>
	<?php
	}
}

// move price block under the meta info
add_action( 'woocommerce_before_single_product', 'porto_product_summary_hook' );
function porto_product_summary_hook(){
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 29 );

	remove_action( 'woocommerce_product_meta_start', 'porto_woocommerce_add_stock_html', 10 );
	add_action( 'woocommerce_product_meta_start', 'porto_custom_woocommerce_add_stock_html', 10 );

	// move custom block to the start of bottom content
	remove_action( 'porto_after_content_bottom', 'porto_woocommerce_product_output_content_bottom', 10 );
	add_action( 'porto_before_content_bottom', 'porto_woocommerce_product_output_content_bottom', 10 );

	// move upsells block
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	add_action( 'porto_after_content_bottom', 'woocommerce_upsell_display', 7 );

	// display property logos
	global $product;
	if ($product->is_type('variable')) {
		add_action( 'woocommerce_after_variations_form', 'porto_show_property_logo', 10 );
	} else {
		add_action( 'woocommerce_single_product_summary', 'porto_show_property_logo', 28 );
	}

	// show product tags
	add_action( 'porto_after_content_bottom', 'porto_product_tags', 8);
}
// remove availability text for stock label
function porto_custom_woocommerce_add_stock_html() {
	global $product;
	if ( $product->is_type( 'simple' ) ) {
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<span class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</span>';
		echo apply_filters( 'porto_woocommerce_stock_html', $availability_html, $availability['availability'], $product );
	}
}


// Hide price range
// add_filter('woocommerce_get_price_html', 'porto_hide_variation_price', 10, 2);
function porto_hide_variation_price( $v_price, $v_product ) {
	$v_product_types = array( 'variable');
	if ( in_array ( $v_product->product_type, $v_product_types ) && !(is_shop()) ) {
		return '';
	}
	return $v_price;
}

// Always show single variation price
function porto_always_show_variation_prices($show, $parent, $variation) {
	return true;
}
add_filter( 'woocommerce_show_variation_price', 'porto_always_show_variation_prices', 99, 3);


// show product tags
function porto_product_tags() {
	$terms = wp_get_post_terms( get_the_id(), 'product_tag' );
	if( count($terms) > 0 ){
		$output = array();
		?>
		<div class="tags">
			<div class="container">
				<h2 class="slider-title"><?php echo __('Tags', 'woocommerce')?></h2>
				<div class="tag-list">
					<?php
					foreach($terms as $term){
						$term_name = $term->name; // Product tag Name
						$term_link = get_term_link( $term, 'product_tag' ); // Product tag link

						$output[] = '<a href="'.$term_link.'">'.$term_name.'</a>';
					}
					// Set the array in a coma separated string of product tags for example
					echo implode( ', ', $output );
					?>
				</div>
			</div>
		</div>
	<?php
	}
}
