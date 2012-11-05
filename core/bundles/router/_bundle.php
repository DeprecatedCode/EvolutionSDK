<?php

/**
 * EvolutionSDK Router Bundle
 *
 * @author Nate Ferrero
 */
namespace e\Router;
use Exception;
use e;

class Bundle {

	private $_root;

	private $_routes = array();

	public function __construct() {
		$root = e::$config->router->root;
		if(is_null($root)) {
			throw new Exception('No root folder found for site ' . E_SITE);
		}
		$this->_root = str_replace('~', E_HOME, $root);
		if(substr($this->_root, strlen($this->_root) - 1, 1) != '/') {
			$this->_root .= '/';
		}
		$this->_root .= '_root.php';
	}

	public function route() {
		/**
		 * Attach routes
		 */
		require_once($this->_root);

		/**
		 * Iterate routes
		 */
		foreach(array_keys($this->_routes) as $path) {

			if(e::$request->matches($path)) {

				/**
				 * Check method
				 */
				if(!isset($this->_routes[$path][e::$request->method])) {
					throw new Exception('Method not allowed');
				}

				$handler = $this->_routes[$path][e::$request->method];

				/**
				 * Functions
				 */
				if(is_callable($handler)) {
					echo call_user_func_array($handler, e::$request->args);
				}

				/**
				 * Method Views
				 */
				else if(is_object($handler)) {
					$m = strtolower(e::$request->method);
					if(!method_exists($handler, $m)) {
						throw new Exception('Method not allowed');
					}
					echo call_user_func_array(array($handler, $m), e::$request->args);
				}

				/**
				 * Literals
				 */
				else {
					echo $handler;
				}

				break;
			}

		}
	}

	/**
	 * Attach a handler to a path
	 * Note: If the handler is an array, all child paths will also be attached
	 */
	public function attach($path, $handler) {

		/**
		 * Handle multiple sections
		 */
		if(is_array($handler)) {
			foreach($handler as $sub_path => $sub_handler) {
				$this->attach($path . $sub_path, $sub_handler);
			}
			return;
		}

		/**
		 * Create route in array
		 */
		if(!isset($this->_routes[$path])) {
			$this->_routes[$path] = array();
		}

		/**
		 * Allow for class views
		 */
		if(is_object($handler) && !is_callable($handler)) {
			$methods = array();
			foreach(explode(' ', 'GET PUT POST DELETE') as $method) {
				if(method_exists($handler, strtolower($method))) {
					$methods[] = $method;
				}
			}
		}

		/**
		 * Non-object views only support GET
		 */
		else {
			$methods = ['GET'];
		}

		/**
		 * Attach methods
		 */
		foreach($methods as $method) {
			$this->_routes[$path][$method] = $handler;
		}
	}

}
