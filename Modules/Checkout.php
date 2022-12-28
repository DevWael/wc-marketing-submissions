<?php

namespace WMS\Modules;

class Checkout {
	public function setup_hooks() {
		add_action( 'woocommerce_review_order_before_submit', [ $this, 'checkout' ] );
		add_action( 'woocommerce_checkout_process', [ $this, 'process' ] );
	}

	public function checkout() {
		woocommerce_form_field( 'wms-opt-in', array(
			'type'        => 'checkbox',
			'class'       => array( 'form-row privacy' ),
			'label_class' => array( 'woocommerce-form__label woocommerce-form__label-for-checkbox checkbox' ),
			'input_class' => array( 'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox' ),
			'required' => false,
			'label'       => 'Marketing Opt-In',
		) );
	}

	public function process() {
		//todo: to be implemented
	}
}