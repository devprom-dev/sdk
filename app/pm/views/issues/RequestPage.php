<?php

include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderIssues.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/TransitionCommentPersister.php";

include SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";
include SERVER_ROOT_PATH.'pm/views/import/ImportXmlForm.php';

include "RequestForm.php";
include "RequestFormDuplicate.php";
include "RequestTable.php";
include "RequestBulkForm.php";
include "RequestPlanningForm.php";
include "IssueBurndownSection.php";
include "IssueEstimationSection.php";
include "IssueCompoundSection.php";
include "RequestIteratorExportBlog.php";
include "IteratorExportIssueBoard.php"; 
include "PageSettingIssuesBuilder.php";
include "PageSectionSpentTime.php";
include "import/ImportIssueFromExcelSection.php";

class RequestPage extends PMPage
{
	var $release_it;

	function __construct()
	{
		global $_REQUEST, $model_factory;

		getSession()->addBuilder(new PageSettingIssuesBuilder());
		getSession()->addBuilder(new RequestViewModelCommonBuilder());
		getSession()->addBuilder(new BulkActionBuilderIssues());

		if ($_REQUEST['release'] > 0) {
			$release = $model_factory->getObject('Release');

			$this->release_it = $release->getExact($_REQUEST['release']);
		}

		parent::__construct();

		if ($_REQUEST['view'] == 'chart') return;

		if ($this->needDisplayForm()) {
			$form = $this->getFormRef();
			$this->addInfoSection(new PageSectionAttributes($form->getObject(), 'additional', translate('Дополнительно')));
			$this->addInfoSection(new PageSectionAttributes($form->getObject(), 'trace', translate('Трассировки')));

			$object_it = $this->getObjectIt();
			if (is_object($object_it) && $object_it->getId() > 0) {
				$this->addInfoSection(new PageSectionComments($object_it));

				$ids = $object_it->getImplementationIds();
				if (count($ids) > 0) {
					$it = $object_it->object->getRegistry()->Query(
							array(new FilterInPredicate($ids))
					);
					while (!$it->end()) {
						$section = new PageSectionComments($it->copy());
						$section->setCaption($section->getCaption() . ' I-' . $it->getId());
						$section->setId($section->getId() . $it->getId());
						$this->addInfoSection($section);
						$it->moveNext();
					}
				}
				if ($object_it->object->getAttributeType('Spent') != '' && $_REQUEST['formonly'] == '') {
					$this->addInfoSection(new PageSectionSpentTime($object_it));
				}
				$this->addInfoSection(new StatableLifecycleSection($object_it));
				$this->addInfoSection(new PMLastChangesSection ($object_it));
			}
		} elseif ($_REQUEST['mode'] == '') {
			if ($_REQUEST['view'] == 'board') $this->addInfoSection(new FullScreenSection());

			$table = $this->getTableRef();

			if (is_object($table)) {
				$filter = new FilterObjectMethod(
						$model_factory->getObject('Release'), '', 'release');
				$filter->setFilter($table->getFiltersName());

				$value = $filter->getValue();
				if (is_numeric($value) && $value > 0) {
					$release = $model_factory->getObject('Release');
					$this->release_it = $release->getExact($value);
				}
			}

			if (!$this->needDisplayForm() && is_object($table)) {
				$this->addInfoSection(new IssueBurndownSection());
			}
		}

		if ($_REQUEST['view'] == 'import') {
			$this->addInfoSection(new ImportIssueFromExcelSection($this->getObject()));
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

	function getReleaseIt()
	{
		return $this->release_it;
	}

	function getTable()
	{
		switch ($_REQUEST['kind']) {
			case 'submitted':
				return $this->getDefaultTable();

			default:
				if ($_REQUEST['view'] == 'chart' && $_REQUEST['report'] == '') {
					if ($_REQUEST['pmreportcategory'] == '') $_REQUEST['pmreportcategory'] = 'issues';

					return new ReportTable(getFactory()->getObject('PMReport'));
				} else {
					return $this->getDefaultTable();
				}
		}
	}

	function getDefaultTable()
	{
		return new RequestTable($this->getObject());
	}

	function needDisplayForm()
	{
		return $_REQUEST['view'] == 'import' || in_array($_REQUEST['mode'], array('bulk', 'group'))
				? true : parent::needDisplayForm();
	}

	function getBulkForm()
	{
		return new RequestBulkForm($this->getObject());
	}

	function getForm()
	{
		switch ($_REQUEST['mode']) {
			case 'group':
				$form = new RequestPlanningForm($this->getObject());
				$form->edit($_REQUEST['ChangeRequest']);
				return $form;
		}
		if ($_REQUEST['view'] == 'import') {
			return new ImportXmlForm($this->getObject());
		}
		if ($_REQUEST['Request'] != '') {
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
}