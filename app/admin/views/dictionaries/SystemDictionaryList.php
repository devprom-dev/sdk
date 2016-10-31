<?php

class SystemDictionaryList extends StaticPageList
{
	function IsNeedToDisplay( $attr )
	{
		switch ( $attr )
		{
			case 'Caption':
				return true;
		}

		return false;
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Caption':
				echo '<a href="/admin/dictionaries.php?dict='.$object_it->get('ReferenceName').'">';
				    echo translate($object_it->getDisplayName());
				echo '</a>';
					
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getGroupFields()
	{
		return array();
	}
}