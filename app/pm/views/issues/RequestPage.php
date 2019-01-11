<?php
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderIssues.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/TransitionCommentPersister.php";

include SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";
include SERVER_ROOT_PATH.'pm/views/import/ImportXmlForm.php';

include "RequestForm.php";
include "RequestFormDuplicate.php";
include "RequestFormLinked.php";
include "RequestTable.php";
include_once "RequestBulkForm.php";
include "RequestPlanningForm.php";
include "IssueCompoundSection.php";
include "IteratorExportIssueBoard.php";
include "PageSettingIssuesBuilder.php";
include "RequestImportDocumentForm.php";

class RequestPage extends PMPage
{
	function __construct()
	{
		getSession()->addBuilder(new PageSettingIssuesBuilder());
		getSession()->addBuilder(new RequestViewModelCommonBuilder());
		getSession()->addBuilder(new BulkActionBuilderIssues());
        getSession()->addBuilder(new TaskModelExtendedBuilder());

		parent::__construct();

		if ($_REQUEST['view'] == 'chart') return;

		if ( $this->needDisplayForm() && is_object($this->getFormRef()) ) {
			$form = $this->getFormRef();
            $object_it = $this->getObjectIt();

            if ( is_object($object_it) ) {
                $object_it = $object_it->getSpecifiedIt();
                if ( $object_it->object instanceof Issue ) {
                    exit(header('Location: '.$object_it->getViewUrl()));
                }
            }

            if ( $_REQUEST['mode'] == 'group' ) {
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'additional', translate('Дополнительно')));
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'trace', translate('Трассировки')));
                $this->addInfoSection(new PageSectionComments($object_it));
            }
            else {
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'deadlines', translate('Сроки')));
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'sla', 'SLA'));
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'additional', translate('Дополнительно')));
                $this->addInfoSection(new PageSectionAttributes($form->getObject(), 'trace', translate('Трассировки')));

                if (is_object($object_it) && $object_it->getId() > 0) {
                    $this->addInfoSection(new PageSectionComments($object_it));

                    $ids = $object_it->getImplementationIds();
                    if (count($ids) > 0) {
                        $it = $object_it->object->getRegistry()->Query(
                            array(new FilterInPredicate($ids))
                        );
                        while (!$it->end()) {
                            $section = new PageSectionComments($it->copy());
                            $section->setCaption($section->getCaption() . ' {'.$it->get('ProjectCodeName').'} I-' . $it->getId());
                            $section->setId($section->getId() . $it->getId());
                            $this->addInfoSection($section);
                            $it->moveNext();
                        }
                    }


                    $this->addInfoSection(new StatableLifecycleSection($object_it));
                    $this->addInfoSection(new PMLastChangesSection ($object_it));
                    $this->addInfoSection(new NetworkSection($object_it));
                }
            }
		}
		elseif ($_REQUEST['mode'] == '') {
			if ($_REQUEST['view'] == 'board') $this->addInfoSection(new FullScreenSection());
			$this->addInfoSection(new DetailsInfoSection());
		}
	}

	function getObject()
	{
		$object = getFactory()->getObject('Request');

		foreach (getSession()->getBuilders('RequestViewModelBuilder') as $builder) {
			$builder->build($object);
		}

		if ($_REQUEST['view'] != 'chart') {
			$builder = new RequestModelExtendedBuilder();
			$builder->build($object);

			$builder = new RequestModelPageTableBuilder();
			$builder->build($object);
		}

		return $object;
	}

    function getTable()
	{
		switch ($_REQUEST['kind']) {
			case 'submitted':
				return $this->getDefaultTable();

			default:
				return $this->getDefaultTable();
		}
	}

	function getDefaultTable()
	{
		return new RequestTable($this->getObject());
	}

	function needDisplayForm()
	{
		if ( parent::needDisplayForm() ) return true;
		return $_REQUEST['view'] == 'import' || in_array($_REQUEST['mode'], array('bulk', 'group'));
	}

	function getBulkForm()
	{
		return new RequestBulkForm($this->getObject(), \RequestForm::class);
	}

	function getForm()
	{
		switch ($_REQUEST['mode']) {
			case 'group':
				$form = new RequestPlanningForm($this->getObject());
				$form->edit($_REQUEST['ChangeRequest']);
				return $form;
		}
		if ( $_REQUEST['view'] == 'import' ) {
			return new ImportXmlForm($this->getObject());
		}
		if ( $_REQUEST['view'] == 'importdoc') {
            return new RequestImportDocumentForm($this->getObject());
        }
        if ( $_REQUEST['IssueLinked'] != '' ) {
            return new RequestFormLinked($this->getObject());
        }
		if ( $_REQUEST['LinkType'] != '' ) {
			return new RequestFormDuplicate($this->getObject());
		}

		$object = $this->getObject();
		$object->addPersister(new TransitionCommentPersister());

		return new RequestForm($object);
	}

	function getPageWidgets()
	{
		return array('kanbanboard', 'issuesboard', 'issues-backlog');
	}

    function buildExportIterator( $object, $ids, $iteratorClassName )
    {
        $iterator = parent::buildExportIterator($object, $ids, $iteratorClassName);
        if ( is_subclass_of($iteratorClassName, 'WikiIteratorExport') ) {
            $data = array();
            while( !$iterator->end() ) {
                $data[] = array(
                    'WikiPageId' => $iterator->getId(),
                    'Caption' => $iterator->getDisplayName(),
                    'Content' => $iterator->get('Description'),
                    'ContentEditor' => getSession()->getProjectIt()->get('WikiEditorClass'),
                    'UID' => 'I-'.$iterator->getId()
                );
                $iterator->moveNext();
            }
            $_REQUEST['options'] = 'UseUID';
            return getFactory()->getObject('WikiPage')->createCachedIterator($data);
        }
        return $iterator;
    }
}