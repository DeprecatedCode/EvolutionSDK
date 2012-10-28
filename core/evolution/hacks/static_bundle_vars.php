<?php

/**
 * This is a hack for PHP 5.3 compatibility.
 *
 * Generated code is of the form:
 *
 * class _e_hack_static_bundle_vars {
 *     public static $bundle_name_1;
 *     public static $bundle_name_2;
 *     public static $bundle_name_3; # Etcetera
 * }
 *
 * @author Nate Ferrero
 */
function _static_bundle_vars() {
	static $ran = false;
	if($ran == true) {
		throw new Exception("Static Bundle Vars Hack called more than once");
	}
	$ran = true;
	$code = 'class _e_hack_static_bundle_vars {';
	$path = realpath(__DIR__ . '/../../bundles') . ';' . getenv('E_BUNDLES');
	$bundles = array();
	foreach(explode(';', $path) as $dir) {
		if(strlen($dir) < 3) {
			continue;
		}
		foreach(glob($dir . '/*', GLOB_ONLYDIR) as $bundle) {
			$bundles[strtolower(basename($bundle))] = true;
		}
	}
	$code .= "\n    public static $" . implode(";\n    public static $", array_keys($bundles));
	eval($code . ";\n}");
}