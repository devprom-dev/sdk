<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporter.php";
include SERVER_ROOT_PATH . "pm/classes/issues/import/RequestImporterContentBuilder.php";

class RequestImportDocumentForm extends PMPageForm
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
		$this->getObject()->addAttribute('DocumentFile', 'FILE', translate('Файл'), true, false, text(2282), 1);

        $typeObject = $this->getObject()->getAttributeObject('Type');
        $typeIt = $typeObject->getAll();
        if ( $typeIt->count() > 0 ) {
            $this->getObject()->setAttributeVisible('Type', true);
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

            $options = array (
                'Type' => $_REQUEST['Type']
            );
            $builder = new RequestImporterContentBuilder($this->getObject());
            $importer = new WikiImporter($this->getObject());
            $importer_it = $importer->getAll();

            while( !$importer_it->end() ) {
                $engineClass = $importer_it->get('EngineClassName');
                if ( class_exists($engineClass) ) {
                    $engine = new $engineClass;
                    $engine->setOptions($options);
                    if ( $engine->import($builder, $fileName, file_get_contents($filePath), $this->getObject()->getEmptyIterator()) ) {
                        $documentIt = $builder->getDocumentIt();
                        echo json_encode(
                            array(
                                'Id' => $documentIt->getId(),
                                'Url' => getFactory()->getObject('PMReport')->getExact('allissues')->getUrl(
                                            'request='.join(',',$documentIt->idsToArray())
                                         )
                            )
                        );
                        exit();
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

    function createFieldObject( $name )
    {
        switch ( $name )
        {
            case 'Type':
                return new FieldIssueTypeDictionary($this->getObject());
            default:
                return parent::createFieldObject( $name );
        }
    }
}