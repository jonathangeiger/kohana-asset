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
	/**
	 * @param array 	$paths		The paths to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function javascripts(array $paths, $prefix = NULL)
	{
		$config = Kohana::config('assets');

		// Lots of paths to deal with here
		$base_dir = $config['js_dir'];
		$cache_dir = $config['js_cache_dir'];
		$cache = $config['cache'];
		
		// Initialize some variables
		$all = '';
		$greatest = 0;
		$written = 0;
			
		// Check if the cache needs to be renewed
		foreach ($paths as $path)
		{	
			$time = filemtime(DOCROOT.$base_dir.$path);
					
			// Gather last modified time
			if ($time > $greatest)
			{
				$greatest = $time;
			}
			
			// Create the scripts
			$all .= html::script($base_dir.$path.'?'.$time)."\n";
		}
		
		// Now that we've found the cache path, we can just concatenate it all together
		$cache_file = $cache_dir.$prefix.$greatest.'.js';
		
		// Check the cache
		if ($cache && file_exists(DOCROOT.$cache_file))
		{
			return html::script($cache_file);
		}
		
		// Still here? Do we need to cache? 
		if ($cache && asset::cache($paths, $cache_file))
		{
			return html::script($cache_file);
		}
		else
		{
			return $all;
		}
	}
	
	protected static function cache($files, $output)
	{
		$written = 0;
		
		// Hide errors
		$ERR = error_reporting(0);
		
		$file = fopen($output, 'w+');
		
		// That sucks. Another loop
		foreach ($paths as $path)
		{						
			// Write to the cache
			if ($file)
			{
				$written += fwrite($file, file_get_contents(DOCROOT.$prefix.$path));
			}
		}
		
		// Done here
		fclose($output);
		error_reporting($ERR);
		
		return $written;
	}
	
} // END class Asset