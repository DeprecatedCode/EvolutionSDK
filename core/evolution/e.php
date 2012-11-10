<?php

/**
 * Ensure output is controlled
 */
ob_start();

/**
 * Ensure proper PHP version is installed
 */
if(PHP_VERSION_ID < 50304) {
	die('PHP version 5.3.4 or greater is required to use EvolutionSDK, you have PHP ' . PHP_VERSION);
}

/**
 * Include core bundles
 */
define('E_BUNDLES_ALL',
	realpath(__DIR__ . '/../bundles') . ';' . E_BUNDLES);

/**
 * Unfortunate hack
 */
require_once('hacks/static_bundle_vars.php');
_e_hack_static_bundle_vars(explode(';', E_BUNDLES_ALL));


/**
 * EvolutionSDK Framework
 *
 * @author Nate Ferrero
 */
class e extends _e_hack_static_bundle_vars {

	public static $_;
	public static $_e_bundle_paths = array();
	public static $_e_bundles = array();

	private $_name;
	private $_file;
	private $_instance = null;

	/**
	 * Register all bundles
	 */
	public static function _e_register_bundles($path) {
		foreach(explode(';', $path) as $dir) {
			if(strlen($dir) < 3) {
				continue;
			}
			$dir = realpath($dir);
			if(!in_array($dir, self::$_e_bundle_paths)) {
				self::$_e_bundle_paths[] = $dir;
			}
			foreach(glob($dir . '/*/_bundle.php') as $bundle) {
				$name = strtolower(basename(dirname($bundle)));
				self::$_e_bundles[$name] = $bundle;
				self::$$name = new self($name, $bundle);
			}
		}
	}

	/**
	 * Initialize
	 */
	public static function _e_init() {
		self::$_ = new self();
	}

	/**
	 * Autoload a class
	 */
	public function _e_autoload($class) {
		$class = explode('\\', $class);
		$e = array_shift($class);
		$bundle = strtolower(array_shift($class));
		if($e != 'e' || !property_exists($this, $bundle)) {
			return;
		}
		$class = array_map("strtolower", $class);
		self::$$bundle->_e_load($class);
	}

	/**
	 * Load a bundle class
	 */
	public function _e_load($class) {
		array_unshift($class, dirname($this->_file));
		$file = implode(DIRECTORY_SEPARATOR, $class) . '.php';
		require_once($file);
	}

	/**
	 * Construct a bundle proxy
	 */
	public function __construct($name = null, $file = null) {
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

/**
 * Initialize Framework
 */
e::_e_init();

/**
 * Register autoloader
 */
spl_autoload_register(array(e::$_, '_e_autoload'), true, true);

/**
 * Register bundles
 */
e::_e_register_bundles(E_BUNDLES_ALL);
