<?php

include 'SubversionFilesList.php';

class SubversionFilesTable extends PMPageTable
{
    var $path;

    function __construct()
    {
        parent::__construct( $this->getObject() );
    }

    function getObject()
    {
        global $model_factory;
        return $model_factory->getObject('pm_Subversion');
    }

    function getSubversionIt()
    {
        global $_REQUEST;

        $values = $this->getFilterValues();

        $repo = $this->getObject();
        if ( $values['subversion'] > 0 )
        {
            $repo_it = $repo->getExact($values['subversion']);
        }
        else
        {
            $repo_it = $repo->getFirst();
            $_REQUEST['subversion'] = $repo_it->getId();
        }

        return $repo_it;
    }

    function getList()
    {
        return new SubversionFilesList( $this->object );
    }

    function getNewActions()
    {
        return array();
    }
    
    function getActions()
    {
        return array();
    }
    
    function getDeleteActions()
    {
    	return array();
    }

    function getFilters()
    {
        global $model_factory;

        $filter = new FilterObjectMethod(
                $model_factory->getObject('pm_Subversion'), '', false );

        $filter->setType( 'singlevalue' );

        $path = new FilterTextWebMethod( translate('Текущий каталог'), 'path' );
        
        $path->setStyle( 'width:600px;' );
        
        return array ( $filter,	$path );
    }

    function getFilterOrientation()
    {
        return 'left';
    }

    function getRenderParms( $parms )
    {
        $path = new FilterTextWebMethod( '', 'path' );
        
        $path->setFilter( $this->getFiltersName() );
        	
    	$_REQUEST['path'] = $path->getValue();

    	if ( $_REQUEST['path'] == '' )
    	{
    		$conn_it = $this->getSubversionIt();

    		$_REQUEST['path'] = (substr($conn_it->get('RootPath'), 0, 1) != "$" ? '/' : '') .$conn_it->get('RootPath'); // kkorenkov: todo: remove this ugly hack for TFS
    	}
        
    	return parent::getRenderParms( $parms );
    }
    
    function drawFooter()
    {
        $it = $this->getSubversionIt();
        
        if ( $it->count() > 0 )
        {
            echo '<div class="line">';
            echo translate('Путь к файлам').': '.
                    $it->get('SVNPath').'/'.$it->get('RootPath');
            echo '</div>';
        }
    }
}