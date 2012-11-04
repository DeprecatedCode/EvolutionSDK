<?php

/**
 * EvolutionSDK Panel Bundle API
 * 
 * @author Nate Ferrero
 */
namespace e\Panel;
use e;

class API {

	public function __construct() {
		$dir = e::$config->dir();
		if(is_dir($dir) && is_writable($dir)) { ; } else {
			echo json_encode(array('error' => "EvolutionSDK Data Folder Not Accessible\n\nPlease create a folder and ensure that PHP has write access at one of the following locations: \n\n" . str_replace(';', "\n", E_DATA)));
			die;
		}
	}

	# API - Authentication
	public function authentication() {
		return array('authenticated' => true);  # TODO
	}

	# API - Sites
	public function sites($path) {
		$data = array();
		foreach(e::$config->sites() as $id) {
			$info = e::$config->site($id, 'info');
			$data[] = $this->_filter($info->_array(), $id);
		}
		return $data;
	}

	# API - Add Site
	public function add_site($path) {
		$sites = e::$config->sites();
		while(!isset($id) || in_array($id, $sites)) {
			$id = $this->_id();
		}
		$data = $this->_filter($_POST);
		$this->_save($id, $data);
		return true;
	}

	# API - Info
	public function info($path) {
		return array(
			'bundle_paths' => e::$_e_bundle_paths,
			'bundles' => e::$_e_bundles
		);
	}

	# Meta

	private function _id() {
		return substr(
			base_convert(md5(
				uniqid('', true)
			), 16, 36),
		0, 4);
	}

	private function _filter($data, $id = null) {
		$ret = array(
			'name' => $data['name']
		);
		if(!is_null($id)) {
			$ret['id'] = $id;
		}
		return $ret;
	}

	private function _save($id, $data) {
		e::$config->site($id, 'info')->_write($data);
	}

	public function _handle($path) {
		if(count($path) == 0) {
			return array('error' => 'No API Method Specified');
		}
		$method = array_shift($path);
		if(!method_exists($this, $method) || $method[0] == '_') {
			return array('error' => 'Invalid API Method Specified');
		}
		return call_user_func(array($this, $method), $path);
	}

}
