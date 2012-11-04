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
function _e_hack_static_bundle_vars($path) {
	static $ran = false;
	if($ran == true) {
		throw new Exception("Static Bundle Vars Hack called more than once");
	}
	$ran = true;
	$code = "class _e_hack_static_bundle_vars {\n    public static $";
	$bundles = array();
	foreach($path as $dir) {
		if(strlen($dir) < 3) {
			continue;
		}
		foreach(glob($dir . '/*/_bundle.php') as $bundle) {
			$bundles[strtolower(basename(dirname($bundle)))] = true;
		}
	}
	$code .= implode(";\n    public static $", array_keys($bundles));
	eval($code . ";\n}");
}