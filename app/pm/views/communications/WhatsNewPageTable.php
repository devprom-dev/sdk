<?php
include_once SERVER_ROOT_PATH . "pm/methods/ViewLogSubjectWebMethod.php";
include "WhatsNewPageList.php";

class WhatsNewPageTable extends PMPageTable
{
	function getList() {
		return new WhatsNewPageList( $this->getObject() );
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' ) {
			return 'RecordModified.D';
		}
		
		return parent::getSortDefault( $sort_parm );
	}
	
	function getFilters()
	{
		return array_merge(
		    parent::getFilters(),
            array(
                $this->buildStartFilter(),
                $this->buildStateFilter(),
                $this->buildActionsFilter(),
                $this->buildEntityFilter(),
                new ViewLogSubjectWebMethod()
            )
        );
	}
	
	function buildStartFilter() {
		return new ViewStartDateWebMethod();
	}
	
	function buildEntityFilter()
	{
		$entity_filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogEntitySet'), '', 'entities' );
		$entity_filter->setHasNone( false );
		$entity_filter->setIdFieldName( 'ClassName' );
        $entity_filter->setHasAny(false);
		
		return $entity_filter;
	}

    function buildStateFilter()
    {
        $entity_filter = new FilterObjectMethod( getFactory()->getObject('ChangeNotificationType'), translate('Состояние'), 'state' );
        $entity_filter->setHasNone( false );
        $entity_filter->setHasAll( false );
        $entity_filter->setDefaultValue('new');
        return $entity_filter;
    }

    function buildActionsFilter()
    {
        $filter = new FilterObjectMethod( getFactory()->getObject('ChangeLogAction'), '', 'action' );
        $filter->setHasNone( false );
        $filter->setIdFieldName( 'ReferenceName' );
        return $filter;
    }

	function getFilterPredicates( $values )
	{
		$filters = array(
            new ChangeLogActionFilter( $values['action'] ),
			new ChangeLogStartFilter( $values['start'] ),
            new ChangeLogVisibilityFilter(),
            new FilterAttributeNotNullPredicate('Caption'),
            new ChangeLogObjectFilter( $values['entities'] ),
			new ChangeLogParticipantFilter( $values['participant'] ),
		);

		if ( $values['state'] == 'new' ) {
		    $filters[] = new ChangeLogSinceNotificationFilter(getSession()->getUserIt());
        }
		
		return array_merge(
		    parent::getFilterPredicates( $values ),
            $filters
        );
	}
	
	function getActions()
	{
		return array();
	}
	
	function getDeleteActions()
	{
		return array();
	}

	function getNewActions()
    {
        return array();
    }

    function getExportActions()
    {
        return array();
    }

    function getBulkActions()
    {
        $action_url = "javascript:processBulk('".text(2463)."','?formonly=true&operation=Method:MarkChangesAsReadWebMethod:objects=%ids%', '', devpromOpts.updateUI);";
        return array(
            'delete' => array(
                array (
                    'uid' => 'bulk-delete',
                    'name' => text(2463),
                    'url' => $action_url
                )
            )
        );
    }

    function IsNeedToDelete() { return false; }

    protected function getFamilyModules( $module )
    {
        return array(
            'project-log',
            'project-question'
        );
    }
}