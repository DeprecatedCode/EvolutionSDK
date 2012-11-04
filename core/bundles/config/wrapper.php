<?php

/**
 * EvolutionSDK Config Bundle
 *
 * @author Nate Ferrero
 */
namespace e\Config;
use Exception;
use e;

class Wrapper {

	private $_data;

	private $_file;

	public function __construct($file) {

		$this->_file = $file;

		if(!is_file($file)) {
			file_put_contents($file, "{\n\n}\n");
			chmod($file, 0666);
			$this->_data = array();
		}

		else {
			$this->_data = json_decode(file_get_contents($file), true);
			$err = json_last_error();
			if($err != null) {
				throw new Exception("Failed decoding JSON in " . $this->_file);
			}
			if(!is_array($this->_data)) {
				$this->_data = array();
			}
		}

	}

	public function __get($name) {
		return isset($this->_data[$name]) ?
			$this->_data[$name] : null;
	}

	public function __call($name, $args) {
		return isset($this->_data[$name]) ?
			$this->_data[$name] : array_shift($args);
	}

	public function _array() {
		return $this->_data;
	}

	public function _write($data) {
		$dir = dirname($this->_file);
		if(!is_dir($dir)) {
			mkdir($dir, 0777);
			chmod($dir, 0777);
			if(!is_dir($dir)) {
				throw new Exception("Unable to create config dir " . $dir);
			}
		}

		file_put_contents($this->_file, e::$json->encode($data));
		chmod($this->_file, 0666);
	}

}
