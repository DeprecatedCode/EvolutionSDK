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

	public function __construct() {
		$root = e::$config->router->root;
		if(is_null($root)) {
			throw new Exception('No root folder found for site ' . E_SITE);
		}
		$this->_root = str_replace('~', E_HOME, $root);
	}

	public function route() {
		die($this->_root);
	}

}