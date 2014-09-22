<?php

class UserList extends PageList
{
	function getPredicates( $values )
	{
		return array(
    		new UserStatePredicate( $values['state'] ),
    		new UserSystemRolePredicate( $values['role'] )
		);
	}
	
	function setupColumns()
	{
		parent::setupColumns();
		
		foreach( $this->object->getAttributes() as $attribute => $data )
		{
			$this->object->setAttributeVisible( $attribute, in_array($attribute, array('Caption', 'Description', 'Email', 'Photo', 'GroupId')) );
		}
	}

	function drawCell( $object_it, $attr )
	{
	    switch( $attr )
	    {
    	    case 'Photo':
    	    	
    	    	echo $this->getTable()->getView()->render('core/UserPicture.php', array (
					'id' => $object_it->getId(), 
					'class' => 
					'participant-avatar', 
					'image' => 'userpics-middle',
					'title' => $object_it->getDisplayName()
				));
		        
		        break;
		        
    	    case 'Caption':
    	        
    	        echo $object_it->getRefLink();
    	        
    	        break;
    	        
    	    default:
    	        
    	        parent::drawCell( $object_it, $attr );
	    }
	}

	function IsNeedToDelete( ) { return false; }

	function getGroupDefault()
	{
		return '';
	}
	
    function getNoItemsMessage()
	{
	    return text(1296);
	}
	
	function getColumnWidth( $attr )
	{
	    switch ( $attr )
	    {
	        case 'Photo':
	            return '1%';
	            
	        default:
	            return parent::getColumnWidth( $attr );
	    }
	}
}
