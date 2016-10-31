<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporter.php";

class ImportDocumentForm extends PMPageForm
{
    protected function extendModel()
    {
    	parent::extendModel();

		$visible = array();
		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
			if ( in_array($attribute, $visible) ) continue;
			$this->getObject()->setAttributeRequired($attribute, false);
			$this->getObject()->setAttributeVisible($attribute, false);
		}

		$this->getObject()->addAttribute('DocumentFile', 'FILE', translate('Файл'), true, false, text(2218));
    }
    
	function createFieldObject( $name )
	{
		switch ( $name )
		{		
			default:
				return parent::createFieldObject( $name );
		}
	}

	function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();

		try {
		    $filePath = $_FILES['DocumentFile']['tmp_name'];
            $fileName = $_FILES['DocumentFile']['name'];
			if ( !is_uploaded_file($filePath) ) {
				throw new Exception(\FileSystem::translateError($_FILES['DocumentFile']['error']));
			}

			$importer = new WikiImporter($this->getObject());
            $importer_it = $importer->getAll();

            while( !$importer_it->end() ) {
                $engineClass = $importer_it->get('EngineClassName');
                if ( class_exists($engineClass) ) {
                    $engine = new $engineClass;
                    if ( $engine->import($this->getObject(), $fileName, file_get_contents($filePath)) ) {
                        $this->redirectOnAdded($engine->getDocumentIt());
                    }
                }
                $importer_it->moveNext();
            }

			throw new Exception(text(2217));
		}
		catch( Exception $e ) {
			$this->setRequiredAttributesWarning();
			$this->setWarningMessage($e->getMessage());
			$this->edit('');
		}
	}

	function getCaption() {
        return $this->getObject()->getDocumentName();
    }
}