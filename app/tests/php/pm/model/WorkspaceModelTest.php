<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/Workspace.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/WorkspaceMenu.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/WorkspaceMenuItem.php";

class WorkspaceModelTest extends DevpromDummyTestCase
{
    function testWorkspaceEntity()
    {   
        global $model_factory;
        
        $entity = $this->getMock('Workspace', array('getAll'));
        
        $entity->expects($this->any())->method('getAll')->will( $this->returnValue(
                $entity->createCachedIterator(array(
                        array( 'pm_WorkspaceId' => '1', 'Caption' => 'Управление проектами', 'UID' => 'mgmt' )
                ))
        ));
        
        $model_factory->expects($this->any())->method('createInstance')
            ->with($this->equalTo('Workspace'))->will($this->returnValue( $entity ));
        
        $workspace = $model_factory->getObject('pm_Workspace');
        
        $workspace_it = $workspace->getAll();
        
        $this->assertEquals( 1, $workspace_it->count() );
        
        $this->assertEquals( 'mgmt', $workspace_it->get('UID') );
    }

    function testWorkspaceMenuEntity()
    {   
        global $model_factory;
        
        $workspace = $this->getMock('Workspace', array('getExact'));
        
        $workspace->expects($this->any())->method('getExact')->will( $this->returnValue(
                $workspace->createCachedIterator(array(
                        array( 'pm_WorkspaceId' => '1', 'Caption' => 'Управление проектами', 'UID' => 'mgmt' )
                ))
        ));
        
        $workspace_menu = $this->getMock('WorkspaceMenu', array('getAll'));
        
        $workspace_menu->expects($this->any())->method('getAll')->will( $this->returnValue(
                $workspace_menu->createCachedIterator(array(
                        array( 'pm_WorkspaceMenuId' => '1', 'Caption' => 'Проект', 'UID' => 'project', 'Workspace' => 1 )
                ))
        ));
        
        $model_factory->expects($this->any())->method('createInstance')
            ->will( $this->returnValueMap( array (
                array( 'Workspace', null, $workspace ),
                array( 'WorkspaceMenu', null, $workspace_menu )
        )));
        
        $workspace_menu = $model_factory->getObject('pm_WorkspaceMenu');
        
        $menu_it = $workspace_menu->getAll();
        
        $this->assertEquals( 1, $menu_it->count() );
        
        $this->assertEquals( 'project', $menu_it->get('UID') );
        
        $workspace_it = $menu_it->getRef('Workspace');

        $this->assertEquals( 'mgmt', $workspace_it->get('UID') );
    }

    function testWorkspaceMenuItemEntity()
    {   
        global $model_factory;
        
        $workspace_menu = $this->getMock('WorkspaceMenu', array('getExact'));
        
        $workspace_menu->expects($this->any())->method('getExact')->will( $this->returnValue(
                $workspace_menu->createCachedIterator(array(
                        array( 'pm_WorkspaceMenuId' => '1', 'Caption' => 'Проект', 'UID' => 'project' )
                ))
        ));
        
        $workspace_menu_item = $this->getMock('WorkspaceMenuItem', array('getAll'));
        
        $workspace_menu_item->expects($this->any())->method('getAll')->will( $this->returnValue(
                $workspace_menu_item->createCachedIterator(array(
                        array( 'pm_WorkspaceMenuItemId' => '1', 'Caption' => 'Активности', 'UID' => 'activity', 'WorkspaceMenu' => 1 )
                ))
        ));
        
        $model_factory->expects($this->any())->method('createInstance')
            ->will( $this->returnValueMap( array (
                array( 'WorkspaceMenu', null, $workspace_menu ),
                array( 'WorkspaceMenuItem', null, $workspace_menu_item )
        )));
        
        $workspace_menu_item = $model_factory->getObject('pm_WorkspaceMenuItem');
        
        $item_it = $workspace_menu_item->getAll();
        
        $this->assertEquals( 1, $item_it->count() );
        
        $this->assertEquals( 'activity', $item_it->get('UID') );
        
        $menu_it = $item_it->getRef('WorkspaceMenu');

        $this->assertEquals( 'project', $menu_it->get('UID') );
    }
}