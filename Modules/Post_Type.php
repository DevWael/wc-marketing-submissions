<?php

namespace WMS\Modules;
class Post_Type {

	public function setup_hooks() {
		add_action( 'init', [ $this, 'cpt' ] );
		add_action( 'add_meta_boxes', [ $this, 'metabox' ] );
		add_action( 'save_post_wms-entries', [ $this, 'save_data' ], 10, 2 );
		add_filter( 'manage_wms-entries_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_wms-entries_posts_custom_column', [ $this, 'user_id_column' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'settings_fields' ] );
		add_action( 'admin_menu', [ $this, 'options_page' ] );
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
			'capabilities'        => array(
				'create_posts' => 'do_not_allow', // Removes support for the "Add New" function, including Super Admin's
			),
			'map_meta_cap'        => true,
			'show_in_rest'        => true,
		);

		$args = apply_filters( 'wms_post_type_args', $args );
		// Registering your Custom Post Type
		register_post_type( 'wms-entries', $args );
	}

	public function metabox() {
		add_meta_box( 'wms_metabox', __( 'Submission Details', 'wms' ), [ $this, 'metabox_content' ], 'wms-entries' );
	}

	public function metabox_content( $post ) {
		$user_id = get_post_meta( $post->ID, 'wms_user_id', true );
		$user    = get_user_by( 'ID', $user_id );
		do_action( 'wms_before_metabox_content', $post, $post->ID );
		wp_nonce_field( 'wms-nonce', '_wms' );
		?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="user_id"><?php _e( 'User ID', 'wms' ); ?></label></th>
                <td><input name="user_id" type="text" id="user_id"
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
                <td><input name="email_address" type="text" id="email_address"
                           value="<?php echo esc_attr( get_post_meta( $post->ID, 'wms_user_email', true ) ); ?>"
                           class="regular-text"></td>
            </tr>
        </table>
		<?php
		do_action( 'wms_after_metabox_content', $post, $post->ID );
	}

	public function save_data( $post_id, $post ) {
		if ( isset( $_REQUEST['_wms'] ) && wp_verify_nonce( $_REQUEST['_wms'], 'wms-nonce' ) ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( $parent_id = wp_is_post_revision( $post_id ) ) {
				$post_id = $parent_id;
			}

			if ( isset( $_POST['user_id'] ) && is_numeric( $_POST['user_id'] ) ) {
				update_post_meta( $post_id, 'wms_user_id', $_POST['user_id'] );
			}

			if ( isset( $_POST['email_address'] ) && is_email( $_POST['email_address'] ) ) {
				update_post_meta( $post_id, 'wms_user_email', $_POST['email_address'] );
			}
		}
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

	public function options_page() {
		add_submenu_page( 'edit.php?post_type=wms-entries', 'Settings',
			'Settings', 'manage_options', 'wms-settings', [ $this, 'options_page_content' ] );
	}

	public function options_page_content() {
		?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title() ?></h1>
            <form method="post" action="options.php">
				<?php
				settings_fields( 'wms_settings' ); // settings group name
				do_settings_sections( 'wms-settings' ); // just a page slug
				submit_button(); // "Save Changes" button
				?>
            </form>
        </div>
		<?php
	}

	public function settings_fields() {
		$page_slug    = 'wms-settings';
		$option_group = 'wms_settings';

		// 1. create section
		add_settings_section(
			'wms_settings_id', // section ID
			'', // title (optional)
			'', // callback function to display the section (optional)
			$page_slug
		);

		// 2. register fields
		register_setting( $option_group, 'wms_enable', array(
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_checkbox' ],
			'default'           => 'yes',
		) );
		register_setting( $option_group, 'wms_label', array(
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_textarea' ],
			'default'           => __( 'I would like to sign up to receive email updates from Deedy.', 'wms' ),
		) );

		// 3. add fields
		add_settings_field(
			'enable_wms',
			__( 'Enable Woocommerce email submissions', 'wms' ),
			[ $this, 'checkbox_field' ], // function to print the field
			$page_slug,
			'wms_settings_id' // section ID
		);

		add_settings_field(
			'wms_label',
			__( 'Checkbox label', 'wms' ),
			[ $this, 'label_field' ],
			$page_slug,
			'wms_settings_id',
			array(
				'label_for' => 'wms_label',
				'class'     => 'label', // for <tr> element
				'name'      => 'wms_label' // pass any custom parameters
			)
		);
	}

	public function checkbox_field( $args ) {
		$value = get_option( 'wms_enable' );
		?>
        <label>
            <input type="checkbox" name="wms_enable" <?php checked( $value, 'yes' ) ?> /> <?php _e( 'Yes', 'wms' ); ?>
        </label>
		<?php
	}

	public function label_field( $args ) {
		printf(
			'<textarea rows="4" cols="50" id="%s" name="%s">%s</textarea>',
			$args['name'],
			$args['name'],
			get_option( $args['name'], __( 'I would like to sign up to receive email updates from Deedy.', 'wms' ) )
		);
	}

	public function sanitize_checkbox( $value ) {
		return 'on' === $value ? 'yes' : 'no';
	}

	public function sanitize_textarea( $value ) {
		return wp_kses( $value, array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
			)
		) );
	}
}