<?php

namespace Devprom\ProjectBundle\Service\Tooltip;

use Devprom\CommonBundle\Service\Tooltip\TooltipService;

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDurationPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/IssueLinkedIssuesPersister.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class TooltipProjectService extends TooltipService
{
	private $editor;
	
	public function __construct( $class_name, $object_id, $extended )
	{
        $this->editor = \WikiEditorBuilder::build();
    	parent::__construct($class_name, $object_id, $extended);
	}
	
    public function getData()
    {
		if ( $this->getObjectIt()->getId() < 1 ) return array();

    	$uid = new \ObjectUID();
    	$uidInfo = $uid->getUIDInfo($this->getObjectIt());

    	$typeAttribute = array_shift($this->getObjectIt()->object->getAttributesByGroup('type'));

    	return array_merge( parent::getData(), array (
    			'comments' =>
    				array (
                        'name' => translate('Комментарий'),
                        'data' => $this->buildComments( $this->getObjectIt() )
    				),
    			'type' => 
    				array (
                        'name' => $this->getObjectIt()->get($typeAttribute) != ''
                            ? $this->getObjectIt()->getRef($typeAttribute)->getDisplayName()
                            : $this->getObjectIt()->object->getDisplayName(),
                        'uid' => $uidInfo['uid'],
                        'url' => $uidInfo['url'],
                        'message' => text(2029)
    				)
    	));
    }

    public function getHtmlRep( $skipTitle = false, $traceAttributes = array() )
    {
        $data = $this->getData();
        $html = '';

        ob_start();
        if ( $skipTitle ) {
            echo $data['type']['uid'];
            echo '<br/>';
            echo '<br/>';
        }

        foreach($data as $key => $section ) {
            switch( $key ) {
                case 'attributes':
                    foreach( $section as $attribute ) {
                        if ( $skipTitle && $attribute['name'] == 'Caption' ) continue;
                        if ( in_array($attribute['name'], $traceAttributes) ) continue;

                        echo '<b>'.$attribute['title'].'</b>: ';
                        switch( $attribute['type'] ) {
                            case 'wysiwyg':
                                echo $attribute['text'];
                                echo '<br/><br/>';
                                break;
                            default:
                                if ( $attribute['name'] == 'Caption' ) {
                                    echo $data['type']['uid'].' ';
                                }
                                echo $attribute['text'];
                                echo '<br/>';
                        }
                    }
                    break;
                case 'lifecycle':
                    echo '<b>'.$section['name'].'</b>: ';
                    echo $section['data']['state'];
                    echo '<br/>';
                    break;
                case 'type':
                    echo '<b>'.translate('Тип').'</b>: ';
                    echo $section['name'];
                    break;
            }
        }

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    protected function extendModel( $object )
    {
    	$object->addPersister( new \AttachmentsPersister() );
    	
    	if ( $object instanceof \MetaobjectStatable && $object->getStateClassName() != '' ) {
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

 	 	if ( $object_it->object instanceof \Request ) {
 	 		$this->buildRequestAttributes( $data, $object_it );
 	 	}

        if ( $object_it->object instanceof \Comment ) {
            $this->buildCommentAttributes( $data, $object_it );
        }

 	 	return $data;
    }   
    
    protected function buildRequestAttributes( &$data, $object_it )
    {
        if ( $this->getExtended() ) {
            $data = array_filter($data, function($item) {
                return $item['name'] != 'OpenTasks';
            });
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
                    $data = array_filter($data, function($item) {
                        return $item['name'] != 'Tasks';
                    });
                    $data[] = array (
                        'name' => 'Tasks',
                        'title' => translate('Задачи'),
                        'type' => 'tasks',
                        'text' => $states
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