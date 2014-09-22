<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCommonBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
   		global $model_factory;
 		
 		$module = $model_factory->getObject('Module');
 		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$session = getSession();
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$project_it = getSession()->getProjectIt();
		
		$terminal = $request->getTerminalStates();
		
		$nonterminal = $request->getNonTerminalStates();
		
		$issues_list_it = $module->getExact('issues-list');
		
		if ( getFactory()->getAccessPolicy()->can_read($issues_list_it) )
		{
			$object->addReport(
				array ( 'name' => 'allissues',
						'title' => text(801),
				        'description' => text(1398),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state=all',
				        'module' => $issues_list_it->getId() )
			);
			
			if ( !$methodology_it->HasTasks() )
			{
				$object->addReport(
					array ( 'name' => 'myissues',
							'title' => translate('Мои пожелания'),
					        'description' => text(1407),
							'category' => FUNC_AREA_MANAGEMENT,
					        'query' => 'state='.join(',',$nonterminal).'&owner=user-id',
					        'module' => $issues_list_it->getId() )
				);
			}
			
			$object->addReport(
				array ( 'name' => 'resolvedissues',
						'title' => text(1249),
				        'description' => text(1399),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state='.join($terminal,',').'&modifiedafter=last-month',
				        'module' => $issues_list_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'bugs',
				        'title' => translate('Обнаруженные ошибки'),
						'description' => text(781),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'type=bug&show=RecordCreated&sort=RecordCreated.D&modifiedafter=last-month',
				        'module' => $issues_list_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'newissues',
						'title' => text(1341),
				        'description' => text(1408),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state='.$nonterminal[0].'&group=none&type=none&submittedon=last-week',
				        'module' => $issues_list_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesmine',
						'title' => translate('Добавлены мной'),
				        'description' => text(1404),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'author=user-id',
				        'module' => $issues_list_it->getId() )
			);
		}

		$module_it = $module->getExact('issues-backlog');

		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'productbacklog',
						'description' => text(819),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state='.$nonterminal[0],
				        'module' => $module_it->getId() )
			);
		}
		
 		$module_it = $module->getExact('issues-board');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			if ( !$project_it->IsPortfolio() )
			{
				$object->addReport(
					array ( 'name' => 'issuesboard',
					        'description' => text(1394),
							'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $module_it->getId() )
				);
			}
			
			if ( count($request->getVpds()) > 1 || $project_it->IsPortfolio() )
			{
				$object->addReport(
					array ( 'name' => 'issuesboardcrossproject',
							'title' => text(1843),
					        'description' => text(1394),
							'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $module_it->getId() )
				);
			}
			elseif ( $methodology_it->HasReleases() )
			{
				$object->addReport(
					array ( 'name' => 'releaseplanningboard',
					        'title' => text(1347),
					        'description' => text(1411),
							'category' => FUNC_AREA_MANAGEMENT,
					        'query' => 'group=PlannedRelease',
					        'module' => $module_it->getId() )
				);
			}
		}
		
		$issues_chart_it = $module->getExact('issues-chart');
			
		if ( getFactory()->getAccessPolicy()->can_read($issues_chart_it) )
		{
			$object->addReport(
				array ( 'name' => 'issuesimplementationchart',
						'title' => text(995),
						'description' => text(1019),
				        'query' => 'group=history&aggby=State&state='.join(',',$nonterminal).'&infosections=none&modifiedafter=last-month',
						'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$convergence_states = $terminal;
			$convergence_states[] = $nonterminal[0];
			
			$object->addReport(
				array ( 'name' => 'defectsconvergencechart',
						'title' => text(996),
				        'description' => text(1402),
				        'query' => 'group=history&aggby=State&type=bug&state='.join(',',$convergence_states).'&infosections=none&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'defectsreopenedchart',
						'title' => text(1004),
				        'description' => text(1401),
				        'query' => 'group=history&aggby=Transition&type=bug&infosections=none&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesbyprioritieschart',
						'title' => text(997),
				        'description' => text(1415),
				        'query' => 'group=history&aggby=Priority&infosections=none&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesbytesterschart',
						'title' => text(998),
				        'description' => text(1403),
				        'query' => 'group=Author&aggby=Priority&infosections=none&aggregator=none&type=bug&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);
			
			if ( $methodology_it->HasReleases() )
			{
				$object->addReport(
					array ( 'name' => 'releaseburndown',
							'title' => text(1196),
					        'description' => text(1396),
					        'query' => 'infosections=none&chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
					        'module' => $issues_chart_it->getId() )
				);
			}
			
			if ( $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'releaseburnup',
							'title' => text(1204),
				            'query' => 'infosections=none&chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'description' => text(1205),
				            'module' => $issues_chart_it->getId() )
				);
			}
			
			if ( !$methodology_it->HasTasks() )
			{
			    $object->addReport(
			            array ( 'name' => 'issuesbyownerschart',
			                    'title' => text(999),
				                'query' => 'group=Owner&state=all&aggby=Priority&infosections=none&aggregator=none&modifiedafter=last-month',
			                    'category' => FUNC_AREA_MANAGEMENT,
			                    'type' => 'chart',
				                'module' => $issues_chart_it->getId() )
			    );
			}
		}

		$issues_trace_it = $module->getExact('issues-trace');
			
		if ( getFactory()->getAccessPolicy()->can_read($issues_trace_it) )
		{
			$object->addReport(
				array ( 'name' => 'issues-trace',
						'description' => text(1015),
				        'query' => 'group=PlannedRelease&state=all&infosections=none&sort=PlannedRelease.D&sort2=RecordModified.D',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $issues_trace_it->getId() )
			);
		}

    	$task = getFactory()->getObject('pm_Task');
    	
		$task_list_it = $module->getExact('tasks-list');
	    
		if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_list_it) )
		{
    		$states = $task->getNonTerminalStates();
    		
		    $object->addReport(
				array ( 'name' => 'currenttasks',
						'title' => text(530),
				        'description' => text(1417),
				        'query' => 'release=all&taskstate=all',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_list_it->getId() )
			);
			
			$object->addReport( array ( 
			        'name' => 'mytasks',
			        'title' => translate('Мои задачи'),
			        'description' => text(1406),
			        'query' => 'taskstate='.join(',',$states).'&taskassignee=user-id',
			        'category' => FUNC_AREA_MANAGEMENT,
				    'module' => $task_list_it->getId() )
			);
			
			$object->addReport(
				array ( 'name' => 'resolvedtasks',
						'title' => text(531),
				        'description' => text(1416),
						'query' => 'release=all&taskstate=resolved',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_list_it->getId() )
			);
		}

        $task_chart_it = $module->getExact('tasks-board');

        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
        	if ( !$project_it->IsPortfolio() )
        	{
				$object->addReport(
					array ( 'name' => 'tasksboard',
					        'description' => text(1393),
					        'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $task_chart_it->getId() )
				);
        	}

			if ( count($task->getVpds()) > 1 || $project_it->IsPortfolio() )
			{
				$object->addReport(
					array ( 'name' => 'tasksboardcrossproject',
							'title' => text(1844),
					        'description' => text(1393),
					        'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $task_chart_it->getId() )
				);
			}
			elseif ( $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'iterationplanningboard',
					        'title' => text(1348),
					        'description' => text(1410),
							'query' => 'group=Release&release=all&sort=_group&sort2=Priority',
					        'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $task_chart_it->getId() )
				);
			}
        }
        
 	    $task_chart_it = $module->getExact('tasks-trace');

        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$object->addReport(
				array ( 'name' => 'tasks-trace',
				        'description' => text(1391),
						'query' => 'group=Release&state=all&infosections=none&sort=Release.D&sort2=RecordModified.D',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_chart_it->getId() )
			);
        }
        
        $task_chart_it = $module->getExact('tasks-chart');

        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$object->addReport(
				array ( 'name' => 'tasksimplementationchart',
						'title' => text(1006),
				        'description' => text(1400),
						'query' => 'group=history&aggby=State&infosections=none&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyprioritieschart',
						'title' => text(1007),
				        'description' => text(1414),
						'query' => 'group=history&infosections=none&aggby=Priority&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyassigneeschart',
						'title' => text(1008),
				        'description' => text(1413),
						'query' => 'group=Assignee&infosections=none&taskstate=all&aggby=Priority&aggregator=none&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);			
		
			$object->addReport(
				array ( 'name' => 'tasksplanbytypes',
						'title' => text(1109),
				        'description' => text(1412),
						'query' => 'aggregator=SUM&group=TaskType&infosections=none&aggby=Planned&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksfactbytypes',
						'title' => text(1110),
				        'description' => text(1419),
						'query' => 'aggregator=SUM&group=TaskType&infosections=none&aggby=Fact&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			if ( $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'iterationburndown',
							'title' => text(1195),
					        'description' => text(1397),
							'query' => 'infosections=none&chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
					        'module' => $task_chart_it->getId() )
				);
			}
        }

		$module_it = $module->getExact('project-blog');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$object->addReport(
    		        array ( 'name' => 'project-blog',
    		                'description' => text(1392),
    		                'category' => FUNC_AREA_MANAGEMENT,
    		                'module' => $module_it->getId() )
    		);
		}
        
   		$object->addReport(
   		        array ( 'name' => 'navigation-settings',
   		                'title' => text(1326),
   		                'category' => FunctionalAreaMenuSettingsBuilder::AREA_UID,
   		                'module' => $module->getExact('navigation-settings')->getId() )
   		);
		
		$module_it = $module->getExact('project-knowledgebase');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$object->addReport(
    		        array ( 'name' => 'project-knowledgebase',
    		                'description' => text(1700),
    		                'category' => FUNC_AREA_MANAGEMENT,
    		                'module' => $module_it->getId() )
    		);
		}
		
		$log_it = $module->getExact('project-log');
		
		if ( getFactory()->getAccessPolicy()->can_read($log_it) )
		{
    		$object->addReport(
    		        array ( 'name' => 'project-log',
    		                'description' => text(1266),
						    'query' => 'start=last-week',
    		                'category' => FUNC_AREA_MANAGEMENT,
    		                'module' => $log_it->getId() 
    	    ));
    		
    		$object->addReport(
    		        array ( 'name' => 'discussions',
    		                'title' => text(980),
    		                'description' => text(1409),
						    'query' => 'action=commented&start=last-week',
    		                'category' => FUNC_AREA_MANAGEMENT,
		                    'module' => $log_it->getId()
    		));
		}
		
		$module_it = $module->getExact('project-spenttime');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'activitiesreport',
						'description' => text(529),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
		
		$module_it = $module->getExact('project-question');

		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
		    $object->addReport(
		            array ( 'name' => 'project-question',
		                    'description' => text(892),
		                    'category' => FUNC_AREA_MANAGEMENT,
		                    'module' => $module_it->getId() )
		    );
		}
		
    	$module_it = $module->getExact('project-plan-hierarchy');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'projectplan',
						'title' => $methodology_it->HasPlanning() ? text(1721) : text(740),
				        'description' => text(1389),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
		
		$module_it = $module->getExact('project-plan-milestone');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
		    $object->addReport(
		            array ( 'name' => 'milestones',
		                    'title' => text(908),
		                    'description' => text(1418),
		                    'category' => FUNC_AREA_MANAGEMENT,
				            'module' => $module_it->getId() )
		    );
		}
		
		$module_it = $module->getExact('features-list');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'allfeatures',
						'description' => text(884),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}

 		$module_it = $module->getExact('features-chart');

		if ( getFactory()->getAccessPolicy()->can_read($module_it) && $methodology_it->HasReleases() )
		{
			$object->addReport(
				array ( 'name' => 'features-chart',
				        'description' => text(1395),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
		
		$module_it = $module->getExact('features-trace');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'featurestrace',
				        'description' => text(1390),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
    }
}