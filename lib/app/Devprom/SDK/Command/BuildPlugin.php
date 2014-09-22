<?php

namespace Devprom\SDK\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnitedPrototype\GoogleAnalytics;

class BuildPlugin extends Command
{
    protected function configure()
    {
        $this
            ->setName('build-plugin')
            ->setDescription('Prepares plugin distributive to be deployed')
            ->addArgument(
                'plugin-name',
                InputArgument::REQUIRED,
                'Internal unique name of the plugin, please use numbers and letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$plugin_name = $input->getArgument('plugin-name');
    	
    	$plugin_path = realpath(__DIR__."/../../../../../app/plugins")."/".$plugin_name;
    	$build_folder = realpath(__DIR__."/../../../../../build");
    	
		$this->full_delete($build_folder."/htdocs/");
		$this->full_delete($build_folder."/devprom/");
		
		mkdir( $build_folder."/htdocs/plugins/".$plugin_name, 775, true );
		mkdir( $build_folder."/devprom", 775, true );
		
		file_put_contents($build_folder."/devprom/update.sql", "");
		
    	$this->full_copy(
    			$plugin_path."/", 
    			$build_folder."/htdocs/plugins/".$plugin_name."/"
		);
    	
    	copy( $plugin_path.".php", $build_folder."/htdocs/plugins/".$plugin_name.".php" );
    	
    	$this->pack( $build_folder, $plugin_name );

		$this->full_delete($build_folder."/htdocs/");
		$this->full_delete($build_folder."/devprom/");
    }

    private function pack( $build_folder, $plugin_name )
    {
    	$archiver_path = realpath(__DIR__."/../../../../../dev/tools")."/7za.exe";
    	
		if ( !file_exists($archiver_path) ) return;
		
		chdir($build_folder);
		
		shell_exec($archiver_path.' a -r -tzip plugin.'.$plugin_name.'.zip devprom/* htdocs/*');
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
					@unlink( $dir . $file );
				}
			}
		}
			
		closedir( $dh );
		
		rmdir( $dir );
		
		return true;
	} 	
 	
}