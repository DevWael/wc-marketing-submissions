<?php

namespace WMS\Modules;

class Submissions {

	/**
	 * @var integer
	 */
	protected $user_id;
	/**
	 * @var string
	 */
	private $first_name;
	/**
	 * @var string
	 */
	private $email;
	/**
	 * @var string
	 */
	private $last_name;

	public function __construct( $email, $user_id = null, $first_name = null, $last_name = null ) {
		$this->email = $email;
		if ( $user_id ) {
			$this->user_id = $user_id;
		}
		if ( $first_name ) {
			$this->first_name = $first_name;
		}
		if ( $last_name ) {
			$this->last_name = $last_name;
		}
	}

	public function add_submission() {
		$params = array(
			'post_type'   => 'wms-entries',
			'post_status' => 'publish',
			'post_title'  => $this->user_id !== null ? '#' . $this->user_id : 'anonymous',
			'meta_input'  => array(
				'wms_user_email' => $this->email,
			)
		);

		if ( $this->first_name ) {
			$params['meta_input']['wms_user_first_name'] = $this->first_name;
		}

		if ( $this->last_name ) {
			$params['meta_input']['wms_user_last_name'] = $this->last_name;
		}

		if ( $this->user_id ) {
			$params['meta_input']['wms_user_id'] = $this->user_id;
		}

		return \wp_insert_post( apply_filters( 'wms_add_submission_params', $params, $this->email ), true );
	}

	public function get_submissions_ids() {
		$args = apply_filters( 'wms_submission_search_params', [
			'post_type'      => 'wms-entries',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'meta_key'       => 'wms_user_email',
			'meta_value'     => $this->email
		], $this->email );

		return \get_posts( $args );
	}

	public function delete_submission() {
		$submissions = $this->get_submissions_ids();
		if ( $submissions ) {
			foreach ( $submissions as $submission_id ) {
				wp_delete_post( $submission_id, true );
			}
		}
	}

	public function is_submission_exist() {
		return count( $this->get_submissions_ids() ) > 0;
	}
}