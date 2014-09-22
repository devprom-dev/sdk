<?php

class ProjectLogList extends PMStaticPageList
{
 	var $participant;
 	
	function setupColumns()
	{
		$this->object->addAttribute('UserAvatar', '', translate('Пользователь'), true, false, '', 1);
	
		$this->object->setAttributeOrderNum( 'SystemUser', 2 );
		
		$this->object->setAttributeCaption( 'SystemUser', translate('Имя') );
		
		parent::setupColumns();
	}
	
	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;

		switch( $attr ) 
		{
			case 'Caption':
				
			    $change_kind = $object_it->getImage();
				
				echo '<i class="'.$change_kind.'"></i> &nbsp; ';

				$anchor_it = $object_it->getObjectIt();
				
				$uid = new ObjectUID;

				if ( $anchor_it->getId() != '' )
				{
    				if ( $uid->hasUid( $anchor_it ) )
    				{
    				    $uid->drawUidIcon( $anchor_it );
    				    
    				    echo ' ';
    				}
    				else
    				{
    				    echo $anchor_it->object->getDisplayName();
    				    
    				    echo ': ';
    				}
				} else if ($object_it->get('EntityRefName') == 'cms_ExternalUser') {
                    echo text(1360) . ': ';
                } else {
				    echo $anchor_it->object->getDisplayName().': ';
				}
				
				drawMore($object_it, 'Caption', 20);

				break;
				
		    case 'UserAvatar':
		        
    	    	echo $this->getTable()->getView()->render('core/UserPicture.php', array (
					'id' => $object_it->get('SystemUser'), 
					'class' => 'user-avatar',
					'title' => $object_it->getRef('SystemUser')->getDisplayName()
				));

				break;
				
		    case 'Content':

			    drawMore($object_it, 'Content', 20);
				
			    if ( strpos($object_it->get('ChangeKind'), 'commented') !== false ) 
			    {
			    	$anchor_it = $object_it->getObjectIt();
			    
				    echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
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
		return array('ChangeDate', 'SystemUser', 'EntityName', 'Project');
	}
	
	function getColumnFields()
	{
		return array('Caption', 'UserAvatar', 'EntityName', 'Content', 'SystemUser', 'RecordModified', 'Project');
	}
}
