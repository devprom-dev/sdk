<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

use Devprom\CommonBundle\Service\Tooltip\TooltipService;

include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/IssueLinkedIssuesPersister.php";

class TooltipProjectService extends TooltipService
{
	private $baseline;
	
	public function __construct( $class_name, $object_id, $baseline )
	{
    	$this->baseline = $baseline;
		
    	parent::__construct($class_name, $object_id);
	}
	
    public function getData()
    {
    	$uid = new \ObjectUID();
    	
    	return array_merge( parent::getData(), array (
    			'lifecycle' =>
    				array (
    						'name' => translate('Жизненный цикл'),
    						'data' => $this->buildLifecycle( $this->getObjectIt() )
    				),
    			'comments' => 
    				array (
    						'name' => translate('Комментарий'), 
    						'data' => $this->buildComments( $this->getObjectIt() )
    				),
    			'type' => 
    				array (
    						'name' => $this->getObjectIt()->object->getDisplayName(),
    						'uid' => $uid->getObjectUid($this->getObjectIt())
    				)
    	));
    }
    
    protected function extendModel( $object )
    {
    	$object->addPersister( new \AttachmentsPersister() );
    	
    	if ( $object instanceof \Request )
    	{
    		$object->addPersister( new \IssueLinkedIssuesPersister() );
    	}
    	
    	if ( $object instanceof \MetaobjectStatable )
    	{
    		$object->addPersister( new \StateDurationPersister() );
    	}
    }
    
    protected function buildAttributes( $object_it )
    {
    	$data = parent::buildAttributes( $object_it );

     	if ( $this->baseline > 0 )
 	 	{
 	 		$data[] = array (
 	 				'name' => 'Baseline',
 	 				'title' => translate('Бейзлайн'),
 	 				'type' => 'text',
 	 				'text' => getFactory()->getObject('Snapshot')->getExact($this->baseline)->getDisplayName()
 	 		);
 	 	}
 	 	
 	 	if ( $object_it->object instanceof \Request )
 	 	{
 	 		$this->buildRequestAttributes( $data, $object_it );
 	 	}
 	 	
 	 	return $data;
    }   
    
    protected function buildRequestAttributes( &$data, $object_it )
    {
    	// Tasks attribute
    	$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
				array (
						new \FilterAttributePredicate('ChangeRequest', $object_it->getId())
				)
		);
		
		$states = $task_it->getStatesArray();
		
		foreach ( $states as $key => $state )
		{
			if ( !is_array($state) ) continue;
			
			switch ( $state['progress'] )
			{
				case '100%':
					$states[$key]['class'] = 'label-success';
					break;
		
				case '0%':
					$states[$key]['class'] = 'label-important';
					break;
			}
		}
		
		if ( count($states) > 0 )
		{
			$data[] = array (
					'name' => 'Tasks',
					'title' => translate('Задачи'),
					'type' => 'tasks',
					'text' => $states 
			);
		}
		
		// Linked requests attribute
		foreach( $data as $key => $attribute )
		{
			if ( $attribute['name'] == 'Links' )
			{
				unset($data[$key]);
			}
		}
		
		$uid = new \ObjectUID();
		$types = array();
		
		foreach( preg_split('/,/',$object_it->get('LinksWithTypes')) as $item )
		{
			if( $item == '' ) continue;
			list($type_name, $object_id, $type_ref) = preg_split('/:/',$item);
			
			$info = $uid->getUIDInfo($object_it->object->getExact($object_id));
			$types[$type_name][] = $info['uid'].' {'.$info['project'].'} '.$info['caption'].' ('.$info['state_name'].')'; 
		}
		
		foreach( $types as $type_name => $requests )
		{
			$data[] = array (
					'title' => $type_name,
					'text' => join(', ', $requests)
			);
		}
    }
    
    private function buildLifecycle( $object_it )
    {
    	$object = $object_it->object;
    	
     	if ( !is_a($object, 'MetaobjectStatable') ) return array();

		if ( $object->getStateClassName() == '' ) return array();
     	 
		$data = array();
		
		$data['state'] = $this->getAttributeValue($object_it, 'State', ''); 
		
 	 	$reason = getFactory()->getObject('pm_StateObject');
 	 	
 	 	$reason->addSort( new \SortReverseKeyClause() );
	 	 	
 	 	$reason_it = $reason->getByRefArray(
 	 		array ( 'ObjectId' => $object_it->getId(),
 	 			    'ObjectClass' => $object->getStatableClassName() ), 1
 	 	);
	 	 	
	 	if ( $reason_it->count() < 1 ) return $data;
	 	
		$transition_it = $reason_it->getRef('Transition');
		
		if ( $transition_it->count() < 1 ) return $data;
		
		$data['name'] = preg_replace('/%1/', $transition_it->getDisplayName(), text(904));
		
		$data['text'] = $reason_it->getHtml('Comment'); 
		
		return $data;
    }
    
    private function buildComments( $object_it )
    {
 	 	$comment_it = getFactory()->getObject('Comment')->getLastCommentIt( $object_it );
 	 	
 	 	if ( $comment_it->count() < 1 ) return array();

 	 	return array (
 	 			'author' => $comment_it->getHtmlDecoded('AuthorName'),
 	 			'text' => $this->getAttributeValue($comment_it, 'Caption', 'text') 
 	 	);
    }
}