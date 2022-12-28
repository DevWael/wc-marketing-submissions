<?php

namespace WMS\Modules;

class Checkout {

	/**
	 * @var int
	 */
	private $user_id;

	public function __construct() {
		$this->user_id = get_current_user_id();
	}

	public function setup_hooks() {
		add_action( 'woocommerce_review_order_before_submit', [ $this, 'checkout' ] );
		add_action( 'woocommerce_checkout_process', [ $this, 'process' ] );
	}

	public function checkout() {
		woocommerce_form_field( 'wms-opt-in', array(
			'type'        => 'checkbox',
			'class'       => array( 'form-row marketing-opt-in' ),
			'label_class' => array( 'woocommerce-form__label woocommerce-form__label-for-checkbox checkbox' ),
			'input_class' => array( 'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox' ),
			'required'    => false,
			'label'       => 'Marketing Opt-In',
		) );
	}

	public function process() {
		$user_id = get_current_user_id();
		if ( $user_id ) {
			$user       = get_user_by( 'ID', $user_id );
			$email      = sanitize_email( $user->user_email );
			$first_name = sanitize_text_field( $user->first_name );
			$last_name  = sanitize_text_field( $user->last_name );
		} else {
			if ( isset( $_POST['billing_email'] ) && is_email( $_POST['billing_email'] ) ) {
				$email      = sanitize_email( $_POST['billing_email'] );
				$first_name = isset( $_POST['billing_first_name'] ) ? sanitize_text_field( $_POST['billing_first_name'] ) : '';
				$last_name  = isset( $_POST['billing_first_name'] ) ? sanitize_text_field( $_POST['billing_last_name'] ) : '';
				$user_id    = null;
			} else {
				return;
			}
		}

		$submission = new Submissions( $email, $user_id, $first_name, $last_name );

		if ( isset( $_POST['wms-opt-in'] ) && $_POST['wms-opt-in'] === '1' ) {
			if ( ! $submission->is_submission_exist() ) {
				$submission->add_submission();
			}
		} else {
			if ( $submission->is_submission_exist() ) {
				$submission->delete_submission();
			}
		}
	}
}