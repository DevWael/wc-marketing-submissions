<?php

namespace WMS\Modules;
class Post_Type {

	public function setup_hooks() {
		add_action( 'init', [ $this, 'cpt' ] );
	}

	public function cpt() {
		$labels = array(
			'name'               => _x( 'Marketing Submissions', 'Post Type General Name', 'wms' ),
			'singular_name'      => _x( 'Submission', 'Post Type Singular Name', 'wms' ),
			'menu_name'          => __( 'M. Submissions', 'wms' ),
			'parent_item_colon'  => __( 'Parent Submission', 'wms' ),
			'all_items'          => __( 'All Submissions', 'wms' ),
			'view_item'          => __( 'View Submission', 'wms' ),
			'add_new_item'       => __( 'Add New Submission', 'wms' ),
			'add_new'            => __( 'Add New', 'wms' ),
			'edit_item'          => __( 'Edit Submission', 'wms' ),
			'update_item'        => __( 'Update Submission', 'wms' ),
			'search_items'       => __( 'Search Submission', 'wms' ),
			'not_found'          => __( 'Not Found', 'wms' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'wms' ),
		);
// Set other options for Custom Post Type

		$args = array(
			'label'               => __( 'Submissions', 'wms' ),
			'description'         => __( 'Woocommerce marketing submissions', 'wms' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false,
				// Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
			),
			'show_in_rest'        => true,
		);

		// Registering your Custom Post Type
		register_post_type( 'wms-entries', $args );
	}
}