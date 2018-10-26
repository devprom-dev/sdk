<?php

class ActivityList extends StaticPageList
{
	function getPredicates( $values )
	{
		return array(
		    new FilterModifiedAfterPredicate($values['modified'])
		);
	}
	
	function IsNeedToDisplay( $attr )
	{
		switch ( $attr )
		{
			case 'RecordModified':
			case 'SystemUser':
				return true;

			case 'Author':
				return false;
				
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}

 	function getImage( $object_it )
 	{
		switch ($object_it->get('ChangeKind')) 
		{
			case 'added': 
				$change_kind = 'icon-plus-sign'; 
				break;
				
			case 'modified': 
				$change_kind = 'icon-edit'; 
				break;

			case 'deleted': 
				$change_kind = 'icon-minus-sign'; 
				break;
				
			case 'commented': 
				$change_kind = 'icon-comment'; 
				break;

			case 'comment_modified': 
				$change_kind = 'icon-comment'; 
				break;

			case 'comment_deleted': 
				$change_kind = 'icon-comment'; 
				break;
		}
		
		return $change_kind;
 	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch( $attr )
		{
			case 'Caption':
                $change_kind = $this->getImage($object_it);
                echo '<i class="'.$change_kind.'"></i> &nbsp; ';

                echo $object_it->get('EntityName').': ';
                echo $object_it->getDisplayName();
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

		if($attr == 'SystemUser') 
		{
			return "15%";
		}

		return '';
	}

	function getColumnsOrder() 
	{
		return array('RecordModified', 'Caption', 'ClassName', 'Content', 'SystemUser');
	}

	function getGroupFields() 
	{
		return array('ChangeDate', 'SystemUser', 'EntityName');
	}
	
	function getColumnFields()
	{
		return array('Caption', 'EntityName', 'Content', 'SystemUser', 'RecordModified');
	}

	function getGroupDefault()
    {
        return 'none';
    }
}