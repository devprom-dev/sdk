<?php

class PMWikiChart extends PMPageChart
{
    function buildIterator()
    {
        $this->getObject()->setRegistry( new WikiPageRegistryContent($this->getObject()) );
        return parent::buildIterator();
    }

    function getGroupDefault()
 	{
 		return 'State';
 	}
 	
	function getGroupFields()
	{
		$fields = parent::getGroupFields();
		
		foreach( $fields as $key => $value )
		{
			if ( $value == 'Content' ) unset($fields[$key]);
			if ( $value == 'ReferenceName' ) unset($fields[$key]);
			if ( $value == 'IsTemplate' ) unset($fields[$key]);
			if ( $value == 'IsDraft' ) unset($fields[$key]);
			if ( $value == 'ContentEditor' ) unset($fields[$key]);
			if ( $value == 'UserField1' ) unset($fields[$key]);
			if ( $value == 'UserField2' ) unset($fields[$key]);
			if ( $value == 'UserField3' ) unset($fields[$key]);
			if ( $value == 'Tasks' ) unset($fields[$key]);
			if ( $value == 'Template' ) unset($fields[$key]);
		}
		
		return $fields;
	}
 	
 	function getAggByFields()
	{
		$fields = parent::getAggByFields();
		
		foreach( $fields as $key => $value )
		{
			if ( $value == 'Content' ) unset($fields[$key]);
			if ( $value == 'ReferenceName' ) unset($fields[$key]);
			if ( $value == 'IsTemplate' ) unset($fields[$key]);
			if ( $value == 'IsDraft' ) unset($fields[$key]);
			if ( $value == 'ContentEditor' ) unset($fields[$key]);
			if ( $value == 'UserField1' ) unset($fields[$key]);
			if ( $value == 'UserField2' ) unset($fields[$key]);
			if ( $value == 'UserField3' ) unset($fields[$key]);
		}
		
		return $fields;
	}
}