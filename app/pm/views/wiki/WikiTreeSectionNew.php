<?php

class WikiTreeSectionNew extends InfoSection
{
 	var $object_it;
 	private $baseline = '';
 	private $rootIt = null;
 	private $table = null;

 	function __construct( $table, $baseline = '' )
 	{
 	    $this->baseline = $baseline;
 	    $this->table = $table;
 	    $this->rootIt = $table->getDocumentIt();
        $object_it = $table->getObjectIt()->getId() > 0
            ? $table->getObjectIt()
            : $table->getDocumentIt();
 	    
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

 	    if ( !is_object($this->rootIt) && is_object($this->object_it) ) {
 	        $this->rootIt = $this->object_it->getRootIt();
        }

 		parent::__construct();
 	}
 	
 	function getObjectIt() {
 		return $this->object_it;
 	}

 	function setObjectIt( $object_it ) {
 	    $this->object_it = $object_it;
    }

	function getTreeData( $root, $open )
	{
		$_REQUEST['root'] = $root;
		$_REQUEST['open'] = $open;
		$_REQUEST['baseline'] = $this->baseline;

		return $this->getPage()->exportWikiTree();
	}
	
	function getParameters()
	{
	    return array (
	        'class' => get_class($this->object_it->object),
	        'id' => $this->object_it->getId(),
	        'root' => join('-',$this->rootIt->idsToArray()),
	    	'baseline' => $this->baseline
	    );
	}
	
 	function getTemplate()
	{
		return 'pm/WikiTreeSectionNew.php';
	}
	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();
        $object_it->object->setVpdContext(getSession()->getProjectIt());
		$url = $object_it->object->getPage();
		
		if ( strpos($url, '?') === false ) $url .= '?';
		
		$url .= '&export=tree';
		if ( $this->baseline != '' ) {
			$url .= '&baseline='.$this->baseline;
		}

		if ( is_object($object_it) ) {
            if ( $object_it->get('ParentPage') != '' ) {
                $url .= '&open='.$object_it->getId();
            }
			$root_it = $object_it->get('ParentPage') != '' ? $this->rootIt : $object_it;
		}
		else {
            $root_it = $object_it;
        }
        $url .= '&root='.(is_object($root_it) ? join('-',$root_it->idsToArray()) : 0);

        $menuItemIt = $object_it->object->createCachedIterator(
            array(
                array(
                    $object_it->object->getIdAttribute() => 'item-id-template',
                    'DocumentId' => $root_it->getId(),
                    'ParentPath' => $root_it->getId()
                )
            )
        );
		
		$treeData = $this->getTreeData(
			is_object($root_it) ? join('-',$root_it->idsToArray()) : '0',
			$object_it->get('ParentPage') != '' ? $object_it->getId() : ''
		);
        $filterValues = $this->table->getFilterValues();

	    return array_merge( parent::getRenderParms(), array (
            'url' => $url . '&treeoptions='.$filterValues['treeoptions'],
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