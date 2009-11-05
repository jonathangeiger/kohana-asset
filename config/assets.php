<?php defined('SYSPATH') or die('No direct script access.');

return array(
	
	/**
	 * Javascript directory to prefix to all generated 
	 * files (except cache files...see below).
	 * 
	 * url::base() is prepended to this automatically.
	 */
	'js_dir' => 'javascripts/',
	
	/**
	 * Cache directory for javascripts
	 * 
	 * ex. javascripts/cache-	writes to	javascripts/cache-212985812.js
	 * ex. javascripts/cache/	writes to	javascripts/cache/212985812.js
	 * 
	 * url::base() is prepended to this automatically.
	 */ 
	'js_cache_dir' => 'javascripts/cache/',

	/**
	 * Whether or not to cache. Cache expiration is handled 
	 * automatically, based on whether or not a file has changed.
	 * 
	 * Garbage collection, is not currently handled by the system
	 */
	'cache'	=> (Kohana::$environment == 'production') ? TRUE : FALSE
	
);
