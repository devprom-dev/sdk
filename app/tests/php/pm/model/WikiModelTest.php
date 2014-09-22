<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";

include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPage.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/PMWikiPage.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiSectionNumberingTrigger.php"; 
include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiSectionNumberingTrigger.php"; 

class WikiModelTest extends DevpromDummyTestCase
{
    protected $entity;
    
    function setUp()
    {
        global $model_factory;
        
        parent::setUp();
        
        // entity mocks
        
        $this->entity = $this->getMock('WikiPage', array('getExact', 'cacheStates', 'getByRefArray', 'getRegistry'));
        
        $this->entity->expects($this->any())->method('cacheStates')->will( $this->returnValue(
                $this->entity->createCachedIterator(array()) 
        ));

        $this->registry = $this->getMock('ObjectRegistrySQL', array('Query'), array($this->entity));

        $this->entity->expects($this->any())->method('getRegistry')->will( $this->returnValue(
                $this->registry 
        ));
        
        $model_factory->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'WikiPage', null, $this->entity ),
                        array ( 'PMWikiPage', null, $this->entity ),
                        array ( get_class($this->entity), null, $this->entity )
                ) 
        ));
    }

    function testParentPath()
    {
        global $model_factory;
        
        $this->registry->expects($this->any())->method('Query')->will( $this->returnValue(
                $this->entity->createCachedIterator(array()) 
        ));
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                        array ( '2', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '2', 'Caption' => 'Child page', 'ParentPage' => '1' )
                        )))
                ) 
        ));
        
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->stringContains("t.ParentPath = ',1,2,'")
        );

        $trigger = new WikiParentPathTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'WikiPageId' => '2', 
                                    'ParentPage' => '' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'WikiPageId' => '2', 
                                    'ParentPage' => '1' 
                                 )
                        ))
        );
    }
    
    function testParentPathLong()
    {
        global $model_factory;
        
        $this->registry->expects($this->any())->method('Query')->will( $this->returnValue(
                $this->entity->createCachedIterator(array()) 
        ));
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                        array ( '2', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '2', 'Caption' => 'Child page', 'ParentPage' => '1' )
                        ))),
                        array ( '3', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '3', 'Caption' => 'Child page 2', 'ParentPage' => '2' )
                        )))
                ) 
        ));
        
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->stringContains("t.ParentPath = ',1,2,3,'")
        );

        $trigger = new WikiParentPathTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'WikiPageId' => '3', 
                                    'ParentPage' => '' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'WikiPageId' => '3', 
                                    'ParentPage' => '2' 
                                 )
                        ))
        );
    }
    
    function testParentPathNewPage()
    {
        global $model_factory;

        $this->registry->expects($this->any())->method('Query')->will( $this->returnValue(
                $this->entity->createCachedIterator(array()) 
        ));
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                        array ( '2', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '2', 'Caption' => 'Child page', 'ParentPage' => '1' )
                        )))
                ) 
        ));
        
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->stringContains("t.ParentPath = ',1,2,'")
        );

        $trigger = new WikiParentPathTrigger();

        $trigger->add( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'WikiPageId' => '2', 
                                    'ParentPage' => '1' 
                                 )
                        ))
        );
    }
    
    function testCyclicReference0()
    {   
        global $model_factory;
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                ) 
        ));
        
        $page = $model_factory->getObject('WikiPage');

        try
        {
            $page->modify_parms( '1', array( 'ParentPage' => '1' ) );
            
            $this->fail('Cyclic reference found');
        }
        catch( Exception $e )
        {
            $this->assertTrue(true);
        }
    }

    function CyclicReference1()
    {   
        global $model_factory;
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                        array ( '2', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '2', 'Caption' => 'Child page', 'ParentPage' => '1' )
                        ))) 
                ) 
        ));
        
        $page = $model_factory->getObject('WikiPage');

        try
        {
            $page->modify_parms( '1', array( 'ParentPage' => '2' ) );
            
            $this->fail('Cyclic reference found');
        }
        catch( Exception $e )
        {
            $this->assertTrue(true);
        }
    }

    function testCyclicReference2()
    {   
        global $model_factory;
        
        $this->entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '1', 'Caption' => 'Parent page', 'ParentPage' => '' )
                        ))),
                        array ( '2', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '2', 'Caption' => 'Child page', 'ParentPage' => '1' )
                        ))),
                        array ( '3', $this->entity->createCachedIterator(array(
                            array( 'WikiPageId' => '3', 'Caption' => 'Sub-child page', 'ParentPage' => '2' )
                        ))) 
                ) 
        ));
        
        $page = $model_factory->getObject('WikiPage');

        try
        {
            $page->modify_parms( '1', array( 'ParentPage' => '3' ) );
            
            $this->fail('Cyclic reference found');
        }
        catch( Exception $e )
        {
            $this->assertTrue(true);
        }
    }
    
    function entityDataCallback( $object_id )
    {
    	switch ( $object_id ) 
    	{
    	    case '1':
		    	return $this->entity->createCachedIterator(array(
					array( 
		            	'WikiPageId' => '1', 
		                'Caption' => 'Parent page', 
		                'ParentPage' => '',
		                'SectionNumber' => '1' 
		            )
		    	));
		    	
    	    case '2':
		    	return $this->entity->createCachedIterator(array(
					array( 
		            	'WikiPageId' => '2', 
		                'Caption' => 'Child page', 
		                'ParentPage' => '',
		                'SectionNumber' => '' 
		            )
		    	));

   	    	case '3':
		    	return $this->entity->createCachedIterator(array(
					array( 
		            	'WikiPageId' => '3', 
		                'Caption' => 'Third page', 
		                'ParentPage' => '',
		                'SectionNumber' => '' 
		            )
		    	));
		    	
    	    default:
    	    	return $this->entity->createCachedIterator(array());
    	}
    }

    function entityQueryCallback( $parms )
    {
    	foreach( $parms as $parameter )
    	{
    		if ( is_a($parameter, 'FilterAttributePredicate') && $parameter->getAttribute() == 'ParentPage' )
    		{
    			switch ( $parameter->getValue() )
    			{
    			    case '1':
    			    	return $this->entity->createCachedIterator(array(
		                        array( 
		                                'WikiPageId' => '3', 
		                                'Caption' => 'Child page', 
		                                'ParentPage' => '1',
		                                'SectionNumber' => '' 
		                             ),
		                        array( 
		                                'WikiPageId' => '2', 
		                                'Caption' => 'Child page', 
		                                'ParentPage' => '1',
		                                'SectionNumber' => '' 
		                             )
		                    ));
    			    	
    			    default:
    			    	return $this->entity->createCachedIterator(array());
    			}
    		}
    	}
    }
}