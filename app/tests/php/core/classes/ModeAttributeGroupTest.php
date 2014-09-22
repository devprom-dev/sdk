<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";

class ModeAttributeGroupTest extends DevpromTestCase
{
    function setUp()
    {
        parent::setUp();
    }

    function testNewGroupAdded()
    {
    	$user = new User();

    	$user->addAttributeGroup( 'Caption', 'tooltip' );
    	
    	$this->assertContains('Caption', $user->getAttributesByGroup('tooltip'));
    }
}