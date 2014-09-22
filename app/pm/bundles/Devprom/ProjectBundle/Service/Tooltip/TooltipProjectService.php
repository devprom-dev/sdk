<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

use Devprom\CommonBundle\Service\Tooltip\TooltipService;

include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";

class TooltipProjectService extends TooltipService
{
	private $object_it;
	
	private $baseline;
	
	public function __construct( $class_name, $object_id, $baseline )
	{
    	$object = getFactory()->getObject($class_name);
    	
    	$object->addPersister( new \AttachmentsPersister() );

    	$this->setObjectIt( $object->getExact($object_id) );
    	
    	$this->baseline = $baseline;
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
 	 	
 	 	return $data;
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

 	 	$info = $comment_it->getUserInfo();

 	 	return array (
 	 			'author' => $info['Name'],
 	 			'text' => $this->getAttributeValue($comment_it, 'Caption', 'text') 
 	 	);
    }
}