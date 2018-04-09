<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH.'pm/views/watchers/FieldWatchers.php';
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";
include_once SERVER_ROOT_PATH.'pm/methods/OpenBrokenTraceWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/methods/ReintegrateWikiPageWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/methods/ReintegrateWikiTraceWebMethod.php';
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

include_once "fields/FieldWikiAttachments.php";
include_once "fields/FieldWikiTagTrace.php";
include_once "fields/FieldWikiTrace.php";
include_once "fields/FieldCompareToContent.php";
include_once "fields/FieldCompareToCaption.php";
include_once "fields/FieldWikiDocumentAttachment.php";
include_once "fields/FieldWikiPageDependency.php";

class PMWikiForm extends PMPageForm
{
	var $review_mode = false;
	var $readonly_mode = false;
	var $form_index = '';

	private $revision_it;
    private $version_it = null;
	private $page_to_compare_it;
	private $document_it;
	private $descriminator_value = null;
	private $trace_actions_template = array();
	private $editable = true;
	private $appendable = true;
	private $append_methods = array();
	private $delete_method = null;
	private $search_text = array();
	private $allTasksReportIt = null;
    private $exportMethods = array();
    private $create_task_actions = array();

	function __construct($object)
	{
		$object->addPersister(new WatchersPersister());

		parent::__construct($object);

		$this->editable = getFactory()->getAccessPolicy()->can_modify($object);
		$this->appendable = getFactory()->getAccessPolicy()->can_create($object);

        $this->buildMethods();
	}

	function getIterator($objectId)
    {
        if ( $this->getEditMode() ) {
            $this->getObject()->addPersister(new WikiPageUsedByPersister());
        }
        return parent::getIterator($objectId);
    }

    protected function buildMethods()
	{
		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( $method->hasAccess() ) {
            $type_it = $this->getTypeIt();
            if ( is_object($type_it) ) {
                while( !$type_it->end() ) {
                    $this->append_methods[] = array(
                        'name' => $type_it->getDisplayName(),
                        'method' => $method,
                        'parms' => array('PageType' => $type_it->getId())
                    );
                    $type_it->moveNext();
                }
            }
            if ( count($this->append_methods) < 1 ) {
                $this->append_methods[] = array(
                    'name' => $this->getAppendActionName(),
                    'method' => $method,
                    'parms' => array()
                );
            }
		}

		$method = new BulkDeleteWebMethod();
		if ($this->checkAccess() && !$this->getReadonly() && $method->hasAccess()) {
			if ($this->IsFormDisplayed() || $this->getReviewMode()) {
				$method->setRedirectUrl('function() { if ( typeof loadContentTree != \'undefined\' ) loadContentTree();}');
			} else {
				$method->setRedirectUrl('donothing');
			}
			$this->delete_method = $method;
		}

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Task'));
        if ( $method->hasAccess() ) {
            $method->setRedirectUrl('donothing');
            $task_types = $this->getTaskTypesRelated();
            if ( count($task_types) > 0 ) {
                $type_it = getFactory()->getObject('TaskType')->getByRefArray(
                    array (
                        'ReferenceName' => $task_types
                    )
                );
            }
            $this->create_task_actions[] = array(
                'name' => $method->getCaption(),
                'method' => $method,
                'type' => count($task_types) > 0 ? $type_it->getId() : 0
            );
        }

        $report_it = getFactory()->getObject('Module')->getExact('tasks-list');
		if (getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && getFactory()->getAccessPolicy()->can_read($report_it)) {
			$this->allTasksReportIt = $report_it;
		}

        $method = $this->buildExportWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array (
                    'WikiPageId' => '%id%'
                )
            )
        );
		$converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $this->exportMethods[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url(
                            $methodPageIt,
                            $converter_it->get('EngineClassName'),
                            $converter_it->get('Caption'),
                            '%baseline%'
                        )
            );
            $converter_it->moveNext();
        }
	}

	protected function extendModel()
	{
		$object = $this->getObject();

		foreach (array('OrderNum', 'ContentEditor') as $attribute) {
			$object->setAttributeVisible($attribute, false);
		}

		$object->setAttributeVisible('ParentPage', $this->getEditMode());
		$object->setAttributeVisible('PageType', is_object($this->getTypeIt()));

        foreach (array('Caption', 'Content', 'Attachments') as $attribute) {
            $object->setAttributeVisible($attribute, true);
        }
        if ( $_REQUEST['formonly'] == true ) {
            foreach (array('Watchers', 'Tags') as $attribute) {
                $object->setAttributeVisible($attribute, true);
            }
        }

		$object_it = $this->getObjectIt();
		if ( is_object($object_it) ) {
			if ( $object_it->get('Dependency') != '' ) {
				$object->setAttributeVisible('Dependency', true);
			}
			if ( $object_it->get('Feature') != '' ) {
				$object->setAttributeVisible('Feature', true);
			}
		}

		parent::extendModel();
	}

	function setSearchText($text) {
		$this->search_text = $text;
	}

	protected function buildExportWebMethod() {
	    return new WikiExportBaseWebMethod();
    }

	function getDiscriminatorField()
	{
		return 'PageType';
	}

	function getDiscriminator()
	{
		if (!is_null($this->descriminator_value)) return $this->descriminator_value;

		$value = $this->getFieldValue($this->getDiscriminatorField());

		if ($value == '') return $this->descriminator_value = '';

		return $this->descriminator_value =
				$this->getObject()->getAttributeObject($this->getDiscriminatorField())
						->getExact($value)->get('ReferenceName');
	}

	function getRedirectUrl()
	{
		if ($this->getAction() == 'delete') {
			$object_it = $this->getObjectIt();

			$root_it = $object_it->getRootIt();

			if ($root_it->getId() > 0) {
				$uid = new ObjectUID();

				$info = $uid->getUidInfo($root_it);

				return $info['url'];
			} else {
				return $this->getObject()->getPage();
			}
		}

		$object_it = $this->getObjectIt();

		if (is_object($object_it)) {
            $uid = new ObjectUID();
            $info = $uid->getUidInfo($object_it);
            return $info['url'];
		}

		return '';
	}

	function processEmbeddedForms($object_it, $callback = null )
	{
		if (!is_object($object_it)) {
			throw new Exception('Trying to process empty iterator object');
		}
		if ($object_it->getId() == '') {
			throw new Exception('Trying to process empty object');
		}
		parent::processEmbeddedForms($object_it, $callback);
	}

	function getTraceObject()
	{
		return getFactory()->getObject('WikiPageTrace');
	}

	function getAppendMethods()
	{
		return $this->append_methods;
	}

	function setFormIndex($index)
	{
		$this->form_index = $index;
	}

	function getAppendActionName()
	{
		return $this->getObject()->getDisplayName();
	}

	function setReviewMode()
	{
		$this->review_mode = true;
	}

	function getReviewMode()
	{
		return $this->review_mode;
	}

	function setReadonly($readonly = true)
	{
		$this->readonly_mode = $readonly;
	}

	function getReadonly()
	{
		return $this->readonly_mode;
	}

	function setRevisionIt($revision_it)
	{
		$this->revision_it = $revision_it;
	}

	function getRevisionIt()
	{
		return $this->revision_it;
	}

	function setVersionIt( $version_it ) {
	    $this->version_it = $version_it;
    }

    function getVersionIt() {
        return $this->version_it;
    }

	function setCompareTo($page_it)
	{
		$this->page_to_compare_it = $page_it;
	}

	function getCompareTo()
	{
		return $this->page_to_compare_it;
	}

	function setDocumentIt($document_it)
	{
		$this->document_it = $document_it;
	}

	function getDocumentIt()
	{
		return $this->document_it;
	}

	function IsWatchable($page_it)
	{
		return true;
	}

	function getTraceActionsTemplate()
	{
		if (count($this->trace_actions_template) > 0) return $this->trace_actions_template;

		return $this->trace_actions_template = $this->buildTraceActionsTemplate();
	}

	function buildTraceActionsTemplate()
	{
		$actions = array();

		$report_it = getFactory()->getObject('Module')->getExact('tasks-list');
		if (getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && getFactory()->getAccessPolicy()->can_read($report_it)) {
			$actions[] = array(
				'name' => translate('Задачи'),
				'url' => $report_it->getUrl() . '&state=all&trace=' . strtolower(get_class($this->getObject())) . ':%page-id%'
			);
		}

		return $actions;
	}

	function getNewRelatedActions()
	{
		$actions = array();
        if ( is_object($this->getCompareTo())) return array();

		$page_it = $this->getObjectIt();

        if ( count($this->create_task_actions) > 0 && is_object($page_it) && !$this->getObject() instanceof ProjectPage) {
            $task_parms = array (
                'Caption' => $page_it->getHtmlDecoded('CaptionLong'),
                get_class($this->getObject()) => $page_it->getId(),
                'Iteration' => is_object($this->getVersionIt()) ? $this->getVersionIt()->get('Release') : ''
            );
            $task_actions = array();
            foreach( $this->create_task_actions as $action ) {
                $method = $action['method'];
                $task_actions[] = array(
                    'name' => $action['name'],
                    'url' => $method->getJSCall( array_merge($task_parms, array(
                        'TaskType' => $action['type']
                    ))),
                    'uid' => 'implement-task-'.$action['type']
                );
            }
            $actions = array_merge($actions, array(array()), $task_actions);

            $method = new ObjectModifyWebMethod($page_it);
            if ( $method->hasAccess() ) {
                $method->setRedirectUrl('donothing');
                $actions[] = array();
                $actions[] = array(
                    'name' => translate('Трассировка'),
                    'url' => $method->getJSCall(array('tab'=>2))
                );
            }
        }

        $not_readonly = !$this->getReadonly() && !$this->getEditMode() && $this->getReviewMode();
		if ($this->appendable && $not_readonly) {
            $appendActions = array();
			foreach ($this->append_methods as $action) {
				$method = $action['method'];
				$method->setRedirectUrl('donothing');
				if ( is_object($page_it) ) {
					$method->setVpd($page_it->get('VPD'));
				}
				$parms = array_merge($action['parms'],
						array(
                            'ParentPage' => is_object($page_it) ? $page_it->getId() : ''
						));
				$action['url'] = $method->getJSCall($parms);
				$action['uid'] = 'append-child-page';
                $appendActions[] = $action;
			}

			$actions[] = array();
            $actions['children'] = array(
                'name' => translate('Дочерние'),
                'items' => $appendActions
            );
		}

		return $actions;
	}

	function getDeleteActions($object_it = null)
	{
		$actions = array();
		if (!is_object($object_it)) $object_it = $this->getObjectIt();
        if ( is_object($this->getCompareTo())) return array();

		if (is_object($this->delete_method) && is_object($object_it)) {
			$actions[] = array(
					'name' => $this->delete_method->getCaption(),
					'url' => $this->delete_method->url(
							$object_it->object,
							$object_it->getId()
					)
			);
		}

		return $actions;
	}

	function getExportActions( $object_it )
	{
		$actions = array();

        foreach( $this->exportMethods as $action ) {
            $ids = $object_it->idsToArray();
            $parms = join('-',$ids);
            if ( $parms != '0' ) {
                $action['url'] = preg_replace('/%(id|ids)%/', $parms, $action['url']);
            }
            $action['url'] = preg_replace('/%baseline%/', is_object($this->getCompareTo()) ? $this->getCompareTo()->getId() : '0', $action['url']);
            $actions[] = $action;
        }

        return $actions;
	}

	function getTraceActions($page_it)
	{
		$actions = array();

		if ( is_object($this->allTasksReportIt) ) {
			$actions[] = array(
				'name' => translate('Задачи'),
				'url' => $this->allTasksReportIt->getUrl('state=all&trace=' . get_class($page_it->object) . ':' . join(',', $page_it->idsToArray()))
			);
		}

		return $actions;
	}

	function getActions($page_it = null)
	{
		if (!is_object($page_it)) $page_it = $this->getObjectIt();
		if (!is_object($page_it)) return array();

		$actions = parent::getActions();
		if ($actions[array_pop(array_keys($actions))]['name'] != '') $actions[] = array();

        $page_it->object->setVpdContext($page_it->get('VPD'));
		$history_url = $page_it->getHistoryUrl();

		if (is_object($this->getRevisionIt()) && $this->getRevisionIt()->getId() != '') {
			$history_url .= '&start=' . $this->getRevisionIt()->getDateTimeFormat('RecordCreated');
		}
		$actions['history'] = array(
				'name' => text(2238),
				'url' => $history_url,
				'uid' => 'history'
		);
        $history_url = $page_it->getPageVersions();
        if ( $history_url != '' ) {
            $actions['compare'] = array(
                'name' => text(2237),
                'url' => $history_url,
                'uid' => 'compare'
            );
        }

		$trace_actions = $this->getTraceActions($page_it);
		if (count($trace_actions) > 0) {
			if ($actions[array_pop(array_keys($actions))]['name'] != '') $actions[] = array();

			array_push($actions, array(
				'id' => 'tracing',
				'name' => translate('Трассировка'),
				'items' => $trace_actions
			));
		}

        $export_actions = $this->getExportActions( $page_it );
        if ( count($export_actions) > 0 ) {
            if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
            $actions[] = array(
                'name' => translate('Экспорт'),
                'items' => $export_actions,
                'uid' => 'export'
            );
        }

		if ($this->IsWatchable($page_it)) {
			$watch_method = new WatchWebMethod($page_it);
			if ($watch_method->hasAccess()) {
				$watch_method->setRedirectUrl('donothing');
				$actions[] = array();
				$actions[] = array(
						'name' => $watch_method->getCaption(),
						'url' => $watch_method->getJSCall()
				);
			}
		}
		return $actions;
	}

	function getCompareActions($object_it)
	{
	    $actions = array();
		if (!is_object($object_it)) return $actions;

		if ( !$object_it->IsPersisted() && $object_it->get('ParentPage') != '' && is_object($this->getCompareTo()) )
		{
            $trace_it = $this->getTraceObject()->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('SourcePage', $this->getCompareTo()->get('ParentPage')),
                    new FilterAttributePredicate('Type', 'branch'),
                    new WikiTraceTargetDocumentPredicate($this->getDocumentIt()->getId())
                )
            );
            if ($trace_it->getId() != '') {
                $method = $this->getDuplicateMethod($object_it);
                if (!is_object($method)) return array();

                $parms = array(
                    'class' => get_class($object_it->object),
                    'objects' => $object_it->getId(),
                    'CopyOption' => 'on',
                    'parent' => $trace_it->get('TargetPage'),
                    'Project' => getSession()->getProjectIt()->getId()
                );

                $script = "javascript: runMethod('".getSession()->getApplicationUrl($object_it)."methods.php?method=" . get_class($method) . "', " . str_replace('"', "'", JsonWrapper::encode($parms)) . ", function(){window.location.reload();}, '')";
                $actions[] = array(
                    'url' => $script,
                    'name' => text(1735)
                );
            }
		}

		if ( is_object($this->getCompareTo()) && $object_it->get('Content') != $this->getCompareTo()->get('Content') )
		{
            $trace_it = $this->getTraceObject()->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('SourcePage', $object_it->getId()),
                    new FilterAttributePredicate('TargetPage', $this->getCompareTo()->getId())
                )
            );
            if ($trace_it->count() > 0 ) {
                $method = new ReintegrateWikiTraceWebMethod($trace_it);
                if ( $method->hasAccess() ) {
                    $actions[] = array(
                        'url' => $method->getJSCall(
                                    array(
                                        'className' => get_class($this->getObject())
                                    )
                                 ),
                        'name' => $method->getCaption()
                    );
                }
            }
            else if ( $object_it->getId() == '' )
            {
                $compareParentIt = getFactory()->getObject('WikiPage')->getExact(
                    $this->getCompareTo()->get('ParentPage')
                );
                $parentIt = $this->getObject()->getRegistryBase()->Query(
                    array(
                        new FilterAttributePredicate('UID', $compareParentIt->get('UID')),
                        new FilterAttributePredicate('DocumentId', $this->getDocumentIt()->getId())
                    )
                );
                $method = new ReintegrateWikiPageWebMethod($object_it);
                if ( $method->hasAccess() ) {
                    $actions[] = array(
                        'url' => $method->getJSCall(
                                    array(
                                        'parent' => $parentIt->getId(),
                                        'className' => get_class($this->getObject()),
                                        'traceClass' => get_class($this->getTraceObject())
                                    )
                                ),
                        'name' => $method->getCaption()
                    );
                }
            }
        }

        if ($object_it->get('BrokenTraces') != '') {
            $trace_it = $this->getTraceObject()->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('SourcePage', preg_split('/,/', $object_it->get('BrokenTraces'))),
                    new FilterAttributePredicate('TargetPage', $object_it->getId())
                )
            );
            if ($trace_it->count() < 1) return $actions;

            while (!$trace_it->end()) {
                $trace_actions = array();

                $method = new OpenBrokenTraceWebMethod();
                $actions[] = array(
                    'name' => text(1933),
                    'url' => $method->getJSCall(array('object' => $object_it->getId()))
                );
                $actions[] = array();

                if ($trace_it->get('Type') == 'branch' && $trace_it->get('UnsyncReasonType') == 'text-changed') {
                    $method = new SyncWikiLinkWebMethod($trace_it);
                    $method->setRedirectUrl("donothing");
                    $actions[] = array(
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );

                    $method = new IgnoreWikiLinkWebMethod($trace_it);
                    $method->setRedirectUrl("donothing");
                    $actions[] = array(
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );
                } else {
                    $method = new ActuateWikiLinkWebMethod($trace_it);
                    $method->setRedirectUrl("donothing");
                    $actions[] = array(
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );
                }
                $trace_it->moveNext();
            }
        }

		return $actions;
	}

	function getDuplicateMethod($object_it)
	{
		return null;
	}

	function getTreeMenu($object_it)
	{
		$actions = array();
		$actions[] = array(
				'name' => text(1556),
				'url' => $object_it->getViewUrl(),
				'uid' => 'open-new'
		);
		$actions[] = array();
		$actions[] = array(
				'name' => translate('Перейти'),
				'url' => "javascript: gotoRandomPage(" . $object_it->getId() . ", 4, true)"
		);

		$method = new ObjectModifyWebMethod($object_it);
		$method->setRedirectUrl('donothing');
		if (!$this->getReadonly() && $method->hasAccess()) {
			if ($actions[count($actions) - 1]['name'] != '') array_push($actions, array());
			$actions[] = array(
					'name' => translate('Редактировать'),
					'url' => $method->getJSCall()
			);
		}

		$method = new ObjectCreateNewWebMethod($this->getObject());
		$method->setRedirectUrl('donothing');
		if (!$this->getReadonly() && $method->hasAccess()) {
			if ($actions[count($actions) - 1]['name'] != '') array_push($actions, array());
			$method->setVpd($object_it->get('VPD'));
			$actions[] = array(
					'name' => translate('Добавить'),
					'url' => $method->url(array('ParentPage' => $object_it->getId()))
			);
		}

		$delete_actions = $this->getDeleteActions($object_it);
		if (count($delete_actions) > 0) {
			$actions[] = array();
			$actions = array_merge($actions, $delete_actions);
		}

		return $actions;
	}

	function getEditor()
	{
		$object_it = $this->getObjectIt();

		$type_it = $this->getTypeIt();

		$editor_class = is_object($object_it) ? $object_it->get('ContentEditor') : "";

		if (is_object($type_it) && $_REQUEST['PageType'] != '') {
			$type_id = $_REQUEST['PageType'];

			$type_it->moveToId($type_id);

			if ($type_it->getId() == $type_id) $editor_class = $type_it->get('WikiEditor');
		}

		$editor = WikiEditorBuilder::build($editor_class);

		if (is_object($object_it) && $_REQUEST['wiki_mode'] != 'new') {
			$editor->setObjectIt($object_it);
		} else {
			$editor->setObject($this->getObject());
		}

		return $editor;
	}

	function getTypeIt()
	{
		return null;
	}

	function getDefaultValue( $field )
	{
		$default = parent::getDefaultValue($field);

		switch( $field ) {
			case 'PageType':
				$parent = $this->getFieldValue('ParentPage');
				if ( $default == '' && $parent != '' ) {
					$parent_it = $this->getObject()->getExact($parent);
					return $parent_it->get('PageType');
				}
				break;
		}

		return $default;
	}

    function getTextTemplateIt() {
        $type_id = $_REQUEST['PageType'];
        if ( $type_id != '' ) {
            $type_it = $this->getTypeIt();
            $type_it->moveToId($type_id);
            if ( $type_it->getId() != '' ) {
                return $type_it->getRef('DefaultPageTemplate');
            }
        }
        return parent::getTextTemplateIt();
    }

	function getFieldValue( $field )
	{
		$object_it = $this->getObjectIt();

		switch ( $field )
		{
			case 'ContentEditor':
				return get_class( $this->getEditor() );
				
			case 'Content':
				$editor = $this->getEditor();
				// if the page was created using other editor then convert the data
				if ( is_object($object_it) && $this->getEditMode() && $object_it->get('ContentEditor') != get_class($editor) ) {
				    $editor->setObjectIt($object_it);
				    $parser = $editor->getEditorParser($object_it->get('ContentEditor'));
				    if ( is_object($parser) ) return $parser->parse($object_it->get('Content'));
				}
				break;
				
			case 'Template':
				$template_it = $this->getDefaultTemplateIt();
				if ( is_object($template_it) && $template_it->getId() > 0 ) {
					return $template_it->getId();
				}
				break;
		}
		return parent::getFieldValue( $field );
	}
	 
	function drawButtons()
	{
		$editor = $this->getEditor();
		
		$editor->drawPreviewButton();

		parent::drawButtons();
	}
	
	function getTransitionAttributes()
	{
		return array('Caption', 'UID');
	}
	
	function createField( $name )
	{
		$field = parent::createField( $name );
		if ( !is_object($field) ) return $field;

		if ( $this->getReadonly() )	$field->setReadonly( true );

		switch ( $name )
		{
			case 'Caption':
				$field->setTabIndex( 1 );
				$field->setId( $field->getId().$this->form_index );

		   		if ( $this->getTransitionIt()->getId() > 0 ) {
   			        $field->setReadonly( true );
   			    }
				break;

			case 'Template':
				$field->setTabIndex( 2 );
				break;

			case 'Content':
				$field->setTabIndex( 3 );
				$field->setId( $field->getId().$this->form_index );
				$field->setSearchText($this->search_text);
				break;
		}
		return $field;
	}
	
	function createFieldObject( $name )
	{
		$this->object_it = $this->getObjectIt();

		switch ( $name )
		{		
			case 'Watchers':
				return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'Tags':
				return new FieldWikiTagTrace( is_object($this->object_it)
					? $this->object_it : null ); 
					
			case 'Caption':

				if ( is_object($this->getCompareTo()) )
				{
					return new FieldCompareToCaption( $this->getObjectIt(), $this->getCompareTo() );
				}
				
				if ( !$this->getEditMode() )
			    {
    				$field = new FieldWYSIWYG( get_class($this->getEditor()) );
     					
     				is_object($this->object_it) ? 
    					$field->setObjectIt( $this->object_it ) : 
    						$field->setObject( $this->getObject() );
    						
    			    $field->getEditor()->setMode( WIKI_MODE_INPLACE_INPUT );
			    }
			    else
			    {
			        $field = parent::createFieldObject($name);
			        
			        if ( is_object($field) )
			        {
	 				    $field->setDefault( translate('Название') );
			        }
			    }
 				
				return $field;
				
			case 'Content':
				
				if ( is_object($this->getCompareTo()) )
				{
					return new FieldCompareToContent( $this->getObjectIt(), $this->getCompareTo() );
				}
				
				$field = new FieldWYSIWYG( get_class($this->getEditor()) );

 				is_object($this->object_it) ? 
					$field->setObjectIt( $this->object_it ) : 
						$field->setObject( $this->getObject() );

				$field->setAttachmentsField( new FieldWikiAttachments(
				        is_object($this->object_it) ? $this->object_it : $this->getObject()
				));

				if ( $this->getEditMode() )
				{
					$field->setHasBorder( !$this->checkAccess() || !$this->IsAttributeEditable($name) );
					$field->getEditor()->setMode( WIKI_MODE_NORMAL );
					$field->setRows(20);
				}
				else
				{
					$field->setRows(2);
    				$field->setCssClassName( 'wysiwyg-text' );
				}
				$field->setToolbar(WikiEditorBase::ToolbarFull);

 				return $field;
 				
			case 'PageType':

				if ( is_object($this->getTypeIt()) )
				{
    				return new FieldDictionary( $this->getTypeIt()->object );
				}
				else
				{
				    return null;
				}
				
			case 'Attachments':
				return new FieldWikiAttachments( is_object($this->object_it) ? $this->object_it : $this->getObject() );

			case 'ParentPage':
			    $object = $this->object->getAttributeObject($name);
		        $object->addFilter( new FilterBaseVpdPredicate() );
				return new FieldHierarchySelectorAppendable($object);

            case 'Dependency':
				return new FieldWikiPageDependency();
            case 'UsedBy':
			case 'Feature':
				if ( is_object($this->getObjectIt()) ) {
				    $refIt = $this->getObjectIt()->getRef($name);
				    if ( $refIt->getId() != '' ) {
                        return new FieldListOfReferences($refIt);
                    }
				}
				return null;

			default:
				return parent::createFieldObject( $name );
		}
	}

 	function validateInputValues( $id, $action )
	{
		$message = parent::validateInputValues( $id, $action );
		
		if ( $message != '' ) return $message;
		
		// check parent page is correct
		if ( is_object($this->object_it) && $this->getAction() != 'add' )
		{
			if ( $this->object_it->getId() == $_REQUEST['ParentPage'] ) {
				return text(971);
			}
			
			$ids = $this->object_it->getAllChildrenIds();
			if ( in_array( $_REQUEST['ParentPage'], $ids ) ) {
				return text(971);
			}
		}
		
		return '';
	}
		
	function getRenderParms()
	{
		$structureActions = array();
		$object = $this->getObject();
		$object_it = $this->getObjectIt();

        if ( is_object($object_it) && $object_it->IsPersisted() )
		{
			$attachments = new FieldWikiDocumentAttachment( $object_it->copy() );
			$attachments->setBaseline($this->getRevisionIt());
			$attachments->setReadonly( !$this->checkAccess() || $this->getReadonly() );
			$attachments->setEditMode( false );

			if ( !$this->getReadonly() ) {
				$structureActions = $this->getStructureActions( $object_it );
			}
        }

		$parms = array (
			'lifecycle' => is_object($object_it) && is_a($object_it, 'StatableIterator') ? $object_it->IsTransitable() : false,
			'formurl' =>  $object->getPage(),
		    'index' => $this->form_index,
		    'document_mode' => $this->getReviewMode(),
			'baseline' => is_object($this->getRevisionIt()) ? $this->getRevisionIt()->getId() : '',
			'compare_actions' => $this->getCompareActions($object_it),
			'persisted' => is_object($object_it) ? $object_it->IsPersisted() : false,
            'attachments' => $attachments,
            'structureActions' => $structureActions,
			'trace_attributes' =>
				array_merge(
					$this->getObject()->getAttributesByGroup('trace'),
					$this->getObject()->getAttributesByGroup('source-attribute')
				)
		);

		$parent_parms = parent::getRenderParms();

		if ( is_object($object_it) && $object_it->get('IsTemplate') > 0 ) unset($parent_parms['uid_icon']);
		
		return array_merge($parent_parms , $parms ); 
	}

	function getStructureActions( $object_it, $parms = array() )
	{
		$actions = array();

		if ( $object_it->get('TotalCount') < 1 && $object_it->get('ParentPage') != '' ) {
			$new_sibling_method = new ObjectCreateNewWebMethod($this->getObject());
			$new_sibling_method->setVpd($object_it->get('VPD'));
			$new_sibling_method->setRedirectUrl('donothing');

			$parent_id = $object_it->get('ParentPage') != '' ? $object_it->get('ParentPage') : $object_it->getId();
			$sort_index = $object_it->get('OrderNum') + 1;

			$actions['sibling'] = array (
				'name' => $this->getNewSiblingActionName(),
				'url' => $new_sibling_method->url(
							array_merge(
								$parms, array('ParentPage'=>$parent_id,'OrderNum'=>$sort_index)
							)
						 ),
				'icon' => 'icon-resize-vertical',
                'uid' => 'new-sibling'
			);
		}

		$new_child_method = new ObjectCreateNewWebMethod($this->getObject());
		$new_child_method->setVpd($object_it->get('VPD'));
		$new_child_method->setRedirectUrl('donothing');

		$actions['child'] = array (
			'name' => $this->getNewChildActionName(),
			'url' => $new_child_method->url(
							array_merge(
								$parms, array('ParentPage'=>$object_it->getId(),'OrderNum'=>1)
							)
						),
			'icon' => 'icon-resize-horizontal',
            'uid' => $object_it->get('ParentPage') == '' ? 'new-sibling' : 'new-child'
		);

		$attachments_method = new ObjectModifyWebMethod($object_it);
		$attachments_method->setRedirectUrl('donothing');
		$actions['attachments'] = array (
			'name' => text(2082),
			'url' => $attachments_method->getJSCall(
						array_merge(
							$parms, array('tab'=>1)
						)
					 ),
			'icon' => 'icon-file',
            'uid' => 'new-tag-file'
		);

		return $actions;
	}

	function getNewChildActionName() {
	    return text(2091);
    }

    function getNewSiblingActionName() {
        return text(2092);
    }

	function getTemplate()
	{
	    if( !$this->getEditMode() )
	    {
	    	return "pm/WikiPageForm.php";
	    }
	    
	    return parent::getTemplate();
	}

	function getSourceIt()
	{
	    $result = array();
		if ( $_REQUEST['Request'] != '' ) {
            $result[] = array (
				getFactory()->getObject('Request')->getExact($_REQUEST['Request']),
				'Description'
			);
		}
		if ( $_REQUEST['Task'] != '' ) {
			$task_it = getFactory()->getObject('Task')->getExact($_REQUEST['Task']);
			if ( $task_it->get('ChangeRequest') == '' ) return parent::getSourceIt();
            $result[] = array (
				$task_it->getRef('ChangeRequest'),
				'Description'
			);
		}
		if ( $_REQUEST['Requirement'] != '' && $_REQUEST['Request'] == '' ) {
			$req = getFactory()->getObject('Requirement');
			if ( $_REQUEST['Baseline'] != '' ) {
				$req->addPersister(
						new SnapshotItemValuePersister($_REQUEST['Baseline'])
				);
			}
            $result[] = array ($req->getExact($_REQUEST['Requirement']),'WikiIteratorExportHtml');
		}
		return array_merge(parent::getSourceIt(), $result);
	}

    function getShortAttributes()
    {
        return array_merge(
            parent::getShortAttributes(),
            array('PageType', 'Template', 'Importance')
        );
    }

    function getTaskTypesRelated()
    {
        return array();
    }
}