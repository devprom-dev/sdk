<?php

class LocalizationTest extends DevpromTestCase
{
	private $usage = array();
	
	private $resources = array();
	
	private $files = array();
	
	private $skip = array('procloud', 'ext', 'cache', 'images', 'scripts', 'styles', 'ckeditor', 'js', 'css');
	
    function setUp()
    {   
    	$this->resources['en'] = array();
    	$this->resources['ru'] = array();
    	
    	$this->buildResourcesUsage( SERVER_ROOT_PATH );
    }
    
    function testHaveNoMissedTranslations()
    {
    	$missed_resources = array_diff($this->usage, $this->resources['ru']);
    	
    	foreach( $missed_resources as $resource )
    	{
    		foreach( $this->files as $file => $data )
    		{
    			if ( in_array($resource, $data) ) echo chr(10).$file.': '.$resource; 
    		}
    	}
    	
    	$this->assertLessThan( 1, count($missed_resources) );
    	
    	$missed_translation = array_diff($this->usage, $this->resources['en']);
    	
    	foreach( $missed_translation as $resource )
    	{
    		foreach( $this->files as $file => $data )
    		{
    			if ( in_array($resource, $data) ) echo chr(10).$file.': '.$resource; 
    		}
    	}
    	
    	$this->assertLessThan( 1, count($missed_translation) );
    }
    
    private function buildResourcesUsage( $path )
    {
		if ( !is_dir($path) ) return;

		$dh = opendir($path);
		
		while (($file = readdir($dh)) !== false ) 
		{
			if( $file != "." && $file != ".." && !in_array($file, $this->skip) )
			{
				if( is_dir( $path . $file ) )
				{
					$this->buildResourcesUsage( $path . $file . "/" );
				}
				elseif ( $file == 'resource.php' || $file == 'terms.php' )
				{
					$data = include_once($path . $file);
					if ( is_array($data) ) $text_array = $data;

					$language = basename($path);
					
					if ( is_array($text_array) )
					{
						$keys = array_keys($text_array);
						
						if ( strpos($path, 'ApplicationBundle') > 0 )
						{
							array_walk( $keys, function(&$value, $key) use($plugin) {
								if ( is_numeric($value) ) $value = 'co'.$value;
							});
						}

						$this->resources[$language] = array_merge($this->resources[$language], $keys);	
					}
					
					if ( is_array($plugin_text_array) )
					{
						$plugin = basename(dirname(dirname($path)));
						
						$keys = array_keys($plugin_text_array);
						
						array_walk( $keys, function(&$value, $key) use($plugin) {
							if ( is_numeric($value) ) $value = $plugin.$value;
						});

						$this->resources[$language] = array_merge($this->resources[$language], $keys);	
					}
				}
				elseif ( false && $file == 'c_generated.php' )
				{
					global $generated_entities , $generated_attributes;
					
					foreach( $generated_entities as $key => $data )
					{
						if ( strpos($data['Caption'], 'text') !== false ) continue;
							
						$this->usage[] = trim($data['Caption']);
						
						$this->files[$path . $file][] = trim($data['Caption']);
					}
					
					foreach( $generated_attributes as $key => $entity )
					{
						foreach( $entity as $key => $data )
						{
							if ( strpos($data['Caption'], 'text') !== false ) continue;
							
							$this->usage[] = trim($data['Caption']);
							
							$this->files[$path . $file][] = trim($data['Caption']);
						}
					}
				}
				elseif ( !in_array($file, array('procloud.php') ) )
				{
					$content = file_get_contents($path . $file);
					
					$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content); 
					
					preg_match_all( '/[^\w]text\([\'\"]?([^\)\'\"]+)[\'\"]?\)/', $content, $matches );
					
					$data = array_filter( $matches[1], function( $value ) {
						return strpos($value, '$') === false;
					});
					
					$this->files[$path . $file] = $data;
					
					$this->usage = array_merge($this->usage, $data);
					
					preg_match_all( '/[^\w]translate\([\'\"]([^\'\"]+)[\'\"]\)/', $content, $matches );
					
					$data = array_filter( $matches[1], function( $value ) {
						return strpos($value, '$') === false;
					});
					
					$this->files[$path . $file] = array_merge($this->files[$path . $file], $data);
					
					$this->usage = array_merge($this->usage, $data); 
				}
			}
		}
		
		closedir($dh);
    }
}