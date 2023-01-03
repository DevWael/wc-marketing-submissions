<?php

namespace WMS\Modules;
class Post_Type {

	public function setup_hooks() {
		add_action( 'init', [ $this, 'cpt' ] );
		add_filter( 'add_meta_boxes', [ $this, 'metabox' ] );
		add_filter( 'manage_wms-entries_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_wms-entries_posts_custom_column', [ $this, 'user_id_column' ], 10, 2 );
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
			'menu_icon'           => 'dashicons-feedback',
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
//			'capabilities'        => array(
//				'create_posts' => false,
//				// Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
//			),
			'show_in_rest'        => true,
		);

		$args = apply_filters( 'wms_post_type_args', $args );
		// Registering your Custom Post Type
		register_post_type( 'wms-entries', $args );
	}

	public function metabox() {
		add_meta_box( 'wms', __( 'Submission Details', 'wms' ), [ $this, 'metabox_content' ], 'wms-entries' );
	}

	public function metabox_content( $post ) {
		$user_id = get_post_meta( $post->ID, 'wms_user_id', true );
		$user    = get_user_by( 'ID', $user_id );
		do_action( 'wms_before_metabox_content', $post, $post->ID );
		?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="user_id"><?php _e( 'User ID', 'wms' ); ?></label></th>
                <td><input readonly name="user_id" type="text" id="user_id"
                           value="<?php echo esc_attr( $user_id ?: '---' ); ?>"
                           class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="username"><?php _e( 'Username', 'wms' ); ?></label></th>
                <td><input readonly name="username" type="text" id="username"
                           value="<?php echo $user ? esc_html( $user->user_login ) : '---'; ?>"
                           class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="fullname"><?php _e( 'Full name', 'wms' ); ?></label></th>
                <td><input readonly name="fullname" type="text" id="fullname" value="<?php
					echo esc_attr( get_post_meta( $post->ID, 'wms_user_first_name', true ) ) . ' ' .
					     esc_attr( get_post_meta( $post->ID, 'wms_user_last_name', true ) ); ?>"
                           class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="email_address"><?php _e( 'Email address', 'wms' ); ?></label></th>
                <td><input readonly name="email_address" type="text" id="email_address"
                           value="<?php echo esc_attr( get_post_meta( $post->ID, 'wms_user_email', true ) ); ?>"
                           class="regular-text"></td>
            </tr>
        </table>
		<?php
		do_action( 'wms_after_metabox_content', $post, $post->ID );
	}

	public function columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['user_id']        = __( 'User ID', 'wms' );
		$columns['username']       = __( 'Username', 'wms' );
		$columns['user_full_name'] = __( 'Full name', 'wms' );
		$columns['email']          = __( 'Email', 'wms' );
		$columns['date']           = $date;

		return $columns;
	}

	public function user_id_column( $column, $post_id ) {
		$user_id = get_post_meta( $post_id, 'wms_user_id', true );
		$user    = get_user_by( 'ID', $user_id );
		if ( $column === 'user_id' ) {
			echo esc_html( $user_id ?: '---' );
		}

		if ( $column === 'username' ) {
			echo $user ? esc_html( $user->user_login ) : '---';
		}

		if ( $column === 'user_full_name' ) {
			echo esc_html( get_post_meta( $post_id, 'wms_user_first_name', true ) ) . ' ' . esc_html( get_post_meta( $post_id, 'wms_user_last_name', true ) );
		}

		if ( $column === 'email' ) {
			echo esc_html( get_post_meta( $post_id, 'wms_user_email', true ) );
		}
	}
}