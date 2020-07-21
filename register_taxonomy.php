<?php
// Adds a custom rule type.
add_filter( 'acf/location/rule_types', function( $choices ){
    $choices[ __("Other",'acf') ]['wc_prod_attr'] = 'WC Product Attribute';
    return $choices;
} );

// Adds custom rule values.
add_filter( 'acf/location/rule_values/wc_prod_attr', function( $choices ){
	return array();
} );

// Matching the custom rule.
add_filter( 'acf/location/rule_match/wc_prod_attr', function( $match, $rule, $options ){
    if ( isset( $options['taxonomy'] ) ) {
		$match = false;
		foreach ( wc_get_attribute_taxonomies() as $attr ) {
			if ($options['taxonomy'] == wc_attribute_taxonomy_name( $attr->attribute_name ) && $attr->attribute_type == 'icon_logo' ) {
				$match = true;
				break;
			}
		}

		if ( '!=' === $rule['operator'] ) {
            $match = !$match;
        }
    }
    return $match;
}, 10, 3 );

if ($_GET['page']=='product_attributes') {
	add_filter( 'product_attributes_type_selector', 'porto_product_attributes_types', 100 );
}
function porto_product_attributes_types( $selector ) {
	$selector['icon_logo'] = __('Icon Logo', 'porto');
	return $selector;
}
