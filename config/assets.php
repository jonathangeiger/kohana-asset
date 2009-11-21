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
		 * If the key of the particular item selected from the array is a string, the 
		 * key is assumed to be the host and the value is assumed to be a path specifying 
		 * a different root to prepend to the path for saving the cache file. This allows you 
		 * to save cache files in different places on the filesystem for different hosts.
		 * 
		 * If the key of the particular item selected is an integer, the value is assumed 
		 * to be the host and the root defaults to the value for the `root` config option.
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
		'cache_prefix' => 'cache/',
		
		/**
		 * The compressor to use or FALSE to disable compression. 
		 * 
		 * 'compressor' => 'yui' // YUI Compressor
		 */
		'compressor' => 'yui',
		
		/**
		 * Options to pass to the compressor. For YUI Compressor, a string in the 
		 * form of shell args is what's supported. 
		 * 
		 * e.g. 'compressor_options' => '--nomunge --preserve-semi'
		 */
		'compressor_options' => NULL,
	),
	
	/**
	 * Config for calls to asset::stylesheets()
	 */
	'stylesheets' => array(
		
		/**
		 * An array of hosts to be prepended to the path. A random one
		 * will be selected from the array for each request.
		 * 
		 * If the key of the particular item selected from the array is a string, the 
		 * key is assumed to be the host and the value is assumed to be a path specifying 
		 * a different root to prepend to the path for saving the cache file. This allows you 
		 * to save cache files in different places on the filesystem for different hosts.
		 * 
		 * If the key of the particular item selected is an integer, the value is assumed 
		 * to be the host and the root defaults to the value for the `root` config option.
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
		'cache_prefix' => 'cache/',
		
		/**
		 * The compressor to use or FALSE to disable compression. 
		 * 
		 * 'compressor' => 'yui' // YUI Compressor
		 */
		'compressor' => 'yui',

		/**
		 * Options to pass to the compressor. For YUI Compressor, a string in the 
		 * form of shell args is what's supported. 
		 * 
		 * e.g. 'compressor_options' => '--nomunge --preserve-semi'
		 */
		'compressor_options' => NULL,
	),
	
	/**
	 * Options for configuring YUI Compressor
	 */
	'yui' => array(
		
		/**
		 * The path to the java executable. On most UNIX systems, this 
		 * will be in your PATH, so 'java' will be fine.
		 */
		'java' => 'java',
		
		/**
		 * The location of the YUI jar file
		 */
		'jar' => MODPATH.'asset/vendor/yui/yuicompressor-2.4.2.jar',
	)
);
