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
	 * @param array 	$files		The files to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function javascripts(array $files, $prefix = NULL)
	{		
		$asset = new Asset($files, asset::JAVASCRIPT, $prefix);
		return $asset->render();
	}
	
	/**
	 * @param array 	$files		The files to generate
	 * @param string	$prefix 	A string to prefix to the cache file
	 * @return void
	 * @author Jonathan Geiger
	 */
	public static function stylesheets(array $files, $prefix = NULL)
	{		
		$asset = new Asset($files, asset::STYLESHEET, $prefix);
		return $asset->render();
	}
	
	/**
	 * The paths we're working with
	 *
	 * @var array
	 */
	protected $files;
	
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
	 * An array of asset hosts to use
	 *
	 * @var array
	 */
	protected $hosts;
	
	/**
	 * The working host
	 *
	 * @var string
	 */
	protected $host;
	
	/**
	 * The working host root. Used in case the cache file 
	 * needs to be saved somewhere else
	 *
	 * @var string
	 */
	protected $host_root;
	
	/**
	 * The path to the files
	 * 
	 * @var string
	 */
	protected $root;
	
	/**
	 * Prefix to generated paths
	 * 
	 * @var string
	 */
	protected $prefix;
	
	/**
	 * Extension to the files
	 * 
	 * @var string
	 */
	protected $extension;
	
	/**
	 * Whether or not to cache
	 *
	 * @var boolean
	 */
	protected $cache;
	
	/**
	 * Whether or not items were successfully cached
	 *
	 * @var boolean
	 */
	protected $cached;
	
	/**
	 * Prefix for cached files.
	 * 
	 * @var string
	 */
	protected $cache_prefix;
	
	/**
	 * Constructor 
	 * 
	 * @author Jonathan Geiger
	 */
	public function __construct(array $files, $type, $options = NULL)
	{
		static $config;
		
		// Only do this once
		if (!$config)
		{
			$config = Kohana::config('assets');
		}
		
		// String? Append to the prefix
		if (is_string($options))
		{
			$options = array('cache_prefix' => $config[$type]['cache_prefix'].$options);
		}
		// False? Disable caching
		else if ($options === FALSE)
		{
			$options = array('cache' => FALSE);
		}
		// Not an array? Not useful
		else if (!is_array($options))
		{
			$options = array();
		}
		
		// Load the configuration
		foreach(arr::merge($config[$type], $options) as $key => $value)
		{
			$this->$key = $value;
		}
		
		// Set the type
		$this->type = $type;
		
		// Set the working host_root and host, which is determined by the host
		$host = $this->host();
		$this->host_root = $host['root'];
		$this->host = $host['host'];
		
		// Process the paths
		$this->process_files($files);
	}
	
	/**
	 * Proxies to render() to show the links
	 *
	 * @return string
	 * @author Jonathan Geiger
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/**
	 * Renders the assets out to HTML
	 *
	 * @return string
	 * @author Jonathan Geiger
	 */
	public function render()
	{
		if (empty($this->html))
		{
			foreach($this->files as $file)
			{
				if ($this->cached && $file['cache']) continue;
				
				switch($this->type)
				{
					case Asset::JAVASCRIPT:
						$this->html .= html::script($file['remote'])."\n";
						break;
						
					case Asset::STYLESHEET:
						$this->html .= html::style($file['remote'])."\n";
						break;
				}
			}
		}
		
		return $this->html;
	}
	
	/**
	 * Returns all of the files after they've been processed. Each file is represented 
	 * by an array containing the following keys and values:
	 * 
	 * remote => the remote path that will be used in the HTML tag
	 * local => the location of the file on the filesystem
	 * mtime => the filemtime() of the file or NULL if the file wasn't found
	 * cache => whether or not the file can be cached. This is determined by the 
	 * 			config and whether or not the file was found on the filesystem
	 *
	 * @return array
	 * @author Jonathan Geiger
	 **/
	public function files()
	{
		return $this->files;
	}
	
	/**
	 * Determines a large amount of information about a particular 
	 * file, all of which are useful in the final output.
	 *
	 * @param array 	$path 		The path to fix
	 * @return void
	 * @author Jonathan Geiger
	 */
	protected function file_info($path)
	{			
		// Ensure we have a file extension
		if ($this->extension !== substr($path, -strlen($this->extension)))
		{
			$path = $path.$this->extension;
		}
		
		// Set up the initial array to return
		$info = array(
			// Path that is used for the HTML tag
			'remote' => $path,
			// Path on the filesystem
			'local' => $path,
			// Last modified time
			'mtime' => NULL,
			// Whether or not to cache
			'cache' => $this->cache
		);
		
		// Check to see if it's a path pointing to a domain, 
		// these files are ignored, but still outputted.
		if (FALSE !== strpos($path, '://'))
		{
			// Don't cache these
			$info['cache'] = FALSE;
			
			// Disallow local path access
			$info['local'] = NULL;
			
			// Just exit early. There's nothing else that can be done
			return $info;
		}
				
		// Append the prefix only if it's a relative path
		if (substr($path, 0, 1) != '/')
		{
			$info['remote'] = $info['local'] = $this->prefix.$path;
		}
		
		// Append root to the path. We'll need this to see if we 
		// can actually locate the file on the FS
		$info['local'] = $this->root.$info['local'];
		
		// See if we can find it.
		$info['mtime'] = @filemtime($info['local']);
		
		// filemtime() doubles as a check for file existence,
		// if it's not there the file won't be cached.
		if ($info['mtime'])
		{
			// Append a cachebuster
			$info['remote'] .= '?'.$info['mtime'];
			
			// The host is empty if there aren't any 
			// configured, so the prepend is safe
			$info['remote'] = $this->host.$info['remote'];
		} 
		
		// Nothing found? Make sure we don't try and 
		// cache this down the line and it's outputted as its own tag
		else
		{
			$info['cache'] = FALSE;
		}
				
		return $info;
	}
	
	/**
	 * Determines the paths to be used for a set of asset includes.
	 *
	 * @param array 	$files 		The paths to generate tags for
	 * @return void
	 * @author Jonathan Geiger
	 */
	protected function process_files(array $files)
	{
		// Check the flag so we're not going through this multiple times
		if (empty($this->files))
		{
			$greatest = 0;

			// Check if the cache needs to be renewed
			foreach ($files as $i => $file)
			{			
				$files[$i] = $file = $this->file_info($file);
				
				// Gather last modified time, the greatest of which will 
				// be used in the filename of the cache file	
				if ($file['mtime'] > $greatest) 
				{
					$greatest = $file['mtime'];
				}			
			}

			// Now that we've found the cache path, we can 
			// just concatenate it all together
			$cache = $this->cache_prefix.$greatest.$this->extension;
			
			// Set this flag so that we know not to output cached items
			$this->cached = FALSE;
			
			// Check the cache
			if ($this->cached($this->host_root.$cache) || $this->cache($files, $this->host_root.$cache))
			{
				// Append this to the files array just as if it were a normal file
				// When it's all rendered, anything with cache=FALSE will be outputted, since it wasn't cached
				array_unshift($files, array(
					'local' => $this->host_root.$cache,
					'remote' => $this->host.$cache,
					'cache' => FALSE,
					'mtime' => NULL
				));
				
				// Set this flag so that we know not to output cached items
				$this->cached = TRUE;
			}
			
			$this->files = $files;
		}
	}
		
	/**
	 * Returns a random host and root for that host
	 *
	 * @return array
	 * @author Jonathan Geiger
	 **/
	protected function host()
	{
		// Always the same without hosts
		if (empty($this->hosts)) 
		{
			return array(
				'host' => '', 
				'root' => $this->root
			);	
		}
		
		$key = array_rand($this->hosts);
		
		// If the key is an integer, the value is the host 
		// and root defaults to $this->root
		if (is_int($key))
		{
			return array(
				'host' => $this->hosts[$key],
				'root' => $this->root
			);
		}
		// Otherwise, it's an associative item and the key 
		// is the host and root is the value of the item
		else
		{
			return array(
				'host' => $key,
				'root' => $this->hosts[$key]
			);
		}
	}
	
	/**
	 * Determines whether or not a file is cached and 
	 * the system is accepting cached files
	 *
	 * @param string 	$file 	The file to check is cached
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
	 * @param string 	$cache 	A path to the file to concatenate $paths to
	 * @return mixed
	 * @author Jonathan Geiger
	 */
	protected function cache($files, $cache)
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
			foreach ($files as $file)
			{					
				if ($file['cache'])	
				{
					// Write to the cache
					$written += fwrite($cache, file_get_contents($file['local'])."\n");
				}
			}
			
			// Done here
			fclose($cache);
		}
		
		// Re-enable errors
		error_reporting($ERR);
		
		return $written;
	}
	
} // END class Asset