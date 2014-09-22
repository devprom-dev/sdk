<?php

include 'SubversionRevisionDetailsList.php';

class SubversionRevisionDetailsTable extends PMPageTable
{
    function getFilterPredicates()
    {
        global $model_factory;
        
        if ( $_REQUEST['pm_SubversionRevisionId'] > 0 )
        {
            $revision = $model_factory->getObject('pm_SubversionRevision');
            $revision_it = $revision->getExact($_REQUEST['pm_SubversionRevisionId']);
            
            if ( $revision_it->getId() > 0 )
            {
                $_REQUEST['version'] = $revision_it->get('Version');
                $_REQUEST['subversion'] = $revision_it->get('Repository');
            }
        }
        
        return parent::getFilterPredicates();
    }

    function getList()
    {
        return new SubversionRevisionDetailsList( $this->getObject() );
    }
    		
    function getFilterActions()
    {
    	return array();
    }

    function getSubversionIt()
    {
        global $model_factory, $_REQUEST;

        $repo = $model_factory->getObject('pm_Subversion');
        $repo_it = $repo->getExact($_REQUEST['subversion']);

        return $repo_it;
    }

    function getCaption()
    {
        global $_REQUEST;

        return translate('Файлы, измененные в версии').': '.SanitizeUrl::parseUrl($_REQUEST['version']);
    }

    function getActions()
    {
        return array();
    }
    
    function getNewActions()
    {
        return array();
    }
    
    function getDeleteActions()
    {
    	return array();
    }

    function drawFilter()
    {
        return;
    }
}