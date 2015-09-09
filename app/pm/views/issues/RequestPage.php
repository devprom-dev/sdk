<?php

include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderIssues.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestViewModelCommonBuilder.php";

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

 		getSession()->addBuilder( new PageSettingIssuesBuilder() );
 		getSession()->addBuilder( new RequestViewModelCommonBuilder() );
 		getSession()->addBuilder( new BulkActionBuilderIssues() ); 
 		getSession()->addBuilder( new RequestModelExtendedBuilder() );
 		
		if ( $_REQUEST['release'] > 0 )
		{
			$release = $model_factory->getObject('Release');
			
			$this->release_it = $release->getExact($_REQUEST['release']);
		}

 		parent::__construct();

 		if ( $_REQUEST['view'] == 'chart' ) return;
 		
 		if ( $this->needDisplayForm() )
 		{
 		    $object_it = $this->getObjectIt();
 		    
 		    $form = $this->getFormRef();
 		    
 		    if( is_object($object_it) && $object_it->getId() > 0 )
 		    {
	            $this->addInfoSection( new PageSectionComments($object_it) );
 		        if ( $object_it->object->getAttributeType('Spent') != '' )
 		        {
 		        	$this->addInfoSection( new PageSectionSpentTime( $object_it ) );
 		        }
 		        $this->addInfoSection( new StatableLifecycleSection( $object_it ) );
 		        $this->addInfoSection( new PMLastChangesSection ( $object_it ) );
 		    }
 		}
 		elseif ( $_REQUEST['mode'] == '' )
 		{
 		    if ( $_REQUEST['view'] == 'board' ) $this->addInfoSection( new FullScreenSection() );
 		    
 		    $table = $this->getTableRef();
 			
 			if ( is_object($table) )
 			{
	 			$filter = new FilterObjectMethod( 
 					$model_factory->getObject('Release'), '', 'release');
	 			$filter->setFilter( $table->getFiltersName() );
	 			
	 			$value = $filter->getValue();
	 			if ( is_numeric($value) && $value > 0 )
	 			{
					$release = $model_factory->getObject('Release');
					$this->release_it = $release->getExact($value);
	 			}
 			}
 			
	 		if ( !$this->needDisplayForm() && is_object($table) )
	 		{
	 		    $this->addInfoSection( new IssueBurndownSection() );
	 		}
 		}
 		
 		if( $_REQUEST['view'] == 'import' )
 		{
 			$this->addInfoSection( new ImportIssueFromExcelSection($this->getObject()));
 		}
 	}
 	
	function getObject()
	{
		$object = getFactory()->getObject('Request');
		$object->addPersister( new IssueLinkedIssuesPersister() );
		
	    foreach(getSession()->getBuilders('RequestViewModelBuilder') as $builder ) {
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
 		if ( $_REQUEST['view'] != 'chart' )
 		{
			getSession()->addBuilder( new RequestModelPageTableBuilder() );
 		}
 				
 		switch ( $_REQUEST['kind'] )
 		{
 			case 'submitted':
 				return $this->getDefaultTable();
	 				
 			default:
 				if ( $_REQUEST['view'] == 'chart' && $_REQUEST['report'] == '' )
   		        {
   		            if ( $_REQUEST['pmreportcategory'] == '' ) $_REQUEST['pmreportcategory'] = 'issues';
             		        
       		        return new ReportTable(getFactory()->getObject('PMReport'));
   		        }
   		        else
   		        {
   		            return $this->getDefaultTable();
   		        }
 		}
 	}
 	
 	function getDefaultTable()
 	{
		return new RequestTable( $this->getObject() );
 	}

 	function needDisplayForm()
 	{
 		return $_REQUEST['view'] == 'import' || in_array($_REQUEST['mode'], array('bulk','group')) 
 				? true : parent::needDisplayForm();
 	}
 	
 	function getBulkForm()
 	{
 		return new RequestBulkForm( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		switch ( $_REQUEST['mode'] )
 		{
 		    case 'group':
	 			$form = new RequestPlanningForm($this->getObject());
	 			$form->edit( $_REQUEST['ChangeRequest'] );
	 			return $form;
 		}
 		if ( $_REQUEST['view'] == 'import' )
 		{
 		    return new ImportXmlForm($this->getObject());
 		}
 		if ( $_REQUEST['Request'] != '' )
 		{
 			return new RequestFormDuplicate($this->getObject());
 		}
		return new RequestForm($this->getObject());
 	}
}