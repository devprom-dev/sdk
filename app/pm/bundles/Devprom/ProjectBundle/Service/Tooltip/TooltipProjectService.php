<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

use Devprom\CommonBundle\Service\Tooltip\TooltipService;

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDetailsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDurationPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/IssueLinkedIssuesPersister.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class TooltipProjectService extends TooltipService
{
	private $baseline;
	private $editor;
	
	public function __construct( $class_name, $object_id, $extended, $baseline )
	{
    	$this->baseline = $baseline;
        $this->editor = \WikiEditorBuilder::build();

    	parent::__construct($class_name, $object_id, $extended);
	}
	
    public function getData()
    {
		if ( $this->getObjectIt()->getId() < 1 ) return array();

    	$uid = new \ObjectUID();
    	return array_merge( parent::getData(), array (
    			'lifecycle' =>
    				array (
    						'name' => translate('Состояние'),
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
    						'uid' => $uid->getUIDIcon($this->getObjectIt())
    				)
    	));
    }
    
    protected function extendModel( $object )
    {
    	$object->addPersister( new \AttachmentsPersister() );
    	
    	if ( $object instanceof \MetaobjectStatable && $object->getStateClassName() != '' ) {
			$object->addPersister( new \StateDetailsPersister() );
    		$object->addPersister( new \StateDurationPersister() );
    	}

        if ( $object instanceof \Request ) {
    	    $builder = new \RequestModelExtendedBuilder();
            $builder->build($object);
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
                'type' => 'varchar',
                'text' => getFactory()->getObject('Snapshot')->getExact($this->baseline)->getDisplayName(),
                'group' => TOOLTIP_GROUP_ADDITIONAL
 	 		);
            $data = array_filter($data, function($value) {
                return $value['name'] != 'DocumentVersion';
            });
 	 	}
 	 	
 	 	if ( $object_it->object instanceof \Request ) {
 	 		$this->buildRequestAttributes( $data, $object_it );
 	 	}

        if ( $object_it->object instanceof \Comment ) {
            $this->buildCommentAttributes( $data, $object_it );
        }

        $state = $this->buildLifecycle($this->getObjectIt());
     	if ( count($state) > 0 ) {
            $data[] = $state;
        }

 	 	return $data;
    }   
    
    protected function buildRequestAttributes( &$data, $object_it )
    {
        if ( $this->getExtended() ) {
            foreach( $data as $key => $field ) {
                if ( in_array($field['name'], array('OpenTasks')) ) {
                    unset($data[$key]);
                }
            }
        }
        else {
            // Tasks attribute
            $task = getFactory()->getObject('Task');
            if ( getFactory()->getAccessPolicy()->can_read($task) ) {
                $this->extendModel($task);
                $task_it = $task->getRegistry()->Query(
                    array (
                        new \FilterAttributePredicate('ChangeRequest', $object_it->getId())
                    )
                );

                $states = $task_it->getStatesArray();
                foreach ( $states as $key => $state )
                {
                    if ( !is_array($state) ) continue;
                    switch ( $state['progress'] ) {
                        case '100%':
                            $states[$key]['class'] = 'label-success';
                            break;
                        case '0%':
                            $states[$key]['class'] = 'label-important';
                            break;
                    }
                }
                if ( count($states) > 0 ) {
                    $data[] = array (
                        'name' => 'Tasks',
                        'title' => translate('Задачи'),
                        'type' => 'tasks',
                        'text' => $states,
                        'group' => TOOLTIP_GROUP_TRACE
                    );
                }
            }
        }


		// Linked requests attribute
		foreach( $data as $key => $attribute )
		{
			if ( $attribute['name'] == 'Links' ) {
				unset($data[$key]);
			}
            if ( $attribute['name'] == 'DueWeeks' && $object_it->get('DeliveryDate') == '' ) {
                unset($data[$key]);
            }
		}
		
		$uid = new \ObjectUID();
		$types = array();
		
		foreach( preg_split('/,/',$object_it->get('LinksWithTypes')) as $item )
		{
			if( $item == '' ) continue;
			list($type_name, $object_id, $type_ref) = preg_split('/:/',$item);
			
			$info = $uid->getUIDInfo($object_it->object->getExact($object_id), true);
			$types[$type_name][] = '<a href="'.$info['url'].'">['.$info['project'].':'.$info['uid'].']</a> '.$info['caption'].' ('.$info['state_name'].')';
		}
		
		foreach( $types as $type_name => $requests )
		{
			$data[] = array (
                'title' => $type_name,
                'text' => join(', ', $requests),
                'group' => TOOLTIP_GROUP_TRACE
			);
		}
    }

    protected function buildCommentAttributes( &$data, $object_it )
    {
        $uid = new \ObjectUID();
        $anchor_it = $object_it->getAnchorIt();

        $data[] = array (
            'name' => 'ObjectId',
            'title' => translate('Артефакт'),
            'text' => $uid->getUidWithCaption($anchor_it),
            'group' => 0
        );
    }
    
    private function buildLifecycle( $object_it )
    {
    	$object = $object_it->object;
    	
     	if ( ! $object instanceof MetaobjectStatable ) return array();
		if ( $object->getStateClassName() == '' ) return array();
     	 
		$data = array(
		    'title' => translate('Состояние'),
            'state' => $this->getAttributeValue($object_it, 'State', '')
        );

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
		$data['group'] = TOOLTIP_GROUP_WORKFLOW;
        $data['type'] = 'state';

		return $data;
    }
    
    private function buildComments( $object_it )
    {
 	 	$comment_it = getFactory()->getObject('Comment')->getLastCommentIt( $object_it );
 	 	
 	 	if ( $comment_it->count() < 1 ) return array();

 	 	return array (
            'author' => $comment_it->getHtmlDecoded('AuthorName'),
            'text' => $this->getAttributeValue($comment_it, 'Caption', 'wysiwyg')
 	 	);
    }

    protected function getAttributeValue( $object_it, $attribute, $type )
    {
        switch ( $type ) {
            case 'wysiwyg':
                $parser = $this->editor->getHtmlParser();
		        $parser->displayHints(true);
		        $parser->setObjectIt($object_it);
                return $parser->parse($object_it->getHtmlDecoded($attribute));

            default:
                return parent::getAttributeValue($object_it, $attribute, $type);
        }
    }
}