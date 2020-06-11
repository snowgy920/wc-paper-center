<?php
add_action( 'init', 'porto_create_custom_taxonomies', 0 );
function porto_create_custom_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Property Logos', 'taxonomy general name', 'porto' ),
		'singular_name'     => _x( 'Property Logo', 'taxonomy singular name', 'porto' ),
		'search_items'      => __( 'Search Property', 'porto' ),
		'all_items'         => __( 'All Properties', 'porto' ),
		'parent_item'       => __( 'Parent Property', 'porto' ),
		'parent_item_colon' => __( 'Parent Property:', 'porto' ),
		'edit_item'         => __( 'Edit Property Logo', 'porto' ),
		'update_item'       => __( 'Update Property Logo', 'porto' ),
		'add_new_item'      => __( 'Add New Property', 'porto' ),
		'new_item_name'     => __( 'New Property Name', 'porto' ),
		'menu_name'         => __( 'Property Logos', 'porto' ),
		'view_item'         => __( 'View Property Logo', 'porto' ),
		'popular_items'     => __( 'Popular Property Logos', 'porto' ),
		'not_found'         => __( 'No property logos found.', 'porto' ),
		'add_or_remove_items'   => __( 'Add or remove property logos', 'porto' ),
		'choose_from_most_used' => __( 'Choose from the mose used property logos', 'porto' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'property_logo' ),
	);

	register_taxonomy( 'property_logo', array( 'product' ), $args );
}

require_once('classes/class_property_logo_images.php');

/*
//add extra fields to custom taxonomy edit form callback function
add_action('property_logo_add_form_fields', 'porto_tax_add_custom_field', 10, 2 );
add_action('property_logo_edit_form_fields', 'porto_tax_add_custom_field', 10, 2 );
function porto_tax_add_custom_field($tag) {
   //check for existing taxonomy meta for term ID
    $t_id = $tag->term_id;
    $term_meta = get_option( "taxonomy_$t_id");
?>
<tr class="form-field">
    <th scope="row" valign="top"><label for="cat_Image_url"><?php _e('Category Image Url'); ?></label></th>
    <td>
        <input type="text" name="term_meta[img]" id="term_meta[img]" size="3" style="width:60%;" value="<?php echo $term_meta['img'] ? $term_meta['img'] : ''; ?>"><br />
        <span class="description"><?php _e('Image for Term: use full url with '); ?></span>
    </td>
</tr>
<?php
}

// save extra taxonomy fields callback function
add_action('created_property_logo', 'porto_tax_save_custom_field', 10, 2);
function porto_tax_save_custom_field( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key){
            if (isset($_POST['term_meta'][$key])){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_$t_id", $term_meta );
    }
}
*/