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

        $item = array();
        
        $item['cms_PluginModuleId'] = 'project-log';
        $item['Caption'] = translate('Активности');
        $item['AccessEntityReferenceName'] = 'ObjectChangeLog';
        $item['Url'] = 'project/log';
        $item['Icon'] = 'icon-eye-open';
        
        $modules[] = $item;

        $item = array();

        $item['cms_PluginModuleId'] = 'metrics';
        $item['Caption'] = translate('Метрики');
        $item['AccessEntityReferenceName'] = 'pm_ProjectMetric';
        $item['Url'] = 'metrics';
        $item['Icon'] = 'icon-signal';

        $modules[] = $item;

        $item = array();
        $item['cms_PluginModuleId'] = 'project-spenttime';
        $item['Caption'] = translate('Затраченное время');
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
        
        $item['cms_PluginModuleId'] = 'project-question';
        $item['Caption'] = translate('Вопросы');
        $item['AccessEntityReferenceName'] = 'pm_Question';
        $item['Url'] = 'project/question';
        $item['Icon'] = 'icon-question-sign';
        
        $modules[] = $item;
        
        $item = array();
        $item['cms_PluginModuleId'] = 'project-blog';
        $item['Caption'] = translate('Блог');
        $item['AccessEntityReferenceName'] = 'BlogPost';
        $item['Url'] = 'project/blog';
        $item['Icon'] = 'icon-pencil';
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
        $item['cms_PluginModuleId'] = 'issues-backlog';
        $item['Caption'] = translate('Бэклог');
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/list';
        $item['Icon'] = 'icon-align-justify';
        $modules[] = $item;
        
        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-board';
        $item['Caption'] = getSession()->getProjectIt()->IsPortfolio() ? text(1843) : text(1340);
        $item['AccessEntityReferenceName'] = 'pm_ChangeRequest';
        $item['Url'] = 'issues/board';
        $item['Icon'] = 'icon-th-large';

        $modules[] = $item;

        $item = array();
        
        $item['cms_PluginModuleId'] = 'issues-trace';
        $item['Caption'] = translate('Трассировка пожеланий');
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

        if ( $methodology_it->HasTasks() )
        {
	        // tasks modules
	        $item = array();
	        
	        $item['cms_PluginModuleId'] = 'tasks-board';
	        $item['Caption'] = getSession()->getProjectIt()->IsPortfolio() ? text(1844) : translate('Доска задач');
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

            $item = array();

            $item['cms_PluginModuleId'] = 'tasks-import';
            $item['Caption'] = translate('Импорт');
            $item['AccessEntityReferenceName'] = 'pm_Task';
            $item['AccessType'] = ACCESS_CREATE;
            $item['Url'] = 'tasks/board';

            $modules[] = $item;
        }

        if ( $methodology_it->HasFeatures() ) {
            $item = array();
            $item['cms_PluginModuleId'] = 'features-list';
            $item['Caption'] = translate('Функции');
            $item['AccessEntityReferenceName'] = 'pm_Function';
            $item['Url'] = 'features/list';
            $item['Icon'] = 'icon-picture';
            $modules[] = $item;
        }

        foreach( $modules as $module ) {
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

        $item = array();
        $item['cms_PluginModuleId'] = 'attachments';
        $item['Caption'] = text(2097);
        $item['AccessEntityReferenceName'] = 'Attachment';
        $item['Url'] = 'attachments';
        $item['Icon'] = 'icon-file';
        $object->addModule( $item );
    }
    
    function buildSettings( ModuleRegistry $object, $methodology_it )
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

        $item['cms_PluginModuleId'] = 'menu';
        $item['Caption'] = text(1807);
        $item['AccessEntityReferenceName'] = 'pm_CustomReport';
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

        $item = array();
        
        $item['cms_PluginModuleId'] = 'snapshots';
        $item['Caption'] = translate('Версионирование');
        $item['Description'] = text(1822);
        $item['AccessEntityReferenceName'] = 'Snapshot';
        $item['Url'] = 'versioning/revisions';
        
        $modules[] = $item;

        foreach( $modules as $module )
        {
        	$module['Area'] = FunctionalAreaMenuSettingsBuilder::AREA_UID;
        	
            $object->addModule( $module );
        }
    }
}