<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromSDLCTestCase.php";

include_once SERVER_ROOT_PATH."cms/views/FieldDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/workflow/FieldAttributeDictionary.php";

include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataBuilder.php";

include_once SERVER_ROOT_PATH."pm/classes/tasks/Task.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataBuilder.php";


class FieldAttributeDictionarySDLCTest extends DevpromSDLCTestCase
{
    function getMetadataBuilders()
    {
        return array_merge( 
        		parent::getMetadataBuilders(), 
        		array (
		            new RequestMetadataBuilder(),
		        	new TaskMetadataBuilder()
        		)
        );
    }
    
    function testImportantFieldsOnRequestEntity()
    {   
        global $model_factory;
        
        $field = new FieldAttributeDictionary( new Request() );

        $values = array();
        
        foreach( $field->getOptions() as $option ) $values[] = $option['value'];
        
        $this->assertContains( 'Fact', $values );
        
        $this->assertContains( 'Tasks', $values );
    }

    function testImportantFieldsOnTaskEntity()
    {   
        global $model_factory;
        
        $field = new FieldAttributeDictionary( new Task() );

        $values = array();
        
        foreach( $field->getOptions() as $option ) $values[] = $option['value'];
        
        $this->assertContains( 'Release', $values );
    }
}