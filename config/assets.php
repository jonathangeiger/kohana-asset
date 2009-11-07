<?php defined('SYSPATH') or die('No direct script access.');

return array(
	
	/**
	 * Config for calls to asset::javascripts()
	 */
	'javascripts' => array(
		
		/**
		 * The location on the filesystem to the paths below. 
		 * 
		 * This is assumed to include url::base() and end with a trailing slash.
		 */
		'root' => DOCROOT,
		
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
		'cache'	=> TRUE,
		
		/**
		 * Prefix for cached javascript files. url::base() is automatically 
		 * added to this in a <script> tag and 'root' is automatically 
		 * added when referencing it on the filesystem
		 */
		'cache_prefix' => 'cache/'
	),
	
	/**
	 * Config for calls to asset::stylesheets()
	 */
	'stylesheets' => array(
		
		/**
		 * The location on the filesystem to the paths below. 
		 * 
		 * This is assumed to include url::base() and end with a trailing slash.
		 */
		'root' => DOCROOT,
		
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
		'cache'	=> TRUE,
		
		/**
		 * Prefix for cached css files. url::base() is automatically 
		 * added to this in a <link> tag and 'root' is automatically 
		 * added when referencing it on the filesystem
		 */
		'cache_prefix' => 'cache/'
	)
);
