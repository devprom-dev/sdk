<?php

class ProjectTemplateList extends PageList
{
	function getGroupFields()
	{
		return array();
	}
	
	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'FileName':
				echo '<a target="_blank" href="/templates/project/'.$object_it->get('FileName').'">'.$object_it->get('FileName').'</a>';
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
}
