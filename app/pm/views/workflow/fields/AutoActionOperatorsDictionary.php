<?php

class AutoActionOperatorsDictionary extends FieldDictionary
{
	function __construct()
	{
		parent::__construct(getFactory()->getObject('entity'));
	}
	
 	function getOptions()
	{
	    return array(
	    		array (
	    				'value' => 'is',
	    				'caption' => text(2435)
	    		),
	    		array (
	    				'value' => 'isnot',
	    				'caption' => text(2436)
	    		),
	    		array (
	    				'value' => 'contains',
	    				'caption' => text(2437)
	    		),
	    		array (
	    				'value' => 'notcontains',
	    				'caption' => text(2438)
	    		),
	    		array (
	    				'value' => 'unknown',
	    				'caption' => text(2439)
	    		),
	    		array (
	    				'value' => 'any',
	    				'caption' => text(2440)
	    		),
				array (
                        'value' => 'greater',
                        'caption' => text(2445)
				),
				array (
                        'value' => 'less',
                        'caption' => text(2446)
				),
	    );
	}
}