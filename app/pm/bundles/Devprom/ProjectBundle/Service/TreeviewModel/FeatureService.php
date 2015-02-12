<?php

namespace Devprom\ProjectBundle\Service\TreeviewModel;

include SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";

class FeatureService
{
	private $object = null;
	private $object_it = null;
	
	public function __construct( $root )
	{
		getSession()->addBuilder( new \FeatureModelExtendedBuilder() );
		
    	$this->object = getFactory()->getObject('Feature');

    	$predicates = $root > 0 
    		? array (
    				new \FilterAttributePredicate('ParentFeature', $root),
    				new \FilterVpdPredicate(),
    				new \SortFeatureHierarchyClause()
    			)
    		: array (
    				new \FeatureRootFilter(),
    				new \FilterVpdPredicate(),
    				new \SortFeatureHierarchyClause()
    			);
    		
    	$this->setObjectIt( $this->object->getRegistry()->Query($predicates) );
	}

	public function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	public function getObjectIt()
	{
		return $this->object_it;
	}
	
    public function getData()
    {
    	$uid = new \ObjectUID();
    	
    	$object_it = $this->getObjectIt();
    	
    	$data = array();
    	
     	while ( !$object_it->end() )
 		{
 		 	if ( $object_it->get('ChildrenCount') > 0 )
    		{
   				$image = 'folder';
    		}
    		else
    		{
    			$image = 'wiki_document';
    		}
 			
    		$item = array();

    		$title = \IteratorBase::wintoutf8($object_it->getDisplayName());
    		
    		$uid_info = $uid->getUidInfo($object_it);
    		
    		$uid_text = '['.$uid_info['uid'].']';
    		
    		if ( $uid_info['alien'] ) $uid_text .= ' {'.$uid_info['project'].'}'; 
    		
 			$item['text'] = 
 	 			'<div class="treeview-label '.$image.'">'.
 	 			'<a class="treeview-title wiki_tree_node item" href="javascript:" object="'.$object_it->getId().'">'.
 				$uid_text.' '.$title.
 				'</a>'.
 				'</div>';

 			$item['expanded'] = false;
 			$item['classes'] = "folder ".$image;
 			$item['id'] = $object_it->getId();
 			$item['hasChildren'] = $object_it->get('ChildrenCount') > 0;
 			
 			$data[] = $item;
 			
 			$object_it->moveNext();
 		}
 		
 		return $data;
    }
}