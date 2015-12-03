<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectSet.php";
include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilder.php";

class CustomizableObjectBuilderTest extends CustomizableObjectBuilder
{
    public function build( CustomizableObjectRegistry & $set )
    {
    	$set->add( 'Request', 'request:RequestSubType', 'Some text' );
    }
}
    	
class CusomizableObjectModelTest extends DevpromDummyTestCase
{
    function getBuilders()
    {
        return array_merge( parent::getBuilders(), array (
        		array( 'CustomizableObjectBuilder', array (new CustomizableObjectBuilderTest(getSession())) )
        ));
    }
    
    function setUp()
    {
        global $model_factory;
        
        parent::setUp();
        
        // entity mocks

        $entity = $this->getMock('CustomizableObjectSet', array('getExact', 'cacheStates'));

        $model_factory->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'CustomizableObjectSet', null, $entity )
                ) 
        ));
    }
    
    function testRequestIsClosedByModification()
    {
        global $model_factory;
        
        $object = $model_factory->getObject('CustomizableObjectSet');
        
        $object_it = $object->getAll();
        
        $this->assertGreaterThan(0, $object_it->count());
        
        $this->assertEquals( 'RequestSubType', array_pop(array_slice(preg_split('/:/', $object_it->getId()), 1)) );
        
        $this->assertEquals( 'Some text', $object_it->get('Caption') );
    }
}