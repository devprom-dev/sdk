<?php

class AutoActionAttributesDictionary extends FieldDictionary
{
	private $autoaction = null;
	
	function __construct($object)
	{
		$this->autoaction = $object;
		parent::__construct(getFactory()->getObject('entity'));
	}
	
 	function getOptions()
	{
	    $subject = getFactory()->getObject($this->autoaction->getSubjectClassName());
    	$options = array();
    	
    	foreach( $this->autoaction->getConditionAttributes() as $attribute ) {
    		if ( $subject->getAttributeType($attribute) == '' ) continue;
    		$title = translate($subject->getAttributeUserName($attribute));
    		if ( in_array('astronomic-time', $subject->getAttributeGroups($attribute)) ) {
                $title .= ', ' . translate('ч.');
            }
    		$options[] = array (
                'value' => $attribute,
                'caption' => $title
    		);
    	}

		usort($options, function($left, $right) {
			return strcmp(translate($left['caption']), translate($right['caption']));
		});

    	return $options;
	}
}