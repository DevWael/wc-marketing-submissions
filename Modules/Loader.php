<?php

namespace WMS\Modules;
class Loader {
	protected $modules = array();

	public function load_modules() {
		$this->modules = apply_filters( 'wms_plugin_modules', array(
			'post_type' => \WMS\Modules\Post_Type::class,
			'checkout'  => \WMS\Modules\Checkout::class,
		) );
	}

	public function run() {
		foreach ( $this->modules as $module_key => $module ) {
			$module_object = new $module();
			$module_object->setup_hooks();
		}
	}
}