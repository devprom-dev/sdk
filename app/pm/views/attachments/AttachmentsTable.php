<?php
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include "AttachmentsList.php";

class AttachmentsTable extends PMPageTable
{
	function getList() {
		return new AttachmentsList( $this->getObject() );
	}

    function getFilters()
    {
        $filters = array();
        $filters[] = new FilterObjectMethod(getFactory()->getObject('AttachmentEntity'), text(2098), 'class');
        $filters[] = new ViewStartDateWebMethod(translate('Добавлено после'));
        $filters[] = new ViewFinishDateWebMethod('Добавлено до');
        return $filters;
    }

	function getFiltersDefault() {
		return array('any');
	}

    function getFilterPredicates()
    {
        $values = $this->getFilterValues();
        return array (
            new FilterModifiedAfterPredicate( $values['start'] ),
            new FilterModifiedBeforePredicate( $values['finish'] ),
            new AttachmentClassPredicate($values['class'])
        );
    }

	function getNewActions() {
		return array();
	}
}