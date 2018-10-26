<?php
include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."cms/views/FieldDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/workflow/FieldAttributeDictionary.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/Task.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataBuilder.php";

class FieldAttributeDictionaryDummyTest extends DevpromDummyTestCase
{
    function setUp() {
        parent::setUp();
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
            array (
                array ( 'TaskType', null, new \TaskType(new \ObjectRegistryMemory) ),
                array ( 'Request', null, new \Request(new \ObjectRegistryMemory) )
            )
        ));
    }

    function getMetadataBuilders()
    {
        return array_merge( parent::getMetadataBuilders(), array (
            new RequestMetadataBuilder(),
        	new TaskMetadataBuilder()
        ));
    }

    function getModelBuilders() {
        return array_merge(
            parent::getModelBuilders(),
            array(
                new RequestModelExtendedBuilder(),
                new TaskModelExtendedBuilder()
            )
        );
    }

    function testImportantFieldsOnRequestEntity()
    {   
        $field = new FieldAttributeDictionary( new Request() );

        $values = array();
        foreach( $field->getOptions() as $option ) $values[] = $option['value'];
        
        $this->assertFalse( in_array('Fact', $values) );
    }

    function testImportantFieldsOnTaskEntity()
    {
        $field = new FieldAttributeDictionary( new Task() );

        $values = array();
        foreach( $field->getOptions() as $option ) $values[] = $option['value'];
        
        $this->assertFalse( in_array('Release', $values) );
    }
}