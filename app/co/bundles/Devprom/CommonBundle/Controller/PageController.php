<?php

namespace Devprom\CommonBundle\Controller;
use Devprom\CommonBundle\Controller\MainController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper;
use core\classes\ExceptionHandler;

include_once SERVER_ROOT_PATH."co/views/Common.php";

class PageController extends MainController
{
    var $logger;
    
    public function moduleAction(Request $request)
    {
        global $plugins, $model_factory;
        
        if ( $request->query->get('module') != 'initialize' )
        {
	    	$response = $this->checkDeploymentState($request);
	    	
	    	if ( is_object($response) ) return $response;
        }
        
        $module_it = $model_factory->getObject('Module')->getAll();

        while ( !$module_it->end() )
        {
         	if ( $module_it->getId() == $request->query->get('module') && !getFactory()->getAccessPolicy()->can_read($module_it) )
         	{
				return new RedirectResponse(
         			$this->generateUrl('error_404', 
         		    	array('page' => urlencode($request->server->get('REQUEST_URI'))) 
				));
         	}
         	
         	$module_it->moveNext();
        }
        
        $module = $plugins->useModule( 
            $request->query->get('namespace'), 'co', $request->query->get('module') 
        );
 
        if ( is_array($module) ) $pageclass = $module['classname'];
        
        if ( $pageclass == '' )
        {
 		    return new RedirectResponse(
 		            $this->generateUrl('error_404', 
 		                    array('page' => urlencode($request->server->get('REQUEST_URI'))) 
 		            ));
        }
        
        $page = new $pageclass;
        
    	if( $page->authorizationRequired() ) 
    	{
    	    $response = $this->checkUserAuthorized($request);
    	    
    	    if ( is_object($response) ) return $response;
    	}
        
        return $this->responsePage( $page ); 
    }
    
    protected function responsePage( $page )
    {
        $plugins_paths = array();
		foreach( getSession()->getPluginsManager()->getNamespaces() as $plugin )
		{
			$path = realpath(SERVER_ROOT_PATH.'plugins/'.$plugin->getNamespace().'/templates');
			if ( is_dir($path) ) $plugins_paths[] = $path.'/%name%';
		}
    	
        $templating = new PhpEngine(
 			new TemplateNameParser(), 
 			new FilesystemLoader(
 					array_merge(
 							$plugins_paths,
 							array (
 									SERVER_ROOT_PATH.'/templates/views/%name%',
 									SERVER_ROOT_PATH.'/templates/views/core/%name%'
 							)
 					)
 			), 
 			array (
				new SlotsHelper(),
 			    new RouterHelper($this->get('router')->getGenerator())
			)
		);
		
        ob_start();

    	$page->render( $templating );
    	
    	$content = ob_get_contents();

    	ob_end_clean();
    	
    	return new Response($content);
    }
    
	protected function replyError( $message )
	{
		$log = $this->getLogger();
		
		if ( is_object($log) ) $log->error( $message );
		
		return $this->replyResult( true, $message );
	}
	
	protected function replySuccess( $message, $object_id = '' )
	{
		$log = $this->getLogger();
		
		if ( is_object($log) ) $log->info( $message );
		
		return $this->replyResult( false, $message, $object_id );
	}

	protected function replyRedirect( $url, $text = '' )
	{
		return $this->_reply( 'redirect', $text, $url );
	}

	protected function replyResult( $is_error, $message, $object_id = '' )
	{
		return $this->_reply( $is_error ? 'error' : 'success', $message, $object_id );
	}

	protected function replyDenied()
	{
		return $this->replyError( text(983) );
	}
	
	protected function replyResultBinary( $is_error, $message, $object_id )
	{
		return $this->_reply( $is_error ? 'error' : 'success', $message, $object_id);  
	}    
	
	private function _reply( $state, $text, $object )
	{
		$result = array (
		    'state' => \IteratorBase::wintoutf8($state),
		    'message' => \IteratorBase::wintoutf8($text),
		    'object' => \IteratorBase::wintoutf8($object)
		);
		
		$log = $this->getLogger();

		if ( is_object($log) )
		{
		    $log->info( $result );
		    
		    $log->info( str_replace('%1', get_class($this), text(1208)) );
		}
		
	    $headers = array (
            "Expires" => "Thu, 1 Jan 1970 00:00:00 GMT",
            "Last-Modified" => gmdate("D, d M Y H:i:s") . " GMT",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache",
            "Content-type" => "text/html; charset=utf-8"
	    );
	    
		return new Response(\JsonWrapper::encode($result), 200, $headers);
	}
	
 	protected function checkRequired( Request $request, $fields )
 	{
 		for( $i = 0; $i < count($fields); $i++ )
 		{
 			if ( $request->request->get($fields[$i]) == '' )
 			{
 				return $this->replyError( text(615) );
 			}
 		}
 	}
	
 	protected function getLogger()
 	{
 	    if ( !is_object($this->logger) )
 	    {
     	    try 
     		{
     			$this->logger = \Logger::getLogger('Commands');
     		}
     		catch( \Exception $e)
     		{
     			error_log('Unable initialize logger: '.$e->getMessage());
     		}
 	    }
 	    
 		return $this->logger;
 	}
}