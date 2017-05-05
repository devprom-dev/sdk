<?php

namespace Devprom\CommonBundle\Service\Emails; 

use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Config\FileLocator;

include_once SERVER_ROOT_PATH."admin/classes/templates/SystemTemplate.php";

class RenderService
{
	private $templating = null;
	
	public function __construct( $session, $additional_path = SERVER_ROOT_PATH )
	{
		$lang = strtolower($session->getLanguageUid());

		$paths = array (
 			SERVER_ROOT_PATH.'co/bundles/Devprom/CommonBundle/Resources/views/Emails/'.$lang,
 			rtrim($additional_path,"\\/").'/'.$lang
 		);
		foreach( $paths as $key => $value ) {
			if ( !is_dir($value) ) unset($paths[$key]);
		}
		
		$plugins_paths = array();
		if ( is_dir(\SystemTemplate::getPath().$lang) ) {
			$plugins_paths[] = \SystemTemplate::getPath().$lang;
		}
		foreach( getFactory()->getPluginsManager()->getNamespaces() as $plugin )
		{
			$path = realpath(SERVER_ROOT_PATH.'plugins/'.$plugin->getNamespace().'/resources/'.$lang);
			if ( is_dir($path) ) $plugins_paths[] = $path;
		}
		
        $this->templating = new TwigEngine(
 			new \Twig_Environment(
 					new \Twig_Loader_Filesystem(
 							array_merge(
 									$plugins_paths, 
		 							$paths
 							)
 					), 
 					array(
						'cache' => CACHE_PATH.'/symfony2-pm',
 						'charset' => APP_ENCODING
					)
        	),
        	new TemplateNameParser(),
        	new FileLocator()
		);
	}
	
	public function getContent( $template_name, $parms = array() )
	{
		return $this->templating->render($template_name, $parms);
	}
}