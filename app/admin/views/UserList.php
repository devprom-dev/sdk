<?php

class UserList extends PageList
{
	function getPredicates( $values )
	{
		return array(
    		new UserAccessPredicate( $values['state'] ),
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
				$method = new ObjectModifyWebMethod($object_it);
				if ( $method->hasAccess() ) {
					echo '<a href="'.$method->getJSCall().'">'.$object_it->getDisplayName().'</a>';
				}
				else {
					parent::drawCell( $object_it, $attr );
				}
    	        break;

			case 'LastActivityDate':
				if ( $object_it->get($attr) == '' ) {
					echo text(2060);
				} else {
					parent::drawCell( $object_it, $attr );
				}
				break;

    	    default:
    	        parent::drawCell( $object_it, $attr );
	    }
	}

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

	function getColumnFields()
	{
		return array_merge(
			parent::getColumnFields(),
			array (
				'IsReadonly'
			)
		);
	}

	function getRenderParms()
	{
		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
		if ( !$license_it->allowCreate($this->getObject()) ) {
			$message = text(2196);
		}

		return array_merge(
			parent::getRenderParms(),
			array (
				'message' => $message
			)
		);
	}
}
