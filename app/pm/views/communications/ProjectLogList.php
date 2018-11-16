<?php

class ProjectLogList extends PMPageList
{
 	var $participant;
 	
 	function __construct( $object )
 	{
 		parent::__construct($object);
		$this->getObject()->setAttributeType( 'Author', 'REF_IssueAuthorId' );
 	}

    function buildItemsHash($object, $predicates)
    {
        return '';
    }

	function setupColumns()
	{
		$this->object->addAttribute('UserAvatar', '', translate('Автор'), true, false, '', 1);
		$this->object->setAttributeCaption( 'SystemUser', translate('Имя автора') );
		$this->object->setAttributeOrderNum( 'SystemUser', 2 );
		
		parent::setupColumns();
	}
	
	function getSorts()
	{
		$sorts = parent::getSorts();
		
		foreach( $sorts as $key => $sort ) {
			if ( !$sort instanceof SortAttributeClause ) continue;
			if ( in_array($this->getObject()->getAttributeType($sort->getAttributeName()), array('date','datetime')) ) {
				$sorts[$key] = new SortChangeLogRecentClause();
			}
			if ( $sort->getAttributeName() == 'Content' ) {
                $sorts[$key] = new SortChangeLogRecentClause();
            }
		}
		
		return $sorts;
	}

	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch( $attr ) 
		{
			case 'SystemUser':
				if ( $entity_it->getId() == '' ) {
				    if ( $object_it->get('UserName') != '' ) {
                        parent::drawCell( $object_it, 'UserName' );
                    }
                    else {
                        parent::drawCell( $object_it, 'AuthorName' );
                    }
				}
				else {
					parent::drawRefCell( $entity_it, $object_it, $attr );
				}
				break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch( $attr )
		{
		    case 'UserAvatar':
		        
    	    	echo $this->getRenderView()->render('core/UserPicture.php', array (
					'id' => $object_it->get('SystemUser'), 
					'class' => 'user-avatar',
					'title' => $object_it->getRef('SystemUser')->getDisplayName()
				));

				break;
				
		    case 'Content':

                echo '<i class="'.$object_it->getIcon().' hidden-print" style="margin-right: 10px;"></i>';

                $uid = new ObjectUID;
                $anchor_it = $object_it->getObjectIt();
                if ( $anchor_it->getId() != '' && $uid->hasUid( $anchor_it ) )
                {
                    if ( strpos($object_it->get('ChangeKind'), 'deleted') !== false ) {
                        parent::drawCell( $object_it, 'Caption' );
                    }
                    else {
                        $uid->drawUidInCaption($anchor_it);
                    }
                    echo '<br/>';
                }
                else if ($object_it->get('EntityRefName') == 'cms_ExternalUser')
                {
                    echo text(1360) . ': ';
                    parent::drawCell( $object_it, 'Caption' );
                }
                else if ($object_it->get('EntityRefName') == 'pm_ChangeRequest')
                {
                    parent::drawCell( $object_it, 'Caption' );
                }
                else
                    {
                    echo $anchor_it->object->getDisplayName().': ';
                    parent::drawCell( $object_it, 'Caption' );
                }

                if ( strpos($object_it->get('Content'), '[url') !== false && $anchor_it->object instanceof WikiPage) {
                    echo '<br/>'.str_replace('%1', $anchor_it->getHistoryUrl().'&start='.$object_it->getDateTimeFormat('RecordModified'), text(2319));
                }
                else if ( $object_it->get('Content') != '' )
                {
                    echo '<br/>';
                    $field = new FieldWYSIWYG();
                    $field->setObjectIt( $object_it );
                    $field->setValue( $object_it->get('Content') );
                    $field->drawReadonly();
                }
			    if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) {
				    echo $this->getRenderView()->render('core/CommentsIcon.php', array (
							'object_it' => $anchor_it,
							'redirect' => 'donothing'
					));
			    }			    
			    
			    break;
				
			case 'RecordModified':
				
			    $group = $this->getGroup();
				
				if ( $group == 'GroupDays' )
				{
					echo $object_it->getTimeFormat('RecordCreated');
				}
				else
				{
					echo $object_it->getDateTimeFormat('RecordCreated');
				}

				break;

			default:
				parent::drawCell( $object_it, $attr );			
		}
	}

	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'RecordModified' ) 
		{
			return "120";
		}

		if($attr == 'Caption') 
		{
			return "30%";
		}

		if($attr == 'UserAvatar') 
		{
			return "1%";
		}

		if($attr == 'SystemUser') 
		{
			return "160";
		}
		
		return '';
	}

	function getGroupFields() 
	{
		return array('ChangeDate', 'SystemUser', 'Project');
	}
	
	function getColumnFields()
	{
		return array('Caption', 'UserAvatar', 'Content', 'SystemUser', 'RecordModified', 'Project');
	}

	function getItemActions($column_name, $object_it)
	{
		$actions = array();

		$method = new UndoWebMethod($object_it->get('Transaction'), $object_it->get('ProjectCodeName'));
		if ( $method->hasAccess() ) {
			$actions[] = array (
				'name' => $method->getCaption(),
				'url' => $method->getJSCall()
			);
		}

		return $actions;
	}

	function IsNeedToSelect() {
		return false;
	}
}
