<?php

class WikiTreeSection extends InfoSection
{
 	var $object_it;
 	
 	private $baseline = '';
 	
 	function __construct( $object_it = null, $baseline = '' )
 	{
 	    global $_REQUEST, $model_factory;
 	    
 	    $this->baseline = $baseline;
 	    
 	    if ( is_a($object_it, 'IteratorBase') )
 	    {
 	        $this->object_it = $object_it;
 	    }
 	    elseif ( $_REQUEST['class'] != '' && $_REQUEST['root'] != '' )
 	    {
 	        $object = $model_factory->getObject($_REQUEST['class']);
 	        
 	        $this->object_it = $object->getExact(
 	            $_REQUEST['id'] == '' ? $_REQUEST['root'] : $_REQUEST['id']
 	        ); 
 	    }

 		parent::__construct();
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
	function getTreeJson( $root, $open )
	{
		global $_REQUEST;
 		
		$_REQUEST['root'] = $root;
		$_REQUEST['open'] = $open;
		$_REQUEST['baseline'] = $this->baseline;
		
		ob_start();
		
		$page = $this->getPage();
		
		$page->exportWikiTree();
		
		$json = ob_get_contents();
		
		ob_end_clean();
		
		return $json;
	}
	
	function getParameters()
	{
	    $root_it = $this->object_it->getRootIt();
	    
	    return array (
	        'class' => get_class($this->object_it->object),
	        'id' => $this->object_it->getId(),
	        'root' => $root_it->getId(),
	    	'baseline' => $this->baseline
	    );
	}
	
 	function getTemplate()
	{
		return 'pm/WikiTreeSection.php';
	}
	
	function getRenderParms()
	{
	    global $model_factory;
	    
		$object_it = $this->getObjectIt();

		$url = $object_it->object->getPage();
		
		if ( strpos($url, '?') === false ) $url .= '?';
		
		$url .= '&export=tree';
		
		if ( $object_it->get('ParentPage') != '' )
		{
			$url .= '&open='.$object_it->getId();
		}

		if ( $this->baseline != '' )
		{
			$url .= '&baseline='.$this->baseline;
		}
		
		if ( is_object($object_it) )
		{
			$root_it = $object_it->get('ParentPage') != '' ? $object_it->getRootIt() : $object_it;
		}
		
		$treeData = $this->getTreeJson(
			is_object($root_it) ? $root_it->getId() : '0',
			$object_it->get('ParentPage') != '' ? $object_it->getId() : ''
		);

	    return array_merge( parent::getRenderParms(), array (
            'url' => $url,
            'data' => $treeData,
            'root_id' => is_object($root_it) ? $root_it->getId() : 0,
            'object_class' => get_class($object_it->object),
            'base_app_url' => getSession()->getApplicationUrl()
	    ));
	}
	
 	function getCaption()
 	{
 		return text(2204);
 	}
}