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
		$assets = new Asset($paths, asset::JAVASCRIPT, $prefix);
		return $assets->render();
	}
	
	/**
	 * @param array 	$paths		The paths to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function stylesheets(array $paths, $prefix = NULL)
	{		
		$assets = new Asset($paths, asset::STYLESHEET, $prefix);
		return $assets->render();
	}
	
	/**
	 * The paths we're working with
	 *
	 * @var array
	 */
	protected $paths;
	
	/**
	 * Whether or not the paths have been processed
	 */
	protected $processed = FALSE;
	
	/**
	 * The asset type
	 *
	 * @var string
	 */
	protected $type;
	
	/**
	 * Final rendered HTML
	 *
	 * @var string
	 */
	protected $html;
	
	/**
	 * Loaded from the config
	 *
	 * @var string
	 */
	protected $prefix;
	protected $extension;
	protected $cache;
	protected $cache_prefix = '';
	protected $root;

	
	/**
	 * Constructor 
	 * 
	 * @author Jonathan Geiger
	 */
	public function __construct(array $paths, $type, $prefix = '')
	{
		static $config;
		
		// Only do this once
		if (!$config)
		{
			$config = Kohana::config('assets');
		}
		
		// Load the configuration as instance variables
		foreach (arr::extract($config[$type], array('prefix', 'extension', 'cache', 'cache_prefix', 'root')) as $key => $value)
		{
			$this->$key = $value;
		}
		
		// Update the cache_prefix to include the optional prefix
		$this->cache_prefix .= $prefix;
		
		// And set the rest of the instance variables
		$this->paths = $paths;
		$this->type = $type;
		
		// Process the paths
		$this->process_paths();
	}
	
	/**
	 * Renders the assets out to HTML
	 *
	 * @return void
	 * @author Jonathan Geiger
	 */
	public function render()
	{
		$paths = $this->paths;
		$html = '';
		
		switch ($this->type)
		{
			case asset::JAVASCRIPT:
				foreach ($paths as $path)
				{
					$html .= html::script($path)."\n";	
				}
				break;
				
			case asset::STYLESHEET:
				foreach ($paths as $path)
				{
					$html .= html::style($path)."\n";	
				}
				break;
		}
		
		return $html;
	}
	
	/**
	 * Creates a full path for a particular file, taking into account things such as relative/absolute paths
	 *
	 * @param array 	$path 		The path to fix
	 * @return void
	 * @author Jonathan Geiger
	 */
	protected function path($path)
	{			
		// Append the prefix only if it's a relative path
		if (substr($path, 0, 1) != '/')
		{
			$path = $this->prefix.$path;
		}
		
		// Ensure we have a file extension
		if ($this->extension !== substr($path, -strlen($this->extension)))
		{
			$path = $path.$this->extension;
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
	protected function process_paths()
	{
		// Check the flag so we're not going through this multiple times
		if (!$this->processed)
		{
			$greatest = 0;

			// Check if the cache needs to be renewed
			foreach ($this->paths as $i => $path)
			{			
				$path = $this->path($path);

				// Gather last modified time, the greatest of which will 
				// be used in the filename of the cache file	
				if (($time = @filemtime($this->root.$path)) > $greatest) 
				{
					$greatest = $time;
				}

				// Create the scripts, which may be discarded altogether 
				// if the cache file is present
				$paths[$i] = $path."?".$time;
			}

			// Now that we've found the cache path, we can 
			// just concatenate it all together
			$cache = $this->cache_prefix.$greatest.$this->extension;

			// Check the cache
			if ($this->cached($this->root.$cache) || $this->cache($paths, $this->root.$cache))
			{
				$this->paths = array($cache);
			}
			else
			{
				$this->paths = $paths;
			}
			
			// Make sure to update the processed flag so we only go through this once
			$this->processed = TRUE;
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
	protected function cached($file)
	{
		return ($this->cache && file_exists($file));
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
	protected function cache($paths, $cache)
	{
		// Make sure we're allowed to cache
		if (!$this->cache) return FALSE;
		
		// The total number of bytes written to the file
		$written = 0;
		
		// Disable error reporting for now
		$ERR = error_reporting(0);
		
		// Open that file!
		$cache = fopen($cache, 'w+');
				
		if ($cache)
		{
			// That sucks. Another loop
			foreach ($paths as $path)
			{						
				$path = $this->root.$path;	
				
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
		
		// Re-enable errors
		error_reporting($ERR);
		
		return $written;
	}
	
} // END class Asset