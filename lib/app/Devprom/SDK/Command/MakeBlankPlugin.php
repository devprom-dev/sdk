<?php

namespace Devprom\SDK\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnitedPrototype\GoogleAnalytics;

class MakeBlankPlugin extends Command
{
    protected function configure()
    {
        $this
            ->setName('new-plugin')
            ->setDescription('Creates new plugin to be implemented')
            ->addArgument(
                'plugin-name',
                InputArgument::REQUIRED,
                'Internal unique name of the plugin, please use numbers and letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->copyFiles( $input->getArgument('plugin-name') );
        
        $this->enablePlugin( $input->getArgument('plugin-name') );
    }
    
    protected function copyFiles( $plugin_name )
    {
    	mkdir(realpath(__DIR__."/../../../../../app/plugins")."/".$plugin_name);

    	$this->full_copy(
    			realpath(__DIR__ . "/../Resources/plugins/myplugin")."/", 
    			realpath(__DIR__."/../../../../../app/plugins/".$plugin_name)."/"
		);
    	 
    	$plugin_folder = realpath(__DIR__."/../../../../../app/plugins");
    	$plugin_file = $plugin_folder."/".$plugin_name.".php";
    	
    	copy(
    			realpath(__DIR__ . "/../Resources/plugins")."/myplugin.php", 
    			$plugin_file
		); 
    	
    	$files = array (
    			$plugin_file,
    			$plugin_folder . "/" . $plugin_name . "/AdminPlugin.php",
    			$plugin_folder . "/" . $plugin_name . "/PMPlugin.php",
    			$plugin_folder . "/" . $plugin_name . "/COPlugin.php",
    			$plugin_folder . "/" . $plugin_name . "/MyPlugin.php",
    	);
    	
    	foreach( $files as $file_name )
    	{
	    	file_put_contents(
	    			$file_name, preg_replace('/myplugin/i', $plugin_name, file_get_contents($file_name))
	    	);
    	} 
    	
    	rename( 
    			$plugin_folder . "/" . $plugin_name . "/MyPlugin.php", 
    			$plugin_folder . "/" . $plugin_name . "/". $plugin_name.".php"
    	);
    }
    
    protected function enablePlugin( $plugin_name )
    {
    	$plugin_folder = realpath(__DIR__."/../../../../../app/plugins");
    	
    	file_put_contents(
    			$plugin_folder . "/plugins.php",
    			file_get_contents($plugin_folder . "/plugins.php").PHP_EOL.
    					'include_once SERVER_ROOT_PATH."plugins/'.$plugin_name.'.php";');
    	
    	$cache_path = realpath(__DIR__."/../../../../../dev/apache/htdocs/cache");
    	
    	if ( $cache_path != '' ) $this->full_delete( $cache_path."/" );
    }
    
 	private function full_copy( $source_path, $destination_path, $application = true ) 
 	{
        if (is_dir($source_path)) {
           if ($dh = opendir($source_path)) {
               while (($file = readdir($dh)) !== false ) {
                   if( $file != "." && $file != ".." )
                   {
                       if( is_dir( $source_path . $file ) )
                       {
                       		$result = !is_dir($destination_path . $file) ? mkdir($destination_path . $file) : true;

                           	$this->full_copy( $source_path . $file . "/", $destination_path . $file . "/" );
                       }
                       else
                       {
                            $result = copy($source_path.$file, $destination_path.$file);
                       }
                   }
               }
               closedir($dh);
           }
       }
 	}
 	
	private function full_delete( $dir, $except = array() )
	{
       	if ( !is_dir($dir) )
       	{
       		return false;
       	}
       
        if ( !($dh = opendir($dir)) ) 
        {
       		return false;
        }
        
		while (($file = readdir($dh)) !== false ) 
		{
           	if( $file != "." && $file != ".." && !in_array($file, $except) )
			{
				if( is_dir( $dir . $file ) )
				{
					$this->full_delete( $dir . $file . "/" );
				}
				else
				{
					unlink( $dir . $file );
				}
			}
		}
			
		closedir( $dh );
		
		rmdir( $dir );
		
		return true;
	} 	
 	
}