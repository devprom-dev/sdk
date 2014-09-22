<?php

include_once SERVER_ROOT_PATH."tests/php/DevpromTestCase.php";
include_once SERVER_ROOT_PATH."core/classes/versioning/Snapshot.php";

class SnapshotModelTest extends DevpromTestCase
{
    protected $entity;
    
    function setUp()
    {
        parent::setUp();
        
        $this->entity = $this->getMock('Snapshot', array('getRegistry'));
        
        $this->entity->expects($this->any())->method('getRegistry')->will( 
        		$this->returnValue(
                	$this->getMock('ObjectRegistrySQL', array('Query'), array($this->entity)) 
        		)
        );
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Snapshot', null, $this->entity )
                ) 
        ));
    }

    function testSingleBranchAllowed()
    {   
        $this->entity->getRegistry()->expects($this->any())->method('Query')->will( 
        		$this->returnValue(
	                $this->entity->createCachedIterator(array(
	                            array( 
	                            		'cms_SnapshotId' => '1', 
	                            		'ObjectId' => '1', 
	                            		'ObjectClass' => 'Requirement', 
						                'Type' => 'branch'  
	                			)
                    ))
        		)
		);
        
        $object = getFactory()->getObject('Snapshot');

        try
        {
            $object->add_parms( 
            		array( 'Type' => 'branch' )
            );
            
            $this->fail('Cyclic reference found');
        }
        catch( Exception $e )
        {
        	$this->assertTrue(true);
        }
    }
}