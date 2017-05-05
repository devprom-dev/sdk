<?php
include 'c_requestsimport.php';

class RequestsImportXml extends RequestsImport
{
 	function validate()
 	{
		$this->checkRequired( array('object') );
		$this->request = getFactory()->getObject($_REQUEST['object']);

		// proceeds with validation
		if( !is_uploaded_file($_FILES['Excel']['tmp_name']) ) {
			$this->replyError( $this->getResultDescription( 1 ) );
		}
		$this->setFileName($_FILES['Excel']['name']);

		return true;
 	}
 	
 	function getObject() {
 		return $this->request;
 	}

 	function getLines()
	{
        $filePath = $_FILES['Excel']['tmp_name'];
        if ( !file_exists($filePath) ) return array();

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($filePath);
            return $objPHPExcel->getActiveSheet()->toArray(null,false,false,true);
        }
        catch( Exception $e ) {
            $this->replyError( $e->getMessage() );
            return array();
        }
	}
}
