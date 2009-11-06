<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Simple asset helper. Outputs javascript tags for multiple asset 
 * files, automatically concatenates them into one file as well.
 * 
 * Current only javascript tags are supported. Asset hosts, JSMin, and CSS
 * support are definitely planned. 
 *
 * @package default
 * @author Jonathan Geiger
 */
class Asset
{
	const JAVASCRIPT = 'javascripts';
	const STYLESHEET = 'stylesheets';
	const CACHE = 'cache';
	
	/**
	 * Generates javascript tags based on the paths provided. 
	 * Caches automatically. File extensions (.js) are optional.
	 *  
	 * @param array 	$paths		The paths to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function javascripts(array $paths, $prefix = NULL)
	{		
		$scripts = '';
		
		// Just loop through each one and output it
		foreach(asset::paths($paths, $prefix, asset::JAVASCRIPT) as $path)
		{
			$scripts .= html::script($path)."\n";
		}
		
		return $scripts;
	}
	
	/**
	 * @param array 	$paths		The paths to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function stylesheets(array $paths, $prefix = NULL)
	{		
		$scripts = '';
		
		// Just loop through each one and output it
		foreach(asset::paths($paths, $prefix, asset::STYLESHEET) as $path)
		{
			$scripts .= html::style($path)."\n";
		}
		
		return $scripts;
	}
	
	/**
	 * Creates a full path for a particular file, taking into account things such as relative/absolute paths
	 *
	 * @param array 	$path 		The path to fix
	 * @param string 	$type 		The type of file we're working with
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function path($path, $type)
	{	
		// Find the prefix and extension
		$prefix = asset::config($type, 'prefix');
		$extension = asset::config($type, 'extension');
		
		// Append the prefix only if it's a relative path
		if (substr($path, 0, 1) != '/')
		{
			$path = $prefix.$path;
		}
		
		// Ensure we have a file extension (except caches)
		if ($type !== asset::CACHE && $extension !== substr($path, -strlen($extension)))
		{
			$path = $path.$extension;
		}
				
		return $path;
	}
	
	/**
	 * Determines the paths to be used for a set of asset includes.
	 *
	 * @param array 	$paths 		The paths to generate tags for
	 * @param string 	$prefix 	A prefix to add to any generated cache files
	 * @param string 	$type 		The type of file we're working with
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function paths(array $paths, $prefix, $type)
	{
		$greatest = 0;
		$docroot = asset::config('root');
		$count = count($paths);
			
		// Check if the cache needs to be renewed
		for ($i = 0; $i < $count; $i++)
		{			
			$path = asset::path($paths[$i], $type);
				
			// Gather last modified time, the greatest of which will 
			// be used in the filename of the cache file	
			if (($time = @filemtime($docroot.$path)) > $greatest) 
			{
				$greatest = $time;
			}
			
			// Create the scripts, which may be discarded altogether 
			// if the cache file is present
			$paths[$i] = $path."?".$time;
		}
		
		// Now that we've found the cache path, we can 
		// just concatenate it all together
		$cache = asset::path($prefix.$greatest, asset::CACHE).asset::config($type, 'extension');
		
		// Check the cache
		if (asset::cached($docroot.$cache, $type) || asset::cache($paths, $type, $cache))
		{
			return array($cache);
		}
		else
		{
			return $paths;
		}
	}
	
	/**
	 * Simple accessor for configuration properties
	 *
	 * @param string 	$group  The group (main array key) to find
	 * @param string 	$key 	The key (sub-array key) to retrieve
	 * @return void
	 * @author Jonathan Geiger
	 */
	protected static function config($group = NULL, $key = NULL)
	{
		static $config = NULL;
		
		// First time, load that config
		if ($config === NULL)
		{
			$config = Kohana::config('assets');
		}
		
		if ($group === NULL)
		{
			return $config;
		}
		else if ($key === NULL)
		{
			return $config[$group];
		}
		else
		{
			return $config[$group][$key];
		}		
	}
	
	/**
	 * Determines whether or not a file is cached and 
	 * the system is accepting cached files
	 *
	 * @param string 	$file 	The file to check is cached
	 * @param string 	$type  	The type of file we're working with
	 * @return void
	 * @author Jonathan Geiger
	 */
	protected static function cached($file, $type)
	{
		return (asset::config($type, 'cache') && file_exists($file));
	}
	
	/**
	 * Caches a group of files
	 *
	 * @param string 	$paths 	The paths to cache
	 * @param string 	$type  	The type of file we're working with
	 * @param string 	$cache 	A path to the file to concatenate $paths to
	 * @return mixed
	 * @author Jonathan Geiger
	 */
	protected static function cache($paths, $type, $cache)
	{
		// Make sure we're allowed to cache
		if (!asset::config($type, 'cache')) return FALSE;
		
		// The total number of bytes written to the file
		$written = 0;
		
		// Open that file!
		$cache = fopen($cache, 'w+');
				
		if ($cache)
		{
			// That sucks. Another loop
			foreach ($paths as $path)
			{						
				$path = asset::config('root').$path;	
				
				// Paths could potentially have a cachebuster on 
				// the end of them. Make sure to strip it.
				if ($pos = strpos($path, '?'))
				{
					$path = substr($path, 0, $pos);
				}

				// Write to the cache
				$written += fwrite($cache, file_get_contents($path)."\n");
			}
			
			// Done here
			fclose($cache);
		}
		
		return $written;
	}
	
} // END class Asset