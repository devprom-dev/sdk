<?php

namespace Devprom\CommonBundle\Service\Emails; 

use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Config\FileLocator;

class RenderService
{
	private $templating = null;
	
	public function __construct( $session, $additional_path = SERVER_ROOT_PATH )
	{
		$lang = strtolower($session->getLanguage()->getLanguage());
		
        $this->templating = new TwigEngine(
 			new \Twig_Environment(
 					new \Twig_Loader_Filesystem( 
 							array (
 									SERVER_ROOT_PATH.'/co/bundles/Devprom/CommonBundle/Resources/views/Emails/'.$lang,
 									rtrim($additional_path,"\\/").'/'.$lang
 							)
 					), 
 					array(
						'cache' => CACHE_PATH.'/symfony2-pm',
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