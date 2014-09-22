<?php

class UpdateTerminologyTest extends DevpromTestCase
{
    function testTerminologyUpdatedWithGivenTerms()
    {   
        global $model_factory;
        
        $resource_mock = $this->getMock('CustomResource', array('getAll'));
        
        $resource_mock->expects($this->any())->method('getAll')->will( $this->returnValue(
                $resource_mock->createCachedIterator(array(
                        array( 'cms_ResourceId' => '1', 'ResourceValue' => 'девочка с хвостиками'),
                        array( 'cms_ResourceId' => '2', 'ResourceValue' => 'добавлено пожелание')
                ))
        ));
        
        $model_factory->expects($this->once())->method('createInstance')
            ->with($this->equalTo('Resource'))->will($this->returnValue( $resource_mock ));
        
        $mock = new UpdateTerminology;
        
        $result = $mock->getUpdatedTerminology( new Term( '', array(
            'пожелание' => 'история' 
        )));
        
        $this->assertEquals( 1, count($result) );
        
        $this->assertEquals( 'добавлено история', $result['2'] );
    }
    
    function testAddUpdatedTermIntoProjectResources()
    {
        global $model_factory;
        
        // resources overriden by user and persisted in the database
        $custom_mock = $this->getMock('CustomResource', array('search', 'getAll', 'getByRef', 'add_parms'));

        $custom_mock->expects($this->any())->method('getAll')->will( $this->returnValue(
                $custom_mock->createCachedIterator(array(
                        array( 'cms_ResourceId' => '1', 'ResourceValue' => 'девочка с хвостиками'),
                        array( 'cms_ResourceId' => '2', 'ResourceValue' => 'добавлено пожелание')
                ))
        ));
        
        $custom_mock->expects($this->any())->method('search')
            ->will( $this->returnValue( 
                $custom_mock->createCachedIterator(array(
                        array( 'ResourceKey' => '3', 'VPD' => '-1-', 'ResourceValue' => 'удалено пожелание'),
                        array( 'ResourceKey' => '1', 'VPD' => '-2-', 'ResourceValue' => 'добавлено пожелание')
                ))
        ));

        $getbyref_map = array (
                array( 'VPD', '-1-', 
                       $custom_mock->createCachedIterator(array(
                                array( 'ResourceKey' => '1', 'VPD' => '-1-', 'ResourceValue' => 'удалено пожелание'))) 
                     ),
                array( 'VPD', '-2-',
                       $custom_mock->createCachedIterator(array(
                                array( 'ResourceKey' => '2', 'VPD' => '-2-', 'ResourceValue' => 'добавлено пожелание')))
                     )
        );
        
        $custom_mock->expects($this->any())->method('getByRef')->will( $this->returnValueMap($getbyref_map) );

        // register objects factory mock
        $model_factory_map = array (
                array( 'Resource', null, $custom_mock )
        );
        
        $model_factory->expects($this->any())->method('createInstance')->will($this->returnValueMap( $model_factory_map ));
        
        // create installable module
        $updater_mock = $this->getMock('UpdateTerminology', array('buildTerms'));

        $updater_mock->expects($this->any())->method('buildTerms')->will( $this->returnValue( 
                array(new Term( 'пожелание', array( 'добавлено пожелание' => 'добавлена история')) )
        ));
        
        $custom_mock->expects($this->once())->method('add_parms')
            ->with( $this->equalTo( 
                    array(  'ResourceKey' => '2', 
                            'ResourceValue' => 'добавлена история', 
                            'VPD' => '-1-',
                            'OrderNum' => 0)) );
        
        // proceed with installation
        $updater_mock->install();
    }
}