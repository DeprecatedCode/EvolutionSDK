<?php

/**
 * Request Bundle
 * 
 * @author Nate Ferrero
 */
namespace e\Request;
use e;

class Bundle {

	/**
	 * Request vars
	 */
	private $_path;
	private $_method;
	private $_segments;
	private $_domain;
	private $_args = array();

	/**
	 * Provide a slight additional layer of security by using getters
	 * i.e. direct setting is not possible, yet getting is.
	 */
	public function __get($var) {
		return $this->{"_$var"};
	}

	/**
	 * Initialize the request bundle
	 */
	public function __construct() {
		$this->_domain = strtolower($_SERVER['HTTP_HOST']);
		$this->_path = $_SERVER['REDIRECT_URL'];

		/**
		 * Remove trailing slash
		 */
		$this->_path = rtrim($this->_path, '/');

		$this->_method = $_SERVER['REQUEST_METHOD'];
		$this->_segments = explode('/', $this->_path);
		array_shift($this->_segments);
	}

	/**
	 * Get the request depth
	 */
	public function depth() {
		return count($this->_segments);
	}

	/**
	 * Get a request segment
	 */
	public function get($pos) {
		return isset($this->_segments[$pos]) ?
			$this->_segments[$pos] : null;
	}

	/**
	 * Shift segments
	 */
	public function shift($by = 1) {
		$trim = array_splice($this->_segments, 0, $by);
		$this->_path = '/' . implode('/', $this->_segments);
		return $trim;
	}

	/**
	 * Check if the request path matches the given arguments
	 */
	public function match() {
		foreach(func_get_args() as $i => $segment) {
			if(!isset($this->_segments[$i])) {
				return is_null($segment);
			}
			if($this->_segments[$i] !== $segment) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if request matches path
	 */
	public function matches($path) {
		$path = str_replace('/', '\\/', $path);
		$path = str_replace('.', '\\.', $path);
		$path = str_replace('*', '(.*)', $path);
		$path = preg_replace('/(<.+?>)/', '([^\/]*)', $path);
		$match = preg_match("/^$path$/", $this->_path, $args);

		if(isset($args[0])) {
			array_shift($args);
			$this->_args = $args;
		}

		return $match === 1;
	}

	/**
	 * Return a slice of segments
	 */
	public function slice($offset = 0, $length = null) {
		return array_slice($this->_segments, $offset, $length);
	}
}
