<?php

include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelPageTableBuilder.php";

include SERVER_ROOT_PATH."pm/views/reports/ReportTable.php";
include SERVER_ROOT_PATH.'pm/views/import/ImportXmlForm.php';

include "RequestForm.php";
include "RequestTable.php";
include "RequestBulkForm.php";
include "RequestPlanningForm.php";
include "IssueBurndownSection.php";
include "IssueEstimationSection.php";
include "IssueCompoundSection.php";
include "RequestIteratorExportBlog.php";
include "IteratorExportIssueBoard.php"; 
include "PageSettingIssuesBuilder.php";
include "import/ImportIssueFromExcelSection.php";

class RequestPage extends PMPage
{
 	var $release_it;
 	
 	function __construct()
 	{
 		global $_REQUEST, $model_factory;

 		getSession()->addBuilder( new PageSettingIssuesBuilder() ); 
 		
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
 		        if ( !$form->getEditMode() )
 		        {
 		            $this->addInfoSection( new PageSectionComments($object_it) );
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
	 		    
	 			$this->addInfoSection( new IssueEstimationSection() );
	 		}
 		}
 		
 		if( $_REQUEST['view'] == 'import' )
 		{
 			$this->addInfoSection( new ImportIssueFromExcelSection($this->getObject()));
 		}
 	}
 	
	function getObject()
	{
		getSession()->addBuilder( new RequestModelExtendedBuilder() );
		
 		return getFactory()->getObject('Request');
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
 	
 	function getForm() 
 	{
 		switch ( $_REQUEST['mode'] )
 		{
 		    case 'group':
	 			$form = new RequestPlanningForm();
	 			
	 			$form->edit( $_REQUEST['ChangeRequest'] );
	 			
	 			return $form;
	 			
 			case 'bulk':
 				return new RequestBulkForm( $this->getObject() );
 		}
 		
 		if ( $_REQUEST['view'] == 'import' )
 		{
 		    return new ImportXmlForm($this->getObject());
 		}
 		
		return new RequestForm($this->getObject());
 	}
}