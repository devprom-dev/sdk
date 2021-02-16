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
        
        $entity = $this->getMockBuilder(Workspace::class)
            ->setConstructorArgs(array())
            ->setMethods(['getAll'])
            ->getMock();

        $entity->expects($this->any())->method('getAll')->will( $this->returnValue(
            $entity->createCachedIterator(array(
                array( 'pm_WorkspaceId' => '1', 'Caption' => 'Управление проектами', 'UID' => 'mgmt' )
            ))
        ));
        
        $model_factory->expects($this->any())->method('createInstance')
            ->with($this->equalTo('Workspace'))->will($this->returnValue( $entity ));
        
        $workspace_it = $model_factory->getObject('pm_Workspace')->getAll();
        $this->assertEquals( 1, $workspace_it->count() );
        $this->assertEquals( 'mgmt', $workspace_it->get('UID') );
    }

    function testWorkspaceMenuEntity()
    {   
        global $model_factory;

        $workspace = $this->getMockBuilder(Workspace::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact'])
            ->getMock();

        $workspace->expects($this->any())->method('getExact')->will( $this->returnValue(
            $workspace->createCachedIterator(array(
                array( 'pm_WorkspaceId' => '1', 'Caption' => 'Управление проектами', 'UID' => 'mgmt' )
            ))
        ));
        
        $workspace_menu = $this->getMockBuilder(WorkspaceMenu::class)
            ->setConstructorArgs(array())
            ->setMethods(['getAll'])
            ->getMock();

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
        
        $menu_it = $model_factory->getObject('pm_WorkspaceMenu')->getAll();
        $this->assertEquals( 1, $menu_it->count() );
        $this->assertEquals( 'project', $menu_it->get('UID') );
        
        $workspace_it = $model_factory->getObject('Workspace')->getExact($menu_it->get('Workspace'));
        $this->assertEquals( 'mgmt', $workspace_it->get('UID') );
    }

    function testWorkspaceMenuItemEntity()
    {   
        global $model_factory;
        
        $workspace_menu = $this->getMockBuilder(WorkspaceMenu::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact'])
            ->getMock();

        $workspace_menu->expects($this->any())->method('getExact')->will( $this->returnValue(
            $workspace_menu->createCachedIterator(array(
                array( 'pm_WorkspaceMenuId' => '1', 'Caption' => 'Проект', 'UID' => 'project' )
            ))
        ));
        
        $workspace_menu_item = $this->getMockBuilder(WorkspaceMenuItem::class)
            ->setConstructorArgs(array())
            ->setMethods(['getAll'])
            ->getMock();

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
        
        $item_it = $model_factory->getObject('WorkspaceMenuItem')->getAll();
        $this->assertEquals( 1, $item_it->count() );
        $this->assertEquals( 'activity', $item_it->get('UID') );
        
        $menu_it = $model_factory->getObject('WorkspaceMenu')->getExact($item_it->get('WorkspaceMenu'));
        $this->assertEquals( 'project', $menu_it->get('UID') );
    }
}