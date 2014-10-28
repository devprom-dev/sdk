<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

include SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";

class BrokenTraceExplainService
{
	private $object_it;
	
	public function __construct( $object_id )
	{
		getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
		
    	$this->object_it = getFactory()->getObject('WikiPage')->getExact($object_id);
	}
	
    public function getData()
    {
    	if ( $this->object_it->get('BrokenTraces') == "" ) return array();
    	
    	$page_it = getFactory()->getObject('WikiPageTrace')->getRegistry()->Query(
	    		array(
		    			new \FilterAttributePredicate('TargetPage', $this->object_it->getId()),
	    				new \FilterAttributePredicate('IsActual', 'N')
	    		)
    	);
    	
    	$uid = new \ObjectUID;
    	
    	$pages = array();
    	
    	$type = getFactory()->getObject('WikiType');
    	
    	while( !$page_it->end() )
    	{
    		$class_name = $type->getExact($page_it->get('SourcePageReferenceName'))->get('ClassName');
    		
    		$object_it = getFactory()->getObject($class_name)->getExact($page_it->get('SourcePage'));
    		
    		$change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
    			array(
    				new \FilterAttributePredicate('WikiPage', $object_it->getId()),
    				new \SortRecentClause()
    			)
    		);
    		
    		$author = $change_it->getId() > 0 
    				? $change_it->getRef('Author')->getDisplayName()
    				: $object_it->getRef('Author')->getDisplayName();
    		
			$recordcreated = $change_it->getId() > 0 
    				? $change_it->getDateTimeFormat('RecordCreated')
    				: $object_it->getDateTimeFormat('RecordCreated');
    		    		
    		$pages[] = array (
    				'title' => $page_it->getRef('UnsyncReasonType')->getDisplayName(),
    				'ref' => $uid->getUidWithCaption($object_it),
    				'author' => $author,
    				'date' => $recordcreated
    		);
    		
    		$page_it->moveNext(); 
    	}
    	
    	return array (
    			'pages' => $pages,
    			'description' => text(1734)
    	);
    }
}