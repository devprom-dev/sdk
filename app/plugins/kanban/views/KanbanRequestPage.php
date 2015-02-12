<?php

include SERVER_ROOT_PATH.'pm/views/issues/RequestPage.php';

include "KanbanComulativeFlowSection.php";
include "KanbanEstimationSection.php";
include "KanbanRequestTable.php";
include "KanbanPageSettingsBuilder.php";

class KanbanRequestPage extends RequestPage
{
 	function __construct()
 	{
 		getSession()->addBuilder( new KanbanPageSettingsBuilder() );
 		
 		parent::__construct();
 		
 		if ( $this->needDisplayForm() ) return;
 		
 		if ( !is_a($this->getTableRef(), 'KanbanRequestTable') ) return;

 		$_REQUEST['view'] = 'board';

 		$this->addInfoSection( new FullScreenSection() );
 		$this->addInfoSection(new KanbanComulativeFlowSection($this->getReleaseIt()));
 		$this->addInfoSection(new KanbanEstimationSection($this->getReleaseIt()));
 	}
 	
 	function getDefaultTable()
 	{
		return new KanbanRequestTable( $this->getObject() );
 	}
}