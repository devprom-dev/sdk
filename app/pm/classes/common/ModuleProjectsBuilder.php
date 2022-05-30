<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuSettingsBuilder.php";

class ModuleProjectsBuilder extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

    	$this->buildProjectManagement($object, $methodology_it);
    	$this->buildSettings($object, $methodology_it);
    }
    
    public function buildProjectManagement( ModuleRegistry & $object, $methodology_it )
    {
    	$modules = array();
        $separateIssues = $methodology_it->get('IsRequirements') == ReqManagementModeRegistry::RDD;

        $item = array();
        $item['cms_PluginModuleId'] = 'project-log';
        $item['Caption'] = text(2624);
        $item['AccessEntityReferenceName'] = 'pm_Question';
        $item['Url'] = 'project/log';
        $item['Icon'] = 'icon-eye-open';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'whatsnew';
        $item['Caption'] = text(2458);
        $item['AccessEntityReferenceName'] = 'ObjectChangeLog';
        $item['Url'] = 'whatsnew';
        $item['Icon'] = 'icon-bell';
        $item['Description'] = text(2459);
        $modules[] = $item;

        $projectIt = getSession()->getProjectIt();
        if ( $projectIt->IsPortfolio() || $projectIt->IsProgram() ) {
            $item = array();
            $item['cms_PluginModuleId'] = 'projects';
            $item['Caption'] = text('projects.list.title');
            $item['AccessEntityReferenceName'] = 'pm_Project';
            $item['Url'] = 'projects';
            $item['Icon'] = 'icon-briefcase';
            $modules[] = $item;
        }

        $item = array();
        $item['cms_PluginModuleId'] = 'project-spenttime';
        $item['Caption'] = translate('Затраченное время');
        $item['Description'] = text(2662);
        $item['AccessEntityReferenceName'] = 'pm_Activity';
        $item['Url'] = 'participants/spenttime';
        $item['Icon'] = 'icon-time';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'worklog';
        $item['Caption'] = text(2334);
        $item['AccessEntityReferenceName'] = 'pm_Activity';
        $item['Url'] = 'worklog';
        $item['Icon'] = 'icon-time';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'worklog-chart';
        $item['Caption'] = text(2492);
        $item['AccessEntityReferenceName'] = 'pm_Activity';
        $item['Url'] = 'worklog/chart';
        $item['Icon'] = 'icon-signal';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'project-question';
        $item['Caption'] = text(2805);
        $item['AccessEntityReferenceName'] = 'pm_Question';
        $item['Url'] = 'project/question';
        $item['Icon'] = 'icon-question-sign';
        $modules[] = $item;

        if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' ) {
            $item = array();
            $item['cms_PluginModuleId'] = 'project-knowledgebase';
            $item['Caption'] = translate('База знаний');
            $item['AccessEntityReferenceName'] = 'ProjectPage';
            $item['Url'] = 'knowledgebase/tree';
            $item['Icon'] = 'icon-book';
            $modules[] = $item;
        }

        $item = array();
        $item['cms_PluginModuleId'] = 'project-reports';
        $item['Caption'] = text(2069);
        $item['Description'] = text(1824);
        $item['AccessEntityReferenceName'] = 'cms_Report';
        $item['Url'] = 'project/reports';
        $modules[] = $item;
        
        $item = array();
        $item['cms_PluginModuleId'] = 'project-plan-hierarchy';
        $item['Caption'] = text(1721);
        $item['AccessEntityReferenceName'] = 'pm_Project';
        $item['Url'] = 'plan/hierarchy';
        $item['Icon'] = 'icon-calendar';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'milestones';
        $item['Caption'] = translate('Вехи');
        $item['AccessEntityReferenceName'] = 'pm_Milestone';
        $item['Url'] = 'plan/milestone';
        $item['Icon'] = 'icon-calendar';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'releases';
        $item['Caption'] = translate('Релизы');
        $item['AccessEntityReferenceName'] = 'pm_Version';
        $item['Url'] = 'releases';
        $item['Icon'] = 'icon-calendar';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'iterations';
        $item['Caption'] = translate('Итерации');
        $item['AccessEntityReferenceName'] = 'pm_Release';
        $item['Url'] = 'iterations';
        $item['Icon'] = 'icon-calendar';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'issues-backlog';
        $item['Caption'] = translate('Бэклог');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/list';
        $item['Icon'] = 'icon-align-justify';
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-board';
        $item['Caption'] = getSession()->IsRDD() ? text(3131) : text(1340);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/board';
        $item['Icon'] = 'icon-th-large';
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-trace';
        $item['Caption'] = $separateIssues ? text(2658) : translate('Трассировка пожеланий');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/trace';
        $item['Icon'] = 'icon-random';
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-chart';
        $item['Caption'] = text(1851);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/chart';
        $item['Icon'] = 'icon-signal';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'tasks-list';
        $item['Caption'] = text(530);
        $item['AccessEntityReferenceName'] = 'pm_Task';
        $item['Url'] = 'tasks/list';
        $item['Icon'] = 'icon-list';
        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'search';
        $item['Caption'] = translate('Поиск');
        $item['AccessEntityReferenceName'] = 'pm_Project';
        $item['Url'] = 'search.php';
        $item['Icon'] = 'icon-search';
        $modules[] = $item;

        if ( $methodology_it->HasTasks() )
        {
	        // tasks modules
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-board';
	        $item['Caption'] = getSession()->getProjectIt()->IsPortfolio() ? text(1844) : translate('Доска задач');
            $item['Description'] = text(2663);
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/board';
            $item['Icon'] = 'icon-th';
	        
	        $modules[] = $item;
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-trace';
	        $item['Caption'] = translate('Трассировка задач');
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/trace';
            $item['Icon'] = 'icon-random';
	        
	        $modules[] = $item;
	        
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-chart';
	        $item['Caption'] = text(1850);
	        $item['AccessEntityReferenceName'] = 'pm_Task';
	        $item['Url'] = 'tasks/chart';
            $item['Icon'] = 'icon-signal';
	        
	        $modules[] = $item;
        }

        $item = array();
        $item['cms_PluginModuleId'] = 'dashboard';
        $item['Caption'] = text(2925);
        $item['AccessEntityReferenceName'] = 'DashboardItem';
        $item['Url'] = 'dashboard';
        $item['Icon'] = 'icon-qrcode';
        $modules[] = $item;

        foreach( $modules as $module ) {
        	$module['Area'] = FUNC_AREA_MANAGEMENT;
            $object->addModule( $module );
        }
        
        $item = array();
        $item['cms_PluginModuleId'] = 'attachments';
        $item['Caption'] = text(2097);
        $item['Description'] = text(2661);
        $item['AccessEntityReferenceName'] = 'Attachment';
        $item['Url'] = 'attachments';
        $item['Icon'] = 'icon-file';
        $object->addModule( $item );

        $item = array();
        $item['cms_PluginModuleId'] = 'delivery';
        $item['Caption'] = text(2932);
        $item['AccessEntityReferenceName'] = 'pm_Function';
        $item['Url'] = 'roadmap';
        $item['Icon'] = 'icon-road';
        $object->addModule( $item );
    }
    
    function buildSettings( ModuleRegistry $object, $methodology_it )
    {
        $modules = array();
        
        // project settings modules
        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-settings';
        $item['Caption'] = text(2618);
        $item['Description'] = text(1812);
        $item['AccessEntityReferenceName'] = 'pm_Project';
        $item['AccessType'] = ACCESS_MODIFY;
        $item['Url'] = 'project/settings';

        $modules[] = $item;

        $item['cms_PluginModuleId'] = 'menu';
        $item['Caption'] = text(1807);
        $item['AccessEntityReferenceName'] = 'pm_Workspace';
        $item['Url'] = 'menu';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'methodology';
        $item['Caption'] = translate('Методология');
        $item['Description'] = text(1816);
        $item['AccessEntityReferenceName'] = 'pm_Methodology';
        $item['Url'] = 'project/methodology';
        
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'profile';
        $item['Caption'] = text(1292);
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
            $item['Icon'] = 'icon-list-alt';

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
            $item['Icon'] = 'icon-retweet';
	        
	        $modules[] = $item;
	    	    	 	
    	 	$object_it->moveNext();
	    }

        $item = array();
        $item['cms_PluginModuleId'] = 'autoactions';
        $item['Caption'] = text(2433);
        $item['AccessEntityReferenceName'] = 'AutoAction';
        $item['Url'] = 'autoactions';
        $item['Icon'] = 'icon-barcode';
        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'tags';
        $item['Caption'] = translate('Тэги');
        $item['Description'] = text(1821);
        $item['AccessEntityReferenceName'] = 'Tag';
        $item['Url'] = 'project/tags';
        
        $modules[] = $item;

        foreach( $modules as $module )
        {
        	$module['Area'] = FunctionalAreaMenuSettingsBuilder::AREA_UID;
            $object->addModule( $module );
        }
    }
}