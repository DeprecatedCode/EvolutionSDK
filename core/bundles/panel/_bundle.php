<?php

/**
 * EvolutionSDK Panel Bundle
 * 
 * @author Nate Ferrero
 */
namespace e\Panel;
use e;

class Bundle {
	
	public function display() {

		/**
		 * Handle static files
		 */
		if(e::$request->match('static')) {
			if(strpos(e::$request->path, '.css') > 0) {
				header('Content-Type: text/css');
			}
			else if(strpos(e::$request->path, '.js') > 0) {
				header('Content-Type: text/javascript');
			} else {
				die('Invalid');
			}
			readfile(__DIR__ . '/static/' . implode('/', e::$request->slice(1)));
		}

		/**
		 * Handle main page
		 */
		else if(e::$request->match(null)) {
			readfile(__DIR__ . '/static/index.html');
		}

		/**
		 * Handle API calls
		 */
		else if(e::$request->match('api')) {
			echo json_encode(
				(new API())->_handle(e::$request->slice(1))
			);
		}

		/**
		 * Handle Error page
		 */
		else {
			echo preg_replace('/error-off/', 'error-on', file_get_contents(__DIR__ . '/static/index.html'));
		}
	}

}
