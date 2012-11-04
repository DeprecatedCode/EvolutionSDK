<?php

/**
 * EvolutionSDK Initialization
 *
 * This file is hit on every request.
 *
 * Consistency, cleanliness, and security:
 *   PHP >= 5.3.4 REQUIRED
 *   No variables are defined.
 *   No functions are defined, except as in ./hacks and _e_conf.
 *   Two constants are defined, E_HOME and E_BUNDLES
 *   One class is defined (e).
 *   Only requests to evolution.dev are processed here.
 *   Timezone is set to UTC and should not be changed.
 *
 * To use EvolutionSDK in a custom configuration without this file,
 * define the following constants, then include EvolutionSDK.
 *   E_HOME    - "Home" folder.
 *   E_DATA    - Semicolon separated folders to search for config in.
 *   E_BUNDLES - Semicolon separated folders to load bundles from.
 *
 * @author Nate Ferrero
 */

/**
 * Sample init file to copy and paste when deploying a site:

<?php

date_default_timezone_set('UTC');
define('E_HOME', '/anything/user');
define('E_DATA', '/anything/evolution;/another/evolution');
define('E_BUNDLES', '/my/evo/bundles;/another/bundles');
define('E_SITE', 'my_site_slug');

require('/path_to_evolutionSDK/evolution/e.php');

e::$router->route();

?>

 */

/**
 * Set UTC timezone
 */
date_default_timezone_set('UTC');

/**
 * Define home dir
 */
define('E_HOME', DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR,
		array_slice(explode(DIRECTORY_SEPARATOR, __DIR__), 1, 2)));

/**
 * Define data storage path
 */
define('E_DATA', str_replace('~', E_HOME,
	'~/.evolution;~/.local/etc/evolution;/etc/evolution'));

/**
 * Load general configuration
 */
function _e_conf() {
	$paths = array();
	foreach(explode(';', E_DATA) as $dir) {
		$file = $dir . '/bundle_paths.txt';
		if(is_file($file)) {
			$paths = array_merge($paths,
				array_map(function($x) {
					return realpath(trim(
						str_replace('~', E_HOME, $x)
					));
				}, file($file)
			));
		}
	}
	define('E_BUNDLES', implode(';', $paths));
}
_e_conf();

/**
 * Load the core framework
 */
require_once('e.php');

/**
 * Handle special cases
 */
switch(e::$request->path) {
	case '/status':
		require_once('utils/status.php');
		exit;
}

/**
 * Handle site previews
 */
if(e::$request->depth() >= 2 && e::$request->match('preview')) {
	define('E_SITE', e::$request->get(1));
	e::$request->shift(2);
	e::$router->route();
}

/**
 * Display panel
 */
else {
	e::$panel->display();
}
