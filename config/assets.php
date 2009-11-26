<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Don't know what all of this means? Check out the wiki on GitHub:
 * 
 * @see http://wiki.github.com/jonathangeiger/kohana-asset/configuration
 */
return array(
	
	/**
	 * Config for calls to asset::javascripts()
	 */
	'javascripts' => array
	(
		'hosts' => array(),
		'root' => DOCROOT,
		'prefix' => 'javascripts/',
		'extension' => '.js',
		'cache' => 'cache/javascript-min.js',
		'compressor' => 'yui',
		'compressor_options' => NULL,
		'cache_bust' => TRUE,
	),
	
	/**
	 * Config for calls to asset::stylesheets()
	 */
	'stylesheets' => array
	(
		'hosts' => array(),
		'root' => DOCROOT,
		'prefix' => 'stylesheets/',
		'extension' => '.css',
		'cache' => 'cache/stylesheet-min.css',
		'compressor' => 'yui',
		'compressor_options' => NULL,
		'cache_bust' => TRUE,
	),
	
	/**
	 * Options for configuring YUI Compressor
	 * 
	 * @see http://wiki.github.com/jonathangeiger/kohana-asset/yui-compressor
	 */
	'yui' => array
	(
		'java' => 'java',
		'jar' => MODPATH.'asset/vendor/yui/yuicompressor-2.4.2.jar',
	)
);
