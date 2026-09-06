<?php
    function shrishanti_register_my_cpts_shri_catalog() {

	/**
	 * Post Type: Catalog.
	 */

	$labels = [
		"name" => esc_html__( "Catalog", "shri-shanti" ),
		"singular_name" => esc_html__( "Catalog", "shri-shanti" ),
		"featured_image" => esc_html__( "Catalog Image", "shri-shanti" ),
	];

	$args = [
		"label" => esc_html__( "Catalog", "shri-shanti" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "shri_catalog", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "shri_catalog", $args );
}

add_action( 'init', 'shrishanti_register_my_cpts_shri_catalog' );

function shrishanti_shri_catalog_register_meta() {
	register_post_meta( "shri_catalog", "_shri_catalog_product_details", [
		"type" => "string",
		"single" => true,
		"show_in_rest" => true,
		"sanitize_callback" => "wp_kses_post",
		"auth_callback" => function() {
			return current_user_can( "edit_posts" );
		},
	] );
}
add_action( 'init', 'shrishanti_shri_catalog_register_meta' );

function shrishanti_shri_catalog_add_meta_box() {
	add_meta_box(
		"shri_catalog_details",
		esc_html__( "Product Details", "shri-shanti" ),
		"shrishanti_shri_catalog_meta_box_html",
		"shri_catalog",
		"normal",
		"high"
	);
}
add_action( 'add_meta_boxes', 'shrishanti_shri_catalog_add_meta_box' );

function shrishanti_shri_catalog_meta_box_html( $post ) {
	wp_nonce_field( "shri_catalog_meta_nonce", "shri_catalog_meta_nonce" );

	$value = get_post_meta( $post->ID, "_shri_catalog_product_details", true );

	echo '<p><label for="shri_catalog_product_details">' . esc_html__( "Enter variants, thickness, materials, width, etc. Use ", "shri-shanti" ) . '<code>&lt;br&gt;</code>' . esc_html__( " for line breaks.", "shri-shanti" ) . '</label></p>';
	echo '<textarea id="shri_catalog_product_details" name="shri_catalog_product_details" rows="8" style="width:100%;">' . esc_textarea( $value ) . '</textarea>';
}

function shrishanti_shri_catalog_save_meta( $post_id ) {
	if ( ! isset( $_POST["shri_catalog_meta_nonce"] ) || ! wp_verify_nonce( $_POST["shri_catalog_meta_nonce"], "shri_catalog_meta_nonce" ) ) {
		return;
	}
	if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( "edit_post", $post_id ) ) {
		return;
	}

	if ( isset( $_POST["shri_catalog_product_details"] ) ) {
		update_post_meta( $post_id, "_shri_catalog_product_details", wp_kses_post( wp_unslash( $_POST["shri_catalog_product_details"] ) ) );
	}
}
add_action( 'save_post_shri_catalog', 'shrishanti_shri_catalog_save_meta' );

?>