<?php defined('SYSPATH') or die('No direct script access.');

return array(
	
	/**
	 * The location on the filesystem to the paths below. 
	 * 
	 * This is assumed to include url::base() and end with a trailing slash.
	 */
	'root' => DOCROOT,
	
	/**
	 * Config for calls to asset::javascripts()
	 */
	'javascripts' => array(
		
		/**
		 * Javascript directory to prefix to all generated 
		 * files (except cache files...see below).
		 * 
		 * url::base() is prepended to this automatically.
		 */
		'prefix' => 'javascripts/',
		
		/**
		 * Should be self-explanatory
		 */
		'extension' => '.js',

		/**
		 * Whether or not to cache. Cache expiration is handled 
		 * automatically, based on whether or not a file has changed.
		 * 
		 * Garbage collection is not currently handled by the system
		 */
		'cache'	=> TRUE
	),
	
	/**
	 * Config for calls to asset::stylesheets()
	 */
	'stylesheets' => array(
		
		/**
		 * Javascript directory to prefix to all generated 
		 * files (except cache files...see below).
		 * 
		 * url::base() is prepended to this automatically.
		 */
		'prefix' => 'stylesheets/',
		
		/**
		 * Should be self-explanatory
		 */
		'extension' => '.css',

		/**
		 * Whether or not to cache. Cache expiration is handled 
		 * automatically, based on whether or not a file has changed.
		 * 
		 * Garbage collection is not currently handled by the system
		 */
		'cache'	=> TRUE
	),
	
	/**
	 * Config for cached files
	 */
	'cache' => array(
		/**
		 * Cache directory for all cached files. Note that javascript AND css
		 * files will be prefixed with this. 
		 * 
		 * This may seem a bit "unclean" but it's ultimately better: there's 
		 * only one directory to delete if you want to manually expire the 
		 * cache. 
		 * 
		 * Also, stylesheets won't have any problems with url() references
		 * if you keep it at the same level as your stylesheets directory.
		 * If cache files were cached in the stylesheets directory, it would 
		 * get all cluttered with source files and cached files.
		 * 
		 * url::base() is prepended to this automatically when the 
		 * script/link tag is generated.
		 */ 
		'prefix' => 'cache/',
		
		/**
		 * Note that this is slightly different from the actual file type extensions.
		 * Just use it if you want to append something to the cache filename.
		 */
		'extension' => NULL
	)
);
