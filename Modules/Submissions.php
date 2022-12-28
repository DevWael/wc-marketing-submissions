<?php

namespace WMS\Modules;

class Submissions {

	protected $user_id;

	public function __construct( $user_id ) {
		$this->user_id = $user_id;
	}

	public function add_submission() {
		return \wp_insert_post( apply_filters( 'wms_add_submission_params', [
			'post_type'   => 'wms-entries',
			'post_status' => 'publish',
			'post_title'  => '#' . $this->user_id,
			'meta_input'  => array(
				'wms_user_id' => $this->user_id,
			)
		], $this->user_id ), true );
	}

	public function get_submissions_ids() {
		$args = apply_filters( 'wms_submission_search_params', [
			'post_type'      => 'wms-entries',
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'meta_key'       => 'wms_user_id',
			'meta_value'     => $this->user_id
		], $this->user_id );

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