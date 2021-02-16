<?php
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/RequestCreateTaskWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MoveToProjectWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

class RequestFormMethods
{
	private $method_create_task = null;
	private $method_duplicate = null;
	private $method_duplicate_issue = null;
	private $method_move = null;
	private $method_watch = null;
	private $new_template_method = null;
	private $target_projects = array();
 	private $method_spend_time = null;
 	private $featureTypesCount = 0;
	private $linkTypes = array();
	private $object = null;
	private $formDisplayed = false;
	private $implementObject = null;
	private $duplicateMethod = null;

	function __construct( $object, $formDisplayed, $implementObject = null )
    {
	    $this->object = $object;
	    $this->formDisplayed = $formDisplayed;
        $this->implementObject = is_object($implementObject) ? $implementObject : $this->object;

	    $this->buildMethods();
    }

    function getObject() {
	    return $this->object;
    }

    function IsFormDisplayed() {
	    return $this->formDisplayed;
    }

    protected function buildMethods()
	{
		$object = $this->getObject();
		$object_it = $object->getEmptyIterator();

        $object->addAttributeGroup('SubmittedVersion', 'additional');
        $object->addAttributeGroup('Environment', 'additional');

 		$method = new RequestCreateTaskWebMethod($object_it);
		if ( $method->hasAccess() ) {
			$this->method_create_task = $method;
		}

		$method = new ObjectCreateNewWebMethod($this->implementObject);
		if ( $method->hasAccess() ) $this->method_duplicate = $method;

		if ( class_exists('Issue') ) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Issue'));
            if ( $method->hasAccess() ) $this->method_duplicate_issue = $method;
        }

		$method = new MoveToProjectWebMethod($object_it);
		if ( $method->hasAccess() ) {
		    if ( $this->formDisplayed ) {
                $method->setUrl($object_it->object->getPage());
            }
			$this->method_move = $method;
		}

		if ( $this->IsFormDisplayed() ) {
			$method = new WatchWebMethod($object_it);
			if ( $method->hasAccess() ) {
				$this->method_watch = $method;
			}
		}

	 	$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() ) {
 			$this->method_spend_time = $method;
 		}

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('RequestTemplate'));
 		if ( $method->hasAccess() ) {
            $this->new_template_method = $method;
        }

 		if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED || defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $projects = array_filter(
                preg_split('/,/',
                    join(',', array(
                        getSession()->getProjectIt()->get('LinkedProject'),
                        getSession()->getProjectIt()->get('PortfolioProject')
                    ))
                ),
                function ($value) { return $value != ''; }
            );
            if ( count($projects) < 1 ) {
                $linked_it = getFactory()->getObject('ProjectActive')->getRegistry()->Query(
                    array(
                        new ProjectNoGroupsPredicate()
                    )
                );
                $projects = $linked_it->idsToArray();
                if ( count($projects) > 11 ) $projects = array();
            }
            else {
                $linked_it = getFactory()->getObject('ProjectLinkedActive')->getRegistry()->Query();
            }
        }
        else {
            $linked_it = getFactory()->getObject('ProjectActive')->getAll();
            $projects = $linked_it->idsToArray();
        }

        $top_limit = getSession()->getProjectIt()->IsPortfolio() ? 11 : 199;
		if ( count($projects) > 0 && count($projects) < $top_limit )
		{
			while( !$linked_it->end() ) {
				$this->target_projects[$linked_it->get('VPD')] = array (
				    'id' => $linked_it->getId(),
					'title' => $linked_it->getDisplayName(),
					'vpd' => $linked_it->get('VPD'),
                    'issue' => $linked_it->getMethodologyIt()->get('IsRequirements') == ReqManagementModeRegistry::RDD
				);
				$linked_it->moveNext();
			}
			$projectIt = getSession()->getProjectIt();
			if ( !$projectIt->IsPortfolio() ) {
				$this->target_projects[$projectIt->get('VPD')] = array (
                    'id' => $projectIt->getId(),
					'title' => $projectIt->getDisplayName(),
					'vpd' => $projectIt->get('VPD'),
                    'issue' => $projectIt->getMethodologyIt()->get('IsRequirements') == ReqManagementModeRegistry::RDD
				);
			}
		}

		$this->featureTypesCount = getFactory()->getObject('pm_FeatureType')->getRecordCount();

		$type_it = getFactory()->getObject('RequestLinkType')->getAll();
		while( !$type_it->end() ) {
			$this->linkTypes[$type_it->get('ReferenceName')] = $type_it->getId();
			$type_it->moveNext();
		}

        $method = new WikiExportBaseWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array ('pm_ChangeRequestId' => '%id%')
            )
        );
        $converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $this->exportMethods[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url($methodPageIt, $converter_it->get('EngineClassName'))
            );
            $converter_it->moveNext();
        }

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Owner') ) {
            $this->assignMethod = array (
                'name' => translate('Назначить'),
                'url' => "javascript:processBulk('".translate('Назначить')."','?formonly=true&operation=AttributeOwner','%ids');"
            );
        }

        $this->duplicateMethod = new DuplicateIssuesWebMethod();
    }

	function getDeleteActions( $object_it, $actions )
	{
		if ( is_object($this->method_watch) )
		{
			$this->method_watch->setObjectIt($object_it);			
		
			array_unshift($actions, array());
			array_unshift($actions, array( 
					'name' => $this->method_watch->getCaption(),
					'url' => $this->method_watch->getJSCall()
			));
		}

		return $actions;
	}

	function getRequestCleansedData( $objectIt )
    {
        $skipAttributes = array_merge(
            $objectIt->object->getAttributesByGroup('system'),
            $objectIt->object->getAttributesByGroup('trace'),
            $this->duplicateMethod->getAttributesToReset()
        );
	    return \JsonWrapper::encode(
	        array_filter(
                $objectIt->getData(),
                function($value,$key) use($skipAttributes) {
                    return !is_numeric($key) && !in_array($key, $skipAttributes) && mb_strlen($value) < 256;
                },
                ARRAY_FILTER_USE_BOTH
            )
        );
    }

 	function getMoreActions( $object_it, $actions )
	{
        $vpd = $object_it->get('VPD');
        $other_projects = array_filter($this->target_projects, function($project) use ($vpd) {
            return $project['vpd'] != $vpd;
        });

        if ( !getSession()->IsRDD() || $this->getObject() instanceof Issue ) {
            if ( !$this->target_projects[$object_it->get('VPD')]['issue'] && is_object($this->method_duplicate) ) {
                $parms = array(
                    'Request' => $this->getRequestCleansedData($object_it),
                    'LinkType' => $this->linkTypes['implemented']
                );
                if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
                if ( count($other_projects) > 0 )
                {
                    $items = array();
                    foreach( $other_projects as $data )
                    {
                        $method = $data['issue'] && is_object($this->method_duplicate_issue)
                            ? $this->method_duplicate_issue
                            : $this->method_duplicate;

                        $method->setVpd($data['vpd']);
                        $items[] = array (
                            'name' => $data['title'],
                            'url' => $method->getJSCall(
                                array_merge($parms, array('Project' => $data['id']))
                            )
                        );
                    }

                    $items[] = array();
                    $this->method_duplicate->setVpd($object_it->get('VPD'));
                    $items[] = array (
                        'name' => translate('Выбрать'),
                        'url' => $this->method_duplicate->getJSCall($parms)
                    );

                    $actions[] = array(
                        'name' => text(867),
                        'items' => $items
                    );
                }
                else
                {
                    $actions[] = array(
                        'name' => text(2694),
                        'url' => $this->method_duplicate->getJSCall($parms),
                        'uid' => 'implement'
                    );
                }
            }
        }

		if ( is_object($this->method_move) )
		{
			$this->method_move->setRequestIt($object_it);
			if ( count($other_projects) > 0 )
			{
				$items = array();
				foreach( $other_projects as $data ) {
					$items[] = array (
							'name' => $data['title'],
							'url' => $this->method_move->getJsCall(array('Project' => $data['id']))
					);
				}

				$items[] = array();
				$items[] = array (
						'name' => translate('Выбрать'),
						'url' => $this->method_move->getJsCall()
				);

				$actions[] = array(
					'name' => $this->method_move->getCaption(),
					'items' => $items
				);
			}
			else
			{
				$actions[] = array(
						'name' => $this->method_move->getCaption(),
						'url' => $this->method_move->getJsCall()
				);
			}
		}

        if ( is_array($this->assignMethod) && !$this->IsFormDisplayed() ) {
            $method = $this->assignMethod;
            $method['url'] = preg_replace('/%ids/', $object_it->getId(), $method['url']);
            $actions[] = array();
            $actions[] = $method;
        }

        if ( is_object($this->method_spend_time) )
		{
			$this->method_spend_time->setAnchorIt($object_it);

			$actions[] = array();
			$actions[] = array (
				'name' => $this->method_spend_time->getCaption(),
				'url' => $this->method_spend_time->getJSCall(),
                'uid' => 'spend-time'
			);
		}

		return $actions;
	}

	function getTaskMethod( $object_it ) {
        if ( is_object($this->method_create_task) ) {
            $this->method_create_task->setRequestIt($object_it);
        }
        return $this->method_create_task;
    }

	function getNewRelatedActions( $object_it, $actions )
	{
        if ( is_object($this->method_create_task) ) {
            $this->method_create_task->setRequestIt($object_it);
            $actions[] = array (
                'name' => $this->method_create_task->getCaption(),
                'url' => $this->method_create_task->getJSCall(),
                'uid' => 'new-task'
            );
        }

		if ( $this->IsFormDisplayed() ) {
			$method = new ObjectCreateNewWebMethod($this->implementObject);
			if ( $method->hasAccess() ) {
                $typeIt = getFactory()->getObject('RequestType')->getAll();
                while(!$typeIt->end()) {
                    $actions[] = array(
                        'name' => $typeIt->getDisplayName(),
                        'url' => $method->getJSCall(
                            array(
                                'IssueLinked' => $object_it->getId(),
                                'Type' => $typeIt->getId()
                            )
                        )
                    );
                    $typeIt->moveNext();
                }
                if ( is_object($this->new_template_method) ) {
                    $actions[] = array(
                        'name' => text(1519),
                        'url' => $this->new_template_method->getJSCall(
                                        array(
                                            'ObjectId' => $object_it->getId(),
                                            'items' => $object_it->getId()
                                        )
                                    ),
                        'uid' => 'as-template'
                    );
                }
            }
		}

		return $actions;
	}

    function getExportActions( $object_it, $actions )
    {
        foreach( $this->exportMethods as $action ) {
            $action['url'] = preg_replace('/%id%/', $object_it->getId(), $action['url']);
            $actions[] = $action;
        }

        return $actions;
    }
}