<?php
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderIssues.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTaskTypeModelBuilder.php';
include_once SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";


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
                    $url = $object_it->getViewUrl();
                    if ( $_REQUEST['attributesonly'] != '' ) {
                        $url .= '&attributesonly=true';
                    }
                    exit(header('Location: '.$url));
                }
            }

            if ( $_REQUEST['mode'] == 'group' ) {
                $this->setInfoSections(array());
                $object = $form->getObject();
                $object->addAttributeGroup('Description', 'description');
                $this->addInfoSection(new PageSectionAttributes($object, 'description', translate('Описание')));
                $this->addInfoSection(new PageSectionAttributes($object, 'additional', translate('Дополнительно')));
                $this->addInfoSection(new PageSectionAttributes($object, 'trace', translate('Трассировки')));
                $this->addInfoSection(new PageSectionComments($object_it, $this->getCommentObject()));
            }
            else {
                if (is_object($object_it) && $object_it->getId() > 0) {
                    $this->addInfoSection(new PageSectionComments($object_it, $this->getCommentObject()));

                    $ids = $object_it->getImplementationIds();
                    if (count($ids) > 0 && $_REQUEST['formonly'] == '') {
                        $uidService = new ObjectUID();
                        $it = getFactory()->getObject('Request')->getRegistry()->Query(
                            array(new FilterInPredicate($ids))
                        );
                        while (!$it->end()) {
                            $section = new PageSectionComments($it->copy(), $this->getCommentObject());
                            $uid = $it->get('UID');
                            if ( $uid == '' ) $uid = $uidService->getObjectUidInt(get_class($it->object), $it->getId());
                            $section->setCaption($section->getCaption() . ' {'.$it->get('ProjectCodeName').'} ' . $uid);
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
    }

	function getObject()
	{
		$object = getFactory()->getObject('Request');

		foreach (getSession()->getBuilders('RequestViewModelBuilder') as $builder) {
			$builder->build($object);
		}
        if ( $this->getReportBase() == 'issuesestimation' ) {
            $builder = new RequestTaskTypeModelBuilder();
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

    function getCommentObject() {
        return new RequestComment();
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
		return in_array($_REQUEST['mode'], array('bulk', 'group'));
	}

	function getBulkForm() {
		return new RequestBulkForm($this->getObject(), \RequestForm::class);
	}

	function getEntityForm()
	{
		switch ($_REQUEST['mode']) {
			case 'group':
				$form = new RequestPlanningForm($this->getObject());
				$form->edit($_REQUEST['ChangeRequest']);
				return $form;
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
		return new RequestForm($object);
	}

	function getPageWidgets() {
		return array('kanbanboard', 'issues-board', 'issues-backlog');
	}

    function buildExportIterator( $object, $ids, $iteratorClassName, $queryParms )
    {
        $iterator = parent::buildExportIterator($object, $ids, $iteratorClassName, $queryParms);
        if ( is_subclass_of($iteratorClassName, 'WikiIteratorExport') ) {
            $data = array();
            while( !$iterator->end() ) {
                $data[] = array(
                    'WikiPageId' => $iterator->getId(),
                    'Caption' => $iterator->getDisplayName(),
                    'Content' => $iterator->get('Description'),
                    'ContentEditor' => getSession()->getProjectIt()->get('WikiEditorClass'),
                    'UID' => $iterator->get('UID')
                );
                $iterator->moveNext();
            }
            return getFactory()->getObject('WikiPage')->createCachedIterator($data);
        }
        return $iterator;
    }
}