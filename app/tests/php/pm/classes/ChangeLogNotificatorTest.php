<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."pm/classes/notificators/PMChangeLogNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";

class ChangeLogNotificatorTest extends DevpromDummyTestCase
{
    function setUp()
    {
        parent::setUp();
    }
    
    function testPasswordSkipped()
    {
        $object = new Request();
        
        $object->addAttribute('PasswordField', 'PASSWORD', '', true);
        
        $object_it = $object->getEmptyIterator();
        
        $factory = new PMChangeLogNotificator();
        
        $this->assertFalse($factory->IsAttributeVisible('PasswordField', $object_it, 'added'));
    }

    function testCaptionVisible()
    {
        $object = new Request();
        
        $object_it = $object->getEmptyIterator();
        
        $factory = new PMChangeLogNotificator();
        
        $this->assertTrue($factory->IsAttributeVisible('Caption', $object_it, 'added'));
    }
}