<?php
use Devprom\ProjectBundle\Service\Wiki\WikiDeltaService;
include_once SERVER_ROOT_PATH."pm/views/design/fields/FieldComponentInverseTrace.php";
include_once SERVER_ROOT_PATH."pm/views/issues/RequestFormMethods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldAttachments.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueTrace.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';
include_once "FieldTasksRequest.php";
include_once "FieldLinkedRequest.php";
include_once "FieldRequestTagTrace.php";
include_once "FieldIssueDeadlines.php";
include_once "FieldAuthor.php";
include_once "FieldIssueCode.php";
include "FieldEstimationDictionary.php";

class RequestFormBase extends PMPageForm
{
    private $methods = null;
	private $links_it = null;
	private $fieldActions = array();
	private $mbeddedTasksForm = null;

	function __construct( $object ) 
	{
		parent::__construct($object);
		$this->methods = $this->buildMethods();
		$this->mbeddedTasksForm = new FormTaskEmbedded(getFactory()->getObject('Task'));
	}

    public function buildMethods() {
	    return array();
    }

    function getMethods() {
        return $this->methods;
    }

    protected function extendModel()
    {
        $object = $this->getObject();
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        if ( $this->getEditMode() ) {
            $object->setAttributeVisible('OrderNum', true);
        }

        $object->setAttributeVisible('State', !$this->getEditMode());
        $object->setAttributeVisible('Fact', is_object($this->getObjectIt()));
        $object->setAttributeEditable('Fact', true);

        if ( getFactory()->getObject('RequestType')->getAll()->count() < 1 || $_REQUEST['Type'] != '' ) {
            $object->setAttributeVisible('Type', false);
            $object->setAttributeRequired('Type', false);
        }
        $object->setAttributeVisible('ProjectPage', true);

        if ( is_object($this->getObjectIt()) ) {
            $state_it = $this->getStateIt();
            if ( $state_it->get('IsTerminal') == 'Y' ) {
                $object->setAttributeVisible('FinishDate', true);
            }
            else if ( $methodology_it->IsAgile() ) {
                $object->setAttributeVisible('DeliveryDate', true);
            }

            if ( $this->getObjectIt()->get('Links') != '' ) {
                $this->links_it = getFactory()->getObject('Request')->getRegistry()->Query(
                    array (
                        new FilterInPredicate(preg_split('/,/', $this->getObjectIt()->get('Links'))),
                        new AttachmentsPersister()
                    )
                );
                $attachments = array_filter($this->links_it->fieldToArray('Attachment'), function($value) {
                    return $value != '';
                });
                if ( count($attachments) > 0 ) {
                    $object->addAttribute('LinksAttachment', 'VARCHAR', text(2124),
                        true, false, '', $object->getAttributeOrderNum('Links') + 1);
                    $object->addAttributeGroup('LinksAttachment', 'trace');
                }
            }

            if ( $this->getAction() == 'view' ) {
                $this->fieldActions = $this->buildFieldActions();
            }
        }

        $object->setAttributeType('PlannedRelease', 'REF_ReleaseRecentId');
        $object->setAttributeType('Iteration', 'REF_IterationRecentId');
        $object->setAttributeType('Owner', 'REF_ProjectUserId');

        parent::extendModel();

        if ( $this->getEditMode() ) {
            if ( !getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Task')) ) {
                $object->setAttributeVisible('Tasks', false);
            }
        }
    }

    function buildFieldActions() {
        return array();
    }

    function getEmbeddedForm( $object )
    {
	    if ( $object instanceof Task ) {
            return $this->mbeddedTasksForm;
        }
        else {
	        return parent::getEmbeddedForm($object);
        }
    }

	function getTransitionAttributes()
	{
	    if ( $this->showDescriptionOnRight() ) return parent::getTransitionAttributes();
		return array('Caption', 'UID');
	}
	
	function createFieldObject( $name )
	{
		$plugins = getFactory()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
   	    foreach( $plugins_interceptors as $plugin )
        {
        	$field = $plugin->interceptMethodFormCreateFieldObject( $this, $name );
        	if ( is_object($field) ) return $field;
		}
		
		$this->object_it = $this->getObjectIt();
		
		switch ( $name )
		{		
			case 'TestExecution':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceTestExecution') );

			case 'TestFound':
				if ( is_object($this->object_it) && !$this->getEditMode() ) {
					return new FieldListOfReferences( $this->object_it->getRef($name) );
				}
				else {
					return parent::createFieldObject( $name );
				}

			case 'HelpPage':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceHelpPage') );

			case 'TestScenario':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceTestScenario') );

			case 'Requirement':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceRequirement') );

			case 'SourceCode':
				return new FieldIssueCode($this->object_it);
				
			case 'Question':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceQuestion') );

			case 'Fact':
				return new FieldSpentTimeRequest( $this->object_it );

			case 'Estimation':
                if ( getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->hasDiscreteValues() ) {
                    return new FieldEstimationDictionary($this->getObject());
                }
                return parent::createFieldObject( $name );

			case 'Tasks':
				$field = new FieldTasksRequest( $this->object_it );
                $field->setRelease($this->getFieldValue('PlannedRelease'));
                return $field;

			case 'Deadlines':
				return new FieldIssueDeadlines( $this->object_it );
					
			case 'Tags':
				return new FieldRequestTagTrace( is_object($this->object_it)
					? $this->object_it : null ); 
				
			case 'Caption':
				if ( !$this->getEditMode() ) {
    				$field = new FieldTextEditable();
     				is_object($this->object_it) ?
    					$field->setObjectIt( $this->object_it ) : 
    						$field->setObject( $this->getObject() );
			    }
			    else {
			        $field = parent::createFieldObject($name);
			    }
			    return $field;
			    
			case 'Function':
                $field = new FieldHierarchySelectorAppendable(getFactory()->getObject('FeatureHasIssues'));
                $field->setTreeObject(getFactory()->getObject('Feature'));
                return $field;

			case 'LinksAttachment':
				return new FieldAttachments( is_object($this->links_it) ? $this->links_it : $this->object );

            case 'ProjectPage':
                if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get($name) != '' ) {
                    return new FieldListOfReferences( $this->getObjectIt()->getRef($name) );
                }
                return null;

            case 'Priority':
                if ( $this->getAction() == 'view' ) {
                    return new FieldPriority($this->getObjectIt(), $this->fieldActions[$name]);
                }
                else {
                    return parent::createFieldObject($name);
                }

            case 'Type':
            case 'Severity':
                if ( $this->getAction() == 'view' ) {
                    return new FieldReferenceAttribute(
                        $this->getObjectIt(),
                        $name,
                        $this->getObject()->getAttributeObject($name),
                        $this->fieldActions[$name]
                    );
                }
                else {
                    return parent::createFieldObject($name);
                }

            case 'Owner':
                if ( $this->getAction() != 'view' ) {
                    return new FieldParticipantDictionary($this->getFieldValue($name));
                }
                return parent::createFieldObject($name);

            case 'PlannedRelease':
            case 'Iteration':
                if ($this->getAction() == 'view') {
                    return new FieldReferenceAttribute(
                        $this->getObjectIt(),
                        $name,
                        $this->getObject()->getAttributeObject($name),
                        array(),
                        'btn-xs'
                    );
                } else {
                    return new FieldAutoCompleteObject($this->getObject()->getAttributeObject($name));
                }

            case 'Components':
                return new FieldComponentInverseTrace( $this->getObjectIt(),
                    getFactory()->getObject('ComponentInversedTraceRequest') );

        }
		
		if( $name == 'Attachment' )
		{
			return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		elseif( $name == 'Watchers' )
		{
			return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		else if( $name == 'Links' )
		{
			return new FieldLinkedRequest( $this->object_it );
		}
		elseif($name == 'Author')
		{
			return new FieldAuthor();
		}
		else
		{
			return parent::createFieldObject( $name );
		}
	}	
	
    function createField( $name )
    {
		$field = parent::createField( $name );
		
   		switch ( $name )
   		{
   			case 'TestExecution':
                $field->setReadonly( true );
   			    break;
   				
   			case 'Caption':
   		   		if ( is_a($field, 'FieldText') ) {
   			        $field->setRows( 1 );
   			    }
   			    if ( $this->getTransitionIt()->getId() > 0 ) {
   			        $field->setReadonly( true );
   			    }
   			    break;
   			    
   			case 'Description':
   			    if ( is_a($field, 'FieldText') ) {
   			        $field->setRows( 6 );
   			    }
   		        if ( $field instanceof FieldWYSIWYG ) {
					if ( !$this->getEditMode() ) $field->setCssClassName( 'wysiwyg-text' );
					$field->setRows(15);
		    	}
   			    break;

			case 'LinksAttachment':
				$field->setReadonly(true);
				break;

            case 'Fact':
                if ( !getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Activity')) ) return null;
                break;

            case 'Estimation':
                if ( $field instanceof FieldNumber ) {
                    $field->setDimension(getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getDimensionText(''));
                    $field->setDecimals(0);
                }
                break;
        }

    	return $field;
    }
   
   	function getDefaultValue( $attr )
   	{
   		$value = parent::getDefaultValue( $attr );
   		
   		switch( $attr )
   		{
   		    case 'Author':
   		    	if ( $value == '' ) return getSession()->getUserIt()->getId();
   		    	break;
            case 'Priority':
                $severity = parent::getDefaultValue('Severity');
                if ( $severity != '' ) {
                    return $this->getObject()->getAttributeObject($attr)->getExact($severity)->getId();
                }
                return $value;
   		}

   		return $value;
   	}

    function getTextTemplateIt()
    {
        if ( $_REQUEST['Type'] != '' ) {
            return $this->getObject()->getEmptyIterator();
        }
        return parent::getTextTemplateIt();
    }

    function getFieldValue( $attr )
    {
    	switch ( $attr )
    	{
    		case 'Tasks': return '1';
    		
            case 'PlannedRelease':
            case 'Iteration':
                $value = parent::getFieldValue( $attr );
                if ( $value == '' && $_REQUEST['DocumentBaseline'] != '' ) {
                    $baselineIt = getFactory()->getObject('Baseline')->getBySnapshotId($_REQUEST['DocumentBaseline']);
                    if ( $baselineIt->getId() != '' ) {
                        $stageIt = $baselineIt->getStageIt();
                        if ( $stageIt->getId() != '' ) {
                            switch( $attr ) {
                                case 'PlannedRelease':
                                    if ( $stageIt->object instanceof Release) {
                                        $value = $stageIt->getId();
                                    }
                                    break;
                                case 'Iteration':
                                    if ( $stageIt->object instanceof Iteration) {
                                        $value = $stageIt->getId();
                                    }
                                    break;
                            }
                        }
                    }
                }
                return $value;

            case 'Owner':
                $value = parent::getFieldValue($attr);
                if ( $value == '' && $_REQUEST['UserGroup'] != '' ) {
                    $userIt = getFactory()->getObject('ProjectUser')->getRegistry()->Query(
                        array(
                            new ProjectUserGroupPredicate($_REQUEST['UserGroup'])
                        )
                    );
                    $value = $userIt->getId();
                }
                return $value;
    			
    		default:
    			if ( $_REQUEST['Question'] > 0 ) {
        			$question_it = getFactory()->getObject('pm_Question')->getExact($_REQUEST['Question']);
        			switch ( $attr ) {
        				case 'Description':
        					return $question_it->get_native('Content');
        				case 'Author':
        					return $question_it->get('Author');
        			}
        		}

    			if ( $_REQUEST['Requirement'] > 0 && $attr == 'Description' ) {
                    list($source_it, $dummy) = array_shift($this->getSourceIt());
                    $service = new WikiDeltaService(getFactory());
                    return $service->execute($source_it);
                }
    		    
    			return parent::getFieldValue( $attr );
    	}
    }

    function IsAttributeVisible($attr)
    {
        switch( $attr ) {
            case 'Question':
                return $this->getFieldValue($attr) != '';
            default:
                return parent::IsAttributeVisible($attr);
        }
    }

    function IsAttributeEditable( $attribute )
	{
	    switch( $attribute )
	    {
			case 'ExternalAuthor': return false;
			case 'DeliveryDate': return false;

			default: return parent::IsAttributeEditable( $attribute );
	    }
	}
	
	function getDeleteActions( $objectIt )
	{
		$actions = parent::getDeleteActions($objectIt);
		if ( !is_object($objectIt) ) return $actions;

		return $this->methods->getDeleteActions($objectIt, $actions);
	}
	
 	function getMoreActions()
	{
		$actions = parent::getMoreActions();

		$object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return $actions;

        return $this->methods->getMoreActions($object_it, $actions);
	}

	function getNewRelatedActions()
	{
        $actions = parent::getNewRelatedActions();

        $object_it = $this->getObjectIt();
        if ( !is_object($object_it) ) return $actions;

        return $this->methods->getNewRelatedActions($object_it, $actions);
	}

    function getExportActions( $object_it )
    {
        $actions = parent::getExportActions($object_it);

        $object_it = $this->getObjectIt();
        if ( !is_object($object_it) ) return $actions;

        return $this->methods->getExportActions($object_it, $actions);
    }

	function getRenderParms()
	{
		$object_it = $this->getObjectIt();

		$parms = array (
			'comments_count' =>
					is_object($object_it)
							? getFactory()->getObject('Comment')->getCountForIt($object_it)
							: '',
			'refs_actions' => 
					is_object($object_it) 
							? $this->buildReferencesActions( $object_it, array (
                                    'function' => 'Function',
                                    'author' => 'Author',
                                    'subversion' => 'SubmittedVersion',
                                    'version' => 'ClosedInVersion',
                                    'environment' => 'Environment'
                                ))
							: array()
		);
		
		return array_merge( parent::getRenderParms(), $parms ); 
	}

	function buildReferencesWidget() {
        return getFactory()->getObject('PMReport')->getExact('allissues');
    }

	function buildReferencesActions( $object_it, $references )
	{
		$refs_actions = array();
		
		$widgetIt = $this->buildReferencesWidget();

	    $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
        while( !$attr_it->end() )
        {
            if ( in_array($attr_it->getRef('AttributeType')->get('ReferenceName'), array('reference')) ) {
            	$references[strtolower($attr_it->get('ReferenceName'))] = $attr_it->get('ReferenceName');
            } 
            $attr_it->moveNext();
        }

		foreach( $references as $parm => $reference )
		{
            if ( $object_it->object->getAttributeType($reference) == '' ) continue;
            if ( $object_it->object->IsReference($reference) && $object_it->get($reference) != '' ) {
                $ref_it = $object_it->getRef($reference);
                if ( $ref_it->getId() == '' ) continue;

                if ( $ref_it->object instanceof IssueAuthor ) {
                    $refObject = getFactory()->getObject($ref_it->get('CustomerClass'));
                    if ( is_object($refObject) ) {
                        $ref_it = $refObject->getExact($ref_it->get('CustomerId'));
                    }
                }
                if ( $object_it->object->getAttributeOrigin($reference) != ORIGIN_CUSTOM && !$ref_it->object instanceof User ) {
                    $refs_actions[$reference]['modify'] = array (
                        'name' => translate('Изменить'),
                        'url' => "javascript:processBulk('".$object_it->object->getAttributeUserName($reference)."','"
                            .$object_it->getEditUrl()."&formonly=true&operation=Attribute".$reference."','".$object_it->getId()."', devpromOpts.updateUI);"
                    );
                    $method = new ObjectModifyWebMethod($ref_it);
                    $refs_actions[$reference][] = array (
                        'name' => translate('Открыть'),
                        'url' => $method->getJSCall()
                    );
                    $refs_actions[$reference][] = array();
                }
            }

            if ( $reference == 'Type' && $object_it->object->getAttributeType('Type') != '' ) {
                $refs_actions[$reference][] = array(
                    'name' => $this->getSameItemsText(),
                    'url' => $widgetIt->getUrl('type=' . $object_it->getRef('Type')->get('ReferenceName'))
                );
            }
            else {
                $refs_actions[$reference][] = array(
                    'name' => $this->getSameItemsText(),
                    'url' => $widgetIt->getUrl($parm.'='.urlencode($object_it->get($reference)))
                );
            }
		}

   		return $refs_actions;
	}

	function getSameItemsText() {
	    return text(1828);
    }

	function getTemplate()
    {
        if ( $this->getAction() == 'view' && $_REQUEST['formonly'] == '' ) {
            return "pm/RequestPageForm.php";
        }
        return parent::getTemplate();
    }

    function showDescriptionOnRight() {
	    return $this->IsAttributeVisible('TransitionComment') && $this->getObjectIt()->get('Description') != '';
    }

    protected function getSourceParms($attributes)
    {
        $visibleAttributes = array_filter($attributes, function($item) {
            return $item['visible'] && $item['id'] == 'pm_ChangeRequestFact';
        });
        if ( count($visibleAttributes) == 1 ) return array();

        return parent::getSourceParms($attributes);
    }

	function getSourceIt()
	{
	    $result = array();

        if ( $this->showDescriptionOnRight() ) {
            $result[] = array(
                $this->getObjectIt(),
                'Description'
            );
        }

		if ( $_REQUEST['Requirement'] != '' ) {
		    $registry = new WikiPageRegistryContent(getFactory()->getObject('Requirement'));
		    $parms = array(
                new ParentTransitiveFilter($_REQUEST['Requirement']),
                new SortDocumentClause()
            );

			if ( $_REQUEST['Baseline'] != '' ) {
                $parms[] = new SnapshotItemValuePersister($_REQUEST['Baseline']);
			}
            $result[] = array(
                $registry->Query($parms),
                'WikiIteratorExportHtml'
            );
		}

        if ( $_REQUEST['TestCaseExecution'] != '' ) {
            $object = getFactory()->getObject('TestCaseExecution');
            $result[] = array(
                $object->getRegistry()->Query(
                    array(
                        new FilterInPredicate($_REQUEST['TestCaseExecution'])
                    )
                ),
                'Content'
            );
        }

		return array_merge(parent::getSourceIt(), $result);
	}

    function getFieldDescription( $attr )
    {
        switch( $attr ) {
            case 'Function':
                $report_it = getFactory()->getObject('Module')->getExact('features-list');
                return str_replace('%1', $report_it->getUrl(),
                            str_replace('%2', $report_it->getDisplayName(),
                                text(2263)));
            case 'DeliveryDate':
                if ( is_object($this->getObjectIt()) ) {
                    $method = new DeliveryDateMethod();
                    return $method->getExact($this->getObjectIt()->get('DeliveryDateMethod'))->getDisplayName();
                }
                else {
                    return "";
                }
            default:
                return parent::getFieldDescription($attr);
        }
    }

    function persist()
    {
        $result = parent::persist();

        if ( $result && $_REQUEST['dependencies'] != '' ) {
            $trace = getFactory()->getObject('TaskTraceTask');
            $tasks = $this->mbeddedTasksForm->getAddedTasks();
            foreach( $tasks as $key => $task_id ) {
                if ( $tasks[$key + 1] < 1 ) break;
                getFactory()->mergeEntity($trace, array(
                        'Task' => $tasks[$key + 1],
                        'ObjectId' => $task_id
                    ));
            }
        }

        return $result;
    }
}