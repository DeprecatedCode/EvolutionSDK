<?php

/**
 * EvolutionSDK Initialization
 *
 * This file is hit on every request.
 *
 * Consistency, cleanliness, and security:
 *   PHP >= 5.3.4 REQUIRED
 *   No variables are defined.
 *   No functions are defined (except as in /hacks).
 *   One class is defined (e).
 *   Only requests to evolution.dev are processed here.
 *   Timezone is set to UTC and should not be changed.
 */
if(PHP_VERSION_ID < 50304) {
	die('PHP version 5.3.4 or greater is required to use EvolutionSDK');
}

/**
 * Set UTC timezone
 */
date_default_timezone_set('UTC');

/**
 * Load the core framework
 */
require_once('e.php');

/**
 * Register bundles
 */
e::register_bundles(realpath(__DIR__ . '/../bundles') . ';' . getenv('E_BUNDLES'));

/**
 * Handle special cases
 */
switch(e::$request->path) {
	case '/.evolution-status':
		require_once('utils/status.php');
		exit;
}

/**
 * Handle site previews
 */
if(e::$request->is_preview) {
	e::$preview->route();
}

/**
 * Handle EvolutionSDK Panel
 */
if(e::$request->domain == 'evolution.dev') {
	e::$panel->display();
}
