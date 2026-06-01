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

?>