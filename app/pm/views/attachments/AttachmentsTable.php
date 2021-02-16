<?php
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
        $filters[] = new FilterDateWebMethod(translate('Добавлено после'), 'start');
        $filters[] = new ViewFinishDateWebMethod('Добавлено до');
        return $filters;
    }

    function getFilterPredicates( $values )
    {
        return array (
            new FilterModifiedAfterPredicate( $values['start'] ),
            new FilterModifiedBeforePredicate( $values['finish'] ),
            new AttachmentClassPredicate($values['class'])
        );
    }

    public function buildFilterValuesByDefault( & $filters )
    {
        $values = parent::buildFilterValuesByDefault( $filters );
        if ( $values['start'] == '' ) {
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j'))));
        }
        return $values;
    }

	function getNewActions() {
		return array();
	}

	function getImportActions() {
        return array();
    }

    function getDetails()
    {
        $settings = parent::getDetails();
        unset($settings['props']);
        unset($settings['form']);
        return $settings;
    }
}