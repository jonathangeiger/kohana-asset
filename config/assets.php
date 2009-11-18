<?php defined('SYSPATH') or die('No direct script access.');

return array(
	
	/**
	 * Config for calls to asset::javascripts()
	 */
	'javascripts' => array(
		
		/**
		 * An array of hosts to be prepended to the path. A random one
		 * will be selected from the array for each request.
		 * 
		 * If this is empty it will not be used.
		 * 
		 * Kohana::$base_url will not be applied when using this.
		 */
		'hosts' => array('http://test.com/', 'http://test2.com/'),
		
		/**
		 * The location on the filesystem to the paths below. 
		 * 
		 * This is assumed to include Kohana::$base_url and end with a trailing slash.
		 */
		'root' => DOCROOT,
		
		/**
		 * Javascript directory to prefix to all generated 
		 * files (except cache files...see below).
		 * 
		 * Kohana::$base_url is prepended to this automatically.
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
		 * Prefix for cached javascript files. Kohana::$base_url is automatically 
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
		 * An array of hosts to be prepended to the path. A random one
		 * will be selected from the array for each request.
		 * 
		 * If this is empty it will not be used.
		 * 
		 * Kohana::$base_url will not be applied when using this.
		 */
		'hosts' => array(),
		
		/**
		 * The location on the filesystem to the paths below. 
		 * 
		 * This is assumed to include Kohana::$base_url and end with a trailing slash.
		 */
		'root' => DOCROOT,
		
		/**
		 * Javascript directory to prefix to all generated 
		 * files (except cache files...see below).
		 * 
		 * Kohana::$base_url is prepended to this automatically.
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
		 * Prefix for cached css files. Kohana::$base_url is automatically 
		 * added to this in a <link> tag and 'root' is automatically 
		 * added when referencing it on the filesystem
		 */
		'cache_prefix' => 'cache/'
	)
);
