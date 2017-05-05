<?php

class WikiTreeSectionNew extends InfoSection
{
 	var $object_it;
 	private $baseline = '';
    private $treeMode = '';
 	
 	function __construct( $object_it = null, $baseline = '' )
 	{
 	    $this->baseline = $baseline;
 	    
 	    if ( is_a($object_it, 'IteratorBase') ) {
 	        $this->object_it = $object_it;
 	    }
 	    elseif ( $_REQUEST['class'] != '' && $_REQUEST['root'] != '' )
 	    {
 	        $object = getFactory()->getObject($_REQUEST['class']);
 	        $this->object_it = $object->getExact(
 	            $_REQUEST['id'] == '' ? $_REQUEST['root'] : $_REQUEST['id']
 	        ); 
 	    }

 		parent::__construct();
 	}
 	
 	function getObjectIt() {
 		return $this->object_it;
 	}

 	function setObjectIt( $object_it ) {
 	    $this->object_it = $object_it;
    }

    function setPlainMode() {
        $this->treeMode = 'plain';
    }

	function getTreeData( $root, $open )
	{
		$_REQUEST['root'] = $root;
		$_REQUEST['open'] = $open;
		$_REQUEST['baseline'] = $this->baseline;
        $_REQUEST['tree-mode'] = $this->treeMode;

		ob_start();
        $this->getPage()->exportWikiTree();
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
	        'root' => join('-',$root_it->idsToArray()),
	    	'baseline' => $this->baseline,
            'tree-mode' => $this->treeMode
	    );
	}
	
 	function getTemplate()
	{
		return 'pm/WikiTreeSectionNew.php';
	}
	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();
		$url = $object_it->object->getPage();
		
		if ( strpos($url, '?') === false ) $url .= '?';
		
		$url .= '&export=tree&tree-mode='.$this->treeMode;
		if ( $this->baseline != '' ) {
			$url .= '&baseline='.$this->baseline;
		}

		if ( is_object($object_it) && $this->treeMode == '' ) {
            if ( $object_it->get('ParentPage') != '' ) {
                $url .= '&open='.$object_it->getId();
            }
			$root_it = $object_it->get('ParentPage') != '' ? $object_it->getRootIt() : $object_it;
		}
		else {
            $root_it = $object_it;
        }
        $url .= '&root='.(is_object($root_it) ? join('-',$root_it->idsToArray()) : 0);

        $menuItemIt = $object_it->object->createCachedIterator(
            array(
                array(
                    $object_it->object->getIdAttribute() => '%id%',
                    'DocumentId' => $root_it->getId(),
                    'ParentPath' => $root_it->getId()
                )
            )
        );
		
		$treeData = $this->getTreeData(
			is_object($root_it) ? join('-',$root_it->idsToArray()) : '0',
			$object_it->get('ParentPage') != '' ? $object_it->getId() : ''
		);

	    return array_merge( parent::getRenderParms(), array (
            'url' => $url,
            'data' => $treeData,
            'object_class' => get_class($object_it->object),
            'base_app_url' => getSession()->getApplicationUrl(),
            'actions' => $this->getPage()->getFormRef()->getTreeMenu($menuItemIt)
	    ));
	}
	
 	function getCaption() {
 		return text(2309);
 	}
}