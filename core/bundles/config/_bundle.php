<?php

/**
 * EvolutionSDK Config Bundle
 *
 * @author Nate Ferrero
 */
namespace e\Config;
use Exception;
use e;

class Bundle {

	private $_dir = null;

	public function __construct() {
		foreach(explode(';', E_DATA) as $dir) {
			if(is_dir($dir) && is_writable($dir)) {
				$this->_dir = $dir;
				break;
			}
		}
		if(!is_null($this->_dir) && defined('E_SITE')) {
			$this->_dir .= '/' . E_SITE;
			if(!is_dir($this->_dir)) {
				throw new Exception("Site " . E_SITE .
					" not in config dir " . dirname($this->_dir));
			}
		}
	}

	public function dir() {
		return $this->_dir;
	}

	public function sites() {
		$sites = array();
		if(defined('E_SITE')) {
			return $sites;
		}
		foreach(glob($this->_dir . '/*', GLOB_ONLYDIR) as $dir) {
			$sites[] = basename($dir);
		}
		return $sites;
	}

	public function site($id, $file) {
		if(is_null($this->_dir)) {
			throw new Exception("Unable to locate config directory");
		}
		return new Wrapper($this->_dir . '/' . $id . '/' . $file . '.json');
	}

	public function __get($file) {
		if(is_null($this->_dir)) {
			throw new Exception("Unable to locate config directory");
		}

		return new Wrapper($this->_dir . '/' . $file . '.json');
	}

}
