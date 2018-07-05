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

		if ( $_REQUEST['ParentPage'] != '' ) {
            $this->getObject()->setAttributeVisible('ParentPage', true);
        }
		$this->getObject()->addAttribute('DocumentFile', 'FILE', translate('Файл'), true, false, text(2218), 1);
        $this->getObject()->addAttribute('Format', 'VARCHAR', '', false, false);

        $typeObject = $this->getObject()->getAttributeObject('PageType');
        $typeIt = $typeObject->getAll();
        if ( $typeIt->count() > 0 ) {
            $this->getObject()->setAttributeVisible('PageType', true);
        }
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

            $fileContent = file_get_contents($filePath);
            if ( $fileContent == '' ) throw new Exception(text(2486));

			if ( $_REQUEST['ParentPage'] != '' ) {
                $parent_it = $this->getObject()->getExact($_REQUEST['ParentPage']);
            }
            else {
                $parent_it = $this->getObject()->getEmptyIterator();
            }

            $options = array (
                'PageType' => $_REQUEST['PageType'],
                'State' => $_REQUEST['State']
            );
			if ( $_REQUEST['Format'] == 'list' ) {
                $builder = new WikiImporterListBuilder($this->getObject());
            }
            else {
                $builder = new WikiImporterContentBuilder($this->getObject());

            }
			$importer = new WikiImporter($this->getObject());
            $importer_it = $importer->getAll();


            while( !$importer_it->end() ) {
                $engineClass = $importer_it->get('EngineClassName');
                if ( class_exists($engineClass) ) {
                    $engine = new $engineClass;
                    $engine->setOptions($options);
                    if ( $engine->import($builder, $fileName, $fileContent, $parent_it) ) {
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