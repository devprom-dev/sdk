<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuSettingsBuilder.php";

class ModuleProjectsBuilder extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
    	$this->buildProjectManagement($object);
    	
    	$this->buildSettings($object);
    }
    
    public function buildProjectManagement( ModuleRegistry & $object )
    {
    	$modules = array();
    	
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-log';
        $item['Caption'] = translate('Активности');
        $item['AccessEntityReferenceName'] = 'ObjectChangeLog';
        $item['Url'] = 'project/log';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-spenttime';
        $item['Caption'] = translate('Затраченное время');
        $item['AccessEntityReferenceName'] = 'pm_Activity';
        $item['Url'] = 'participants/spenttime';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-question';
        $item['Caption'] = translate('Вопросы');
        $item['AccessEntityReferenceName'] = 'pm_Question';
        $item['Url'] = 'project/question';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-blog';
        $item['Caption'] = translate('Блог');
        $item['AccessEntityReferenceName'] = 'BlogPost';
        $item['Url'] = 'project/blog';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-knowledgebase';
        $item['Caption'] = translate('База знаний');
        $item['AccessEntityReferenceName'] = 'ProjectPage';
        $item['Url'] = 'knowledgebase/tree';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-reports';
        $item['Caption'] = translate('Все отчеты');
        $item['Description'] = text(1824);
        $item['AccessEntityReferenceName'] = 'cms_Report';
        $item['Url'] = 'project/reports';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-plan-hierarchy';
        $item['Caption'] = translate('План');
        $item['AccessEntityReferenceName'] = 'pm_Version';
        $item['Url'] = 'plan/hierarchy';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-plan-milestone';
        $item['Caption'] = translate('Вехи');
        $item['AccessEntityReferenceName'] = 'pm_Milestone';
        $item['Url'] = 'plan/milestone';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-list';
        $item['Caption'] = text(1355);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/list';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-backlog';
        $item['Caption'] = translate('Баклог');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/list';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-board';
        $item['Caption'] = text(1340);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/board';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-trace';
        $item['Caption'] = translate('Трассировка пожеланий');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/trace';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-chart';
        $item['Caption'] = text(1851);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/chart';
        
        $modules[] = $item;

        if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
        {
	        // tasks modules
	        $plugin = translate('Задачи');
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-list';
	        $item['Caption'] = text(1356);
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/list';
	        
	        $modules[] = $item;
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-board';
	        $item['Caption'] = translate('Доска задач');
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/board';
	        
	        $modules[] = $item;
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-trace';
	        $item['Caption'] = translate('Трассировка задач');
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/trace';
	        
	        $modules[] = $item;
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-chart';
	        $item['Caption'] = text(1850);
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/chart';
	        
	        $modules[] = $item;
        }
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'features-list';
        $item['Caption'] = translate('Функции');
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/list';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'features-trace';
        $item['Caption'] = translate('Трассировка функций');
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/trace';
        
        $modules[] = $item;
        
        /*
        $item = array();
        
        $item['cms_PluginModuleId'] = 'features-chart';
        $item['Caption'] = text(1339);
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'features/chart';

        $modules[] = $item;
        */
        
        foreach( $modules as $module )
        {
        	$module['Area'] = FUNC_AREA_MANAGEMENT;
        	
            $object->addModule( $module );
        }
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-import';
        $item['Caption'] = translate('Импорт');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['AccessType'] = ACCESS_CREATE;
        $item['Url'] = 'issues/board';
        
        $object->addModule( $item );
    }
    
    function buildSettings( ModuleRegistry $object )
    {
        $modules = array();
        
        // project settings modules
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-settings';
        $item['Caption'] = translate('Проект');
        $item['Description'] = text(1812);
        $item['AccessEntityReferenceName'] = 'pm_Project';
        $item['AccessType'] = ACCESS_MODIFY;
        $item['Url'] = 'project/settings';
        
        $modules[] = $item;

        $item['cms_PluginModuleId'] = 'navigation-settings';
        $item['Caption'] = text(1807);
        $item['AccessEntityReferenceName'] = 'pm_CustomReport';
        $item['Url'] = 'menu';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'participants';
        $item['Caption'] = translate('Участники');
        $item['Description'] = text(1815);
        $item['AccessEntityReferenceName'] = 'pm_Participant';
        $item['Url'] = 'participants/list';
        
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'methodology';
        $item['Caption'] = translate('Методология');
        $item['Description'] = text(1816);
        $item['AccessEntityReferenceName'] = 'pm_Methodology';
        $item['Url'] = 'project/methodology';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'rights';
        $item['Caption'] = translate('Права доступа');
        $item['Description'] = text(1817);
        $item['AccessEntityReferenceName'] = 'pm_AccessRight';
        $item['AccessType'] = ACCESS_MODIFY;
        $item['Url'] = 'participants/rights';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'profile';
        $item['Caption'] = text(1904);
        $item['Description'] = text(1905);
        $item['AccessEntityReferenceName'] = 'pm_Participant';
        $item['Url'] = 'profile';
        
        $modules[] = $item;
        
        $object_it = getFactory()->getObject('Dictionary')->getAll();
	    
	    while ( !$object_it->end() ) 
	    {
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'dicts-'.strtolower($object_it->getId());
	        $item['Caption'] = $object_it->getDisplayName();
	        $item['Description'] = text(1818);
	        $item['AccessEntityReferenceName'] = $object_it->getId();
	        $item['Url'] = 'project/dicts/'.$object_it->getId();
	        
	        $modules[] = $item;
    	 	
    	 	$object_it->moveNext();
	    }
        
	    $object_it = getFactory()->getObject('Workflow')->getAll();
	    
	    while ( !$object_it->end() ) 
	    {
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'workflow-'.strtolower($object_it->getId());
	        $item['Caption'] = $object_it->getDisplayName();
	        $item['Description'] = text(1819);
	        $item['AccessEntityReferenceName'] = $object_it->getId();
	        $item['Url'] = 'project/workflow/'.$object_it->getId();
	        
	        $modules[] = $item;
	    	    	 	
    	 	$object_it->moveNext();
	    }
	    
        $item = array();
        
        $item['cms_PluginModuleId'] = 'kbtemplates';
        $item['Caption'] = text(1343);
        $item['Description'] = text(1820);
        $item['AccessEntityReferenceName'] = 'KnowledgeBaseTemplate';
        $item['Url'] = 'knowledgebase/templates';
        
        $modules[] = $item;
	    
        $item = array();
        
        $item['cms_PluginModuleId'] = 'tags';
        $item['Caption'] = translate('Тэги');
        $item['Description'] = text(1821);
        $item['AccessEntityReferenceName'] = 'Tag';
        $item['Url'] = 'project/tags';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'snapshots';
        $item['Caption'] = translate('Версионирование');
        $item['Description'] = text(1822);
        $item['AccessEntityReferenceName'] = 'Snapshot';
        $item['Url'] = 'versioning/revisions';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'versions';
        $item['Caption'] = text(1344);
        $item['Description'] = text(1823);
        $item['AccessEntityReferenceName'] = 'pm_VersionSettings';
        $item['Url'] = 'project/versionsettings';
        
        $modules[] = $item;
        
        foreach( $modules as $module )
        {
        	$module['Area'] = FunctionalAreaMenuSettingsBuilder::AREA_UID;
        	
            $object->addModule( $module );
        }
    }
}