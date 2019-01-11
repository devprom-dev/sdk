<?php

include_once SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

class RequestFormMethods
{
	private $method_create_task = null;
	private $method_duplicate = null;
	private $method_duplicate_issue = null;
	private $method_move = null;
	private $method_watch = null;
	private $new_template_url = '';
	private $target_projects = array();
 	private $method_spend_time = null;
 	private $featureTypesCount = 0;
	private $linkTypes = array();
	private $object = null;
	private $formDisplayed = false;

	function __construct( $object, $formDisplayed ) {
	    $this->object = $object;
	    $this->formDisplayed = $formDisplayed;
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
			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_create_task = $method;
		}

		$method = new ObjectCreateNewWebMethod($object);
		if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
		if ( $method->hasAccess() ) $this->method_duplicate = $method;

		if ( class_exists('Issue') ) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Issue'));
            if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
            if ( $method->hasAccess() ) $this->method_duplicate_issue = $method;
        }

		$method = new MoveToProjectWebMethod($object_it);
		if ( $method->hasAccess() ) {
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
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
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
		
		$this->new_template_url = getFactory()->getObject('RequestTemplate')->getPageNameObject().'&ObjectId=%object-id%&items=%object-id%';

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
				$this->target_projects[$linked_it->getId()] = array (
					'title' => $linked_it->getDisplayName(),
					'vpd' => $linked_it->get('VPD'),
                    'issue' => $linked_it->getMethodologyIt()->get('IsRequirements') == ReqManagementModeRegistry::RDD
				);
				$linked_it->moveNext();
			}
			if ( !getSession()->getProjectIt()->IsPortfolio() ) {
				$this->target_projects[getSession()->getProjectIt()->getId()] = array (
					'title' => getSession()->getProjectIt()->getDisplayName(),
					'vpd' => getSession()->getProjectIt()->get('VPD'),
                    'issue' => false
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
	
 	function getMoreActions( $object_it, $actions )
	{
		if ( is_object($this->method_duplicate) )
		{
			$parms = array(
				'Request' => $object_it->getId(),
				'LinkType' => $this->linkTypes['implemented']
			);
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();

			$vpd = $object_it->get('VPD');
			$other_projects = array_filter($this->target_projects, function($project) use ($vpd) {
				return $project['vpd'] != $vpd;
			});
			if ( count($other_projects) > 0 )
			{
				$items = array();
				foreach( $other_projects as $id => $data )
				{
				    $method = $data['issue'] && is_object($this->method_duplicate_issue)
                        ? $this->method_duplicate_issue
                        : $this->method_duplicate;

                    $method->setVpd($data['vpd']);
					$items[] = array (
                        'name' => $data['title'],
                        'url' => $method->getJSCall(
                                    array_merge($parms, array('Project'=>$id))
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
						'name' => translate('Реализовать'),
						'url' => $this->method_duplicate->getJSCall($parms)
				);
			}
		}

		if ( is_object($this->method_move) )
		{
			$this->method_move->setRequestIt($object_it);
			if ( count($other_projects) > 0 )
			{
				$items = array();
				foreach( $other_projects as $id => $data ) {
					$items[] = array (
							'name' => $data['title'],
							'url' => $this->method_move->getJsCall(array('Project'=>$id))
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

		if ( $this->IsFormDisplayed() )
		{
			$method = new ObjectCreateNewWebMethod($this->getObject());
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
                if ( is_object($this->method_duplicate) ) {
                    $actions[] = array(
                        'name' => text(1519),
                        'url' => preg_replace('/%object-id%/', $object_it->getId(), $this->new_template_url),
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