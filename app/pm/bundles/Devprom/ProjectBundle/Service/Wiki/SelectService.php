<?php

namespace Devprom\ProjectBundle\Service\Wiki;

include SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";

class SelectService
{
	private $object = null;
	
	private $object_it = null;
	
	public function __construct( $class_name, $root )
	{
		getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
		
    	$this->object = getFactory()->getObject($class_name);

    	$predicates = $root > 0 
    		? array (
    				new \FilterAttributePredicate('ParentPage', $root),
    				new \FilterVpdPredicate()
    			)
    		: array (
    				new \WikiRootFilter(),
    				new \FilterVpdPredicate()
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
 		    if ( $object_it->get('TotalCount') > 0 )
    		{
    			if ( $object_it->get('ContentPresents') == 'Y' )
    			{
    				$image = 'folder_page';
    			}
    			else
    			{
    				$image = 'folder';
    			}
    		}
    		else
    		{
    			$image = 'wiki_document';
    		}

    		$item = array();

    		$title = \IteratorBase::wintoutf8($object_it->get('ParentPage') == '' ? $object_it->getDisplayName() : $object_it->get('Caption'));
    		
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
 			$item['documentid'] = $object_it->get('DocumentId');
 			$item['hasChildren'] = $object_it->get('TotalCount') > 0;
 			
 			$data[] = $item;
 			
 			$object_it->moveNext();
 		}
 		
 		return $data;
    }
}