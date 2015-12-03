<?php

include SERVER_ROOT_PATH.'ext/diff/prepend.php';
include SERVER_ROOT_PATH.'ext/diff/diff.php';

class SubversionFileDiffTable extends PMPageTable
{
    var $subversion_it;

    function SubversionFileDiffTable( $subversion_it )
    {
        $this->subversion_it = $subversion_it;

        parent::PMPageTable( $this->getObject() );
    }

    function getObject()
    {
        global $model_factory;
        return $model_factory->getObject('pm_Subversion');
    }

    function getTemplate()
    {
        return "../../plugins/sourcecontrol/templates/SubversionFileDiffTable.php";
    }
    
    function getRenderParms( $parms )
    {
        $parts = preg_split('/\//', $_REQUEST['path']);
        $file = $parts[count($parts) - 1];
        
        unset($parts[count($parts) - 1]);
        $directory = join('/', $parts);
        
        $connector = $this->subversion_it->getConnector();

        return array_merge( parent::getRenderParms( $parms ), array (
                'file_body' => $this->getDifference(
           								$connector->getTextFile($_REQUEST['path'], $_REQUEST['version']),
           								$connector->getTextFile($_REQUEST['path'], $_REQUEST['preversion'])
        						),
                'path' => $_REQUEST['path'],
                'name' => IteratorBase::utf8towin($_REQUEST['name']),
                'version' => $_REQUEST['version'],
                'preversion' => $_REQUEST['preversion']
        ));
    }
    
    function getDifference( $left, $right )
    {
 		include_once SERVER_ROOT_PATH."ext/diff/finediff.php";

 		$diff = new FineDiff (
            preg_replace('/[\r\n]+/', PHP_EOL, $left),
            preg_replace('/[\r\n]+/', PHP_EOL, $right),
            array(
                FineDiff::paragraphDelimiters,
                FineDiff::sentenceDelimiters,
                FineDiff::wordDelimiters
            )
		);
 		
 		return html_entity_decode($diff->renderDiffToHtml(), ENT_COMPAT | ENT_HTML401);
    }
} 