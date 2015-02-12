<?php

class ParticipantList extends PMPageList
{
 	var $currency_short;
 	
	function __construct($object) 
	{
		parent::__construct($object);
	}
	
	function setupColumns()
	{
		$this->object->addAttribute('UserAvatar', '', translate('Фото'), true, false, '', 1);
		
		parent::setupColumns();
	}
	
	function IsNeedToDelete( ) { return false; }
	
	function drawRefCell( $entity_it, $object_it, $attr )
	{
		$view = $this->getRenderView();
		
		switch( $attr )
		{
			case 'ParticipantRole':
				
				if ( $object_it->getId() < 1 )
				{
					echo text(1864);
					
					return;
				}
			    
			    $project_it = getSession()->getProjectIt();
			    
				$part_role_it = $object_it->getRef('ParticipantRole');
	
				while( !$part_role_it->end() )
				{
				    if ( $part_role_it->get('VPD') != $project_it->get('VPD') )
				    {
				        $part_role_it->moveNext();
				        
				        continue;
				    }
				    
				    $actions = array();
				    
					$role_it = $part_role_it->getRef('ProjectRole');
		
					$title = $role_it->getDisplayName().' ('.$part_role_it->get('Capacity').' '.translate('ч.').')';
					
					$method = new ObjectModifyWebMethod($part_role_it);
		
					if ( $method->hasAccess() && $object_it->get('IsActive') <> 'N' )
					{
						$method->setRedirectUrl('donothing');
						
						$actions[] = array(
								'name' => translate('Изменить'), 
								'url' => $method->getJSCall() 
						);
					}
					
					$method = new DeleteObjectWebMethod($part_role_it);
			
					if ( $method->hasAccess() )
					{
						$method->setRedirectUrl('donothing');
						
						$actions[] = array();
						
					    $actions[] = array(
						    'name' => $method->getCaption(), 
					    	'url' => $method->getJSCall() 
					    );
					}
					
					echo $view->render('core/TextMenu.php', array (
					        'title' => $title,
					        'items' => array_merge( array(), $actions )
					));

					echo '<div class="clear-fix"></div>';
					
					$part_role_it->moveNext();
				}
				
				break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function drawCell( $object_it, $attr )
	{
		$session = getSession();
		
		switch( $attr )
		{
		    case 'Email':
		        
		        echo '<a href="mailto:'.$object_it->get('Email').'">'.$object_it->get('Email').'</a>';
		        
		        break;
		        
		    case 'Capacity':

				$capacity = $object_it->get('Capacity');
			
    			if ( !is_null($capacity) )
    			{
        			if ( $capacity > 0 )
        			{
            			echo '<div class="line">';
            				echo $capacity.' '.translate('ч.');
            			echo '</div>';
        			}
        			else
        			{
        				echo '<div class="note" style="width:160px;">';
        					echo '<div class="line">';
        						echo text(833);
        					echo '</div>';
        				echo '</div>';
        			}
    			}
		        
		        break;
		        
		    case 'UserAvatar':
		        
    	    	echo $this->getTable()->getView()->render('core/UserPicture.php', array (
					'id' => $object_it->getId(), 
					'class' => 'participant-avatar', 
					'image' => 'userpics-middle',
					'title' => $object_it->getDisplayName()
				));
		        
		        break;
		        
		    default:
		        
		        parent::drawCell( $object_it, $attr );
		}
	}
	
	function getRowColor( $object_it, $attr )
	{
		return $object_it->get('IsActive') == 'N' ? 'silver' : 'black';
	}
	
	function getColumnWidth( $attr ) 
	{
	    switch ( $attr  )
	    {
	        case 'UserAvatar':
	            
	            return "1%";

	        case 'Capacity':
	            
	            return '1%';
	                    
    	    case 'ParticipantRole':
	            
	            return '1%';

    	    case 'Project':
	            
	            return '1%';
	            
    	    case 'Email':
	            
	            return '1%';
	            
	        default:
    	        
    	        return parent::getColumnWidth( $attr );
	    }
	}
	
	function IsNeedToDisplayOperations()
	{
		return true;
	}
	
	function getItemActions( $column_name, $object_it ) 
	{
		return $object_it->getId() < 1 
				? $this->getInvitedActions($object_it) 
				: $this->getUserActions($object_it);
	}

	function getInvitedActions( $object_it )
	{
		$actions = array();
		
		$invitation_it = getFactory()->getObject('Invitation')->getExact($object_it->get('Invitation'));
		
		$method = new DeleteObjectWebMethod($invitation_it);
		
		if ( $method->hasAccess() )
		{
			$actions[] = array (
					'name' => $method->getCaption(),
					'url' => $method->getJSCall() 
			);
		}
		
		return $actions;
	}
	
	function getUserActions( $object_it )
	{
        $participant = getFactory()->getObject('Participant');
		
		if ( $object_it->get('Participant') > 0 )
		{
		    $participant_it = $object_it->getRef('Participant');
		    
			$method = new ObjectModifyWebMethod($participant_it);

			if ( $method->hasAccess() || $participant_it->getId() == getSession()->getParticipantIt()->getId() )
			{
				$method->setRedirectUrl('donothing');
				
				$actions[] = array(
						'name' => translate('Изменить'), 
						'url' => $method->getJSCall() 
				);

				$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_ParticipantRole'));

				if ( $method->hasAccess() && $participant_it->get('IsActive') <> 'N' )
				{
        			if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
        			
        			$actions[] = array(
        			    'url' => $method->getJSCall( array (
			        			    		'Participant' => $participant_it->getId(),
			        			    		'Project' => getSession()->getProjectIt()->getId()
	        			    			)), 
        	        	'name' => translate('Назначить роль')
        			);
        		}
    		}
		}
		elseif ( getFactory()->getAccessPolicy()->can_create($participant) )
		{
			$method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Participant'));
			
            $role_it = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
		            		array (
		            				new ProjectRoleInheritedFilter(),
		            				new FilterBaseVpdPredicate()
		            		)
            		);
            
            while( !$role_it->end() )
            {
    			$actions[] = array(
    			    'url' => $method->getJSCall( array (
		    			    		'SystemUser' => $object_it->getId(),
		    			    		'ProjectRole' => $role_it->getId()
	    			    		)), 
    	        	'name' => str_replace('%1', $role_it->getDisplayName(), text(1369))
    			);
                
        		$role_it->moveNext(); 
            }
		}
		
		if ( getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('pm_AccessRight')) )
		{
			if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
			
		    $actions[] = array(
		        'url' => getSession()->getApplicationUrl().'participants/rights?user='.$object_it->getId().'&area=mgmt', 
	        	'name' => translate('Права доступа')
		    );
		}
				
		return $actions;		
	}
	
	function getGroupFields() 
	{
		$fields = parent::getGroupFields();
		
		unset($fields[array_search('ParticipantRole', $fields)]);
		
		$fields[] = 'ProjectRole';
		
		return $fields;
	}
}
