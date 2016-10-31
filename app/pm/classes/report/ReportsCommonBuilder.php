<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCommonBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
        $areas = getFactory()->getObject('ModuleCategory')->getAll()->fieldToArray('ReferenceName');
        $qa_area = in_array('qa', $areas) ? 'qa' : FUNC_AREA_MANAGEMENT;

        $module = getFactory()->getObject('Module');
		$request = getFactory()->getObject('pm_ChangeRequest');
		$bug_type = getFactory()->getObject('RequestType')->getByRef('ReferenceName', 'bug')->get('ReferenceName');

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		$project_it = getSession()->getProjectIt();
		
		$terminal = $request->getTerminalStates();
		$nonterminal = $request->getNonTerminalStates();
		
		$module_it = $module->getExact('issues-backlog');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'allissues',
					'title' => text(801),
					'description' => text(1398),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'state=all',
					'module' => $module_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'resolvedissues',
					'title' => text(1249),
					'description' => text(1399),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'state='.join($terminal,',').'&modifiedafter=last-month',
					'module' => $module_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'bugs',
					'title' => text(2043),
					'description' => text(781),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'type='.$bug_type.'&modifiedafter=last-month',
					'module' => $module_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'newissues',
					'title' => text(1341),
					'description' => text(1408),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'state='.array_shift(array_values($nonterminal)).'&group=none&type=none&submittedon=last-week',
					'module' => $module_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesmine',
					'title' => translate('Добавлены мной'),
					'description' => text(1404),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'author=user-id',
					'module' => $module_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'productbacklog',
						'description' => text(819),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state='.array_shift(array_values($nonterminal)),
				        'module' => $module_it->getId() )
			);
		}
		
 		$module_it = $module->getExact('issues-board');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			if ( !$project_it->IsPortfolio() ) {
				if ( $project_it->getMethodologyIt()->get('IsKanbanUsed') != 'Y' ) {
					$object->addReport(
						array ( 'name' => 'issuesboard',
							'description' => text(1394),
							'category' => FUNC_AREA_MANAGEMENT,
							'module' => $module_it->getId() )
					);
				}
				if ( $methodology_it->HasReleases() ) {
					$object->addReport(
						array (
							'name' => 'releaseplanningboard',
							'title' => text(1347),
							'description' => text(1411),
							'category' => FUNC_AREA_MANAGEMENT,
							'query' => ($methodology_it->HasTasks() ? 'group=none' : 'group=Owner').'&state='.join(',',$nonterminal),
							'module' => $module_it->getId() )
					);
				}
				if ( $methodology_it->HasPlanning() ) {
					$object->addReport(
						array (
							'name' => 'iterationplanningboard',
							'title' => text(2190),
							'category' => FUNC_AREA_MANAGEMENT,
							'query' => ($methodology_it->HasTasks() ? 'group=none' : 'group=Owner').'&state='.join(',',$nonterminal),
							'module' => $module_it->getId() )
					);
				}
			}
			
			if ( $project_it->IsProgram() )
			{
				$object->addReport(
					array ( 'name' => 'issuesboardcrossproject',
							'title' => text(1843),
					        'description' => text(1394),
							'category' => FUNC_AREA_MANAGEMENT,
					        'module' => $module_it->getId() )
				);
			}
			else
			{
			}

			$object->addReport(
				array ( 'name' => 'issuesboarddeadlines',
						'title' => text(1939),
				        'description' => text(1940),
						'category' => FUNC_AREA_MANAGEMENT,
						'query' => 'group=DueWeeks&state='.join(',',$nonterminal),
				        'module' => $module_it->getId() )
			);
		}
		
		$issues_chart_it = $module->getExact('issues-chart');
			
		if ( getFactory()->getAccessPolicy()->can_read($issues_chart_it) )
		{
			$object->addReport(
				array ( 'name' => 'issuesimplementationchart',
						'title' => text(995),
						'description' => text(1019),
				        'query' => 'group=history&aggby=State&state='.join(',',$nonterminal).'&modifiedafter=last-month',
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
				        'query' => 'group=history&aggby=State&type='.$bug_type.'&state='.join(',',$convergence_states).'&modifiedafter=last-month',
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'defectsreopenedchart',
						'title' => text(1004),
				        'description' => text(1401),
				        'query' => 'group=history&aggby=LastTransition&type='.$bug_type.'&state=all&modifiedafter=last-month&transition='.join(',',$this->getReopenTransitions($request)),
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesreopenedbyownerchart',
						'title' => text(1885),
				        'description' => text(1886),
				        'query' => 'group=Owner&aggby=Priority&aggregator=none&state=all&was-transition='.join(',',$this->getReopenTransitions($request)),
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);
			
			$object->addReport(
				array ( 'name' => 'issuesbyprioritieschart',
						'title' => text(997),
				        'description' => text(1415),
				        'query' => 'group=history&aggby=Priority&state=all&modifiedafter=last-month',
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesbytesterschart',
						'title' => text(998),
				        'description' => text(1403),
				        'query' => 'group=Author&aggby=Priority&aggregator=none&type='.$bug_type.'&state=all&modifiedafter=last-month',
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array (
                    'name' => 'bugstable',
					'title' => text(2046),
					'description' => text(2047),
					'query' => 'group=State&aggby=Priority&aggregator=none&type='.$bug_type.'&state=all',
					'category' => $qa_area,
					'type' => 'chart',
					'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array (
					'name' => 'reportedperweek',
					'title' => text(2051),
					'description' => text(2052),
					'query' => 'group=WeekCreated&aggby=Author&aggregator=none&submittedon=last-month&type='.$bug_type.'&state=all',
					'category' => $qa_area,
					'type' => 'chart',
					'module' => $issues_chart_it->getId() )
			);

			if ( $methodology_it->HasReleases() )
			{
				$object->addReport(
					array ( 'name' => 'releaseburndown',
							'title' => text(1196),
					        'description' => text(1396),
					        'query' => 'chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'icon' => 'icon-fire',
					        'module' => $issues_chart_it->getId() )
				);

				$object->addReport(
					array ( 'name' => 'projectburnup',
							'title' => text(1928),
					        'query' => 'chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'icon' => 'icon-fire',
					        'module' => $issues_chart_it->getId() )
				);
			}
			
			if ( $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'releaseburnup',
							'title' => text(1204),
				            'query' => 'chartdata=hide&chartlegend=hide',
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
							'query' => 'group=Owner&state=all&aggby=Priority&aggregator=none&modifiedafter=last-month',
							'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'description' => text(1205),
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
				        'query' => 'group=PlannedRelease&state=all&sort=PlannedRelease.D&sort2=RecordModified.D',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $issues_trace_it->getId() )
			);
		}

    	$task = getFactory()->getObject('pm_Task');
		$terminal_states = $task->getTerminalStates();
		$non_terminal_states = $task->getNonTerminalStates();

		$task_list_it = $module->getExact('tasks-list');
		if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_list_it) )
		{

			$query_common = '';
			if ( $methodology_it->HasPlanning() ) {
				$query_common .= '&group=Release';
			}

			$object->addReport(
				array ( 'name' => 'newtasks',
					'title' => text(2138),
					'query' => 'submittedon=last-week',
					'category' => FUNC_AREA_MANAGEMENT,
					'module' => $task_list_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'unassignedtasks',
					'title' => text(2139),
					'query' => 'taskassignee=none',
					'category' => FUNC_AREA_MANAGEMENT,
					'module' => $task_list_it->getId() )
			);

		    $object->addReport(
				array ( 'name' => 'currenttasks',
						'title' => text(1356),
				        'description' => text(1417),
				        'query' => $query_common.'&iteration=all&taskstate='.join(',',$non_terminal_states),
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_list_it->getId() )
			);
			
			$object->addReport(
				array ( 'name' => 'resolvedtasks',
						'title' => text(531),
				        'description' => text(1416),
						'query' => 'iteration=all&taskstate=resolved',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_list_it->getId() )
			);
		}

		$worker_it = getFactory()->getObject('User')->getRegistry()->Query(
			array (
				new FilterInPredicate(getSession()->getUserIt()->getId()),
				new UserWorkerPredicate()
			)
		);
		if ( $worker_it->count() > 0 ) {
			$object->addReport( array (
					'name' => 'mytasks',
					'title' => translate('Мои задачи'),
					'description' => text(1406),
					'query' => $query_common.'&taskassignee=user-id',
					'category' => FUNC_AREA_MANAGEMENT,
					'module' => $task_list_it->getId() )
			);
		}

		$task_chart_it = $module->getExact('tasks-board');
        if ( $methodology_it->HasTasks() ) {
            if ( getFactory()->getAccessPolicy()->can_read($task_chart_it) ) {
                $object->addReport(
                    array (
                        'name' => 'tasksboard',
                        'description' => text(1393),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_chart_it->getId() )
                );
                $object->addReport(
                    array (
                        'name' => 'tasksboardforissues',
                        'title' => text(2222),
                        'query' => 'group=ChangeRequest',
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_chart_it->getId() )
                );
                if ( getSession()->getProjectIt()->IsProgram() ) {
                    $object->addReport(
                        array (
                            'name' => 'tasksboardcrossproject',
                            'title' => text(1844),
                            'description' => text(1393),
                            'category' => FUNC_AREA_MANAGEMENT,
                            'module' => $task_chart_it->getId() )
                    );
                }
                else if ( count($task->getVpds()) == 1 && $methodology_it->HasPlanning() ) {
                    $object->addReport(
                        array (
                            'name' => 'tasksplanningboard',
                            'title' => text(1348),
                            'description' => text(1410),
                            'query' => 'group=Assignee&iteration=all&taskstate=' . join(',', $non_terminal_states),
                            'category' => FUNC_AREA_MANAGEMENT,
                            'module' => $task_chart_it->getId() )
                    );
                }
            }
        }

 	    $task_chart_it = $module->getExact('tasks-trace');
        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$object->addReport(
				array ( 'name' => 'tasks-trace',
				        'description' => text(1391),
						'query' => 'group=Release&taskstate=all&sort=Release.D&sort2=RecordModified.D',
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
						'query' => 'group=history&aggby=State&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyprioritieschart',
						'title' => text(1007),
				        'description' => text(1414),
						'query' => 'group=history&aggby=Priority&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyassigneeschart',
						'title' => text(1008),
				        'description' => text(1413),
						'query' => 'group=Assignee&taskstate=all&aggby=Priority&aggregator=none&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);			
		
			$object->addReport(
				array ( 'name' => 'tasksreopenedbyassigneechart',
						'title' => text(1888),
				        'description' => text(1889),
				        'query' => 'group=Assignee&aggby=Priority&aggregator=none&state=all&was-transition='.join(',',$this->getReopenTransitions($task)),
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);
			
			$object->addReport(
				array ( 'name' => 'tasksplanbytypes',
						'title' => text(1109),
				        'description' => text(1412),
						'query' => 'aggregator=SUM&group=TaskType&aggby=Planned&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksfactbytypes',
						'title' => text(1110),
				        'description' => text(1419),
						'query' => 'aggregator=SUM&group=TaskType&aggby=Fact&taskstate=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			if ( $methodology_it->TaskEstimationUsed() )
			{
				$object->addReport(
					array(
						'name' => 'tasksplanbyfact',
						'title' => text(2063),
						'category' => FUNC_AREA_MANAGEMENT,
						'query' => 'chartdata=hide&chartlegend=hide&aggregator=AVG&group=FinishDate&aggby=PlanFact&taskstate=' .
										join(',', $terminal_states) . '&modifiedafter=last-month',
						'type' => 'chart',
						'module' => $task_chart_it->getId()
					)
				);
			}

			if ( $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'iterationburndown',
							'title' => text(1195),
					        'description' => text(1397),
							'query' => 'chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'icon' => 'icon-fire',
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
   		                'module' => $module->getExact('menu')->getId() )
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
		                    'module' => $log_it->getId(),
							'icon' => 'icon-comment'
    		));
		}
		
		$module_it = $module->getExact('project-spenttime');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'activitiesreport',
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
						'title' => text(1721),
				        'description' => text(1389),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
		
		$module_it = $module->getExact('attachments');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
					array ( 'name' => 'attachments',
							'category' => FUNC_AREA_MANAGEMENT,
							'module' => $module_it->getId() )
			);
		}

        $module_it = $module->getExact('project-reports');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array (
                'name' => 'charts',
                'title' => text(2229),
                'query' => 'reporttype=chart',
                'icon' => 'icon-signal',
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));
        }
    }
    
    protected function getReopenTransitions( $object )
    {
		return WorkflowScheme::Instance()->getStateTransitionIt(
			$object, WorkflowScheme::Instance()->getTerminalStates($object)
		)->idsToArray();
    }
}