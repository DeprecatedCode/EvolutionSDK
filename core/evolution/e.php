<?php

/**
 * Unfortunate hack
 */
require_once('hacks/static_bundle_vars.php');
_static_bundle_vars();


/**
 * EvolutionSDK Framework
 */
class e extends _e_hack_static_bundle_vars {

	/**
	 * Register all bundles
	 */
	public static function register_bundles($path) {
		foreach(explode(';', $path) as $dir) {
			if(strlen($dir) < 3) {
				continue;
			}
			foreach(glob($dir . '/*', GLOB_ONLYDIR) as $bundle) {
				$name = strtolower(basename($bundle));
				self::$$name = new self($name, $bundle . '/_bundle.php');
			}
		}
	}

	private $_name;
	private $_file;
	private $_instance = null;

	/**
	 * Construct a bundle proxy
	 */
	public function __construct($name, $file) {
		$this->_name = $name;
		$this->_file = $file;
	}

	/**
	 * Bundles are lazy-loaded as needed
	 */
	private function _get_instance() {
		if(is_null($this->_instance)) {
			require_once($this->_file);
			$class = '\\e\\' . ucfirst($this->_name) . '\\Bundle';
			$this->_instance = new $class;
		}
		return $this->_instance;
	}

	/**
	 * Call a bundle method
	 */
	public function __call($method, $args) {
		return call_user_func_array(array($this->_get_instance(), $method), $args);
	}

	/**
	 * Get a bundle property
	 */
	public function __get($property) {
		return $this->_get_instance()->$property;
	}

	/**
	 * Set a bundle property
	 */
	public function __set($property, $value) {
		return $this->_get_instance()->$property = $value;
	}
}