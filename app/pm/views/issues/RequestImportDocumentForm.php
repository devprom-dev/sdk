<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporter.php";
include SERVER_ROOT_PATH . "pm/classes/issues/import/RequestImporterContentBuilder.php";

class RequestImportDocumentForm extends PMPageForm
{
    protected function extendModel()
    {
    	parent::extendModel();

    	$object = $this->getObject();
		$visible = array();
		foreach( $this->getObject()->getAttributes() as $attribute => $info ) {
            $object->resetAttributeGroup($attribute, 'additional');
			if ( in_array($attribute, $visible) ) continue;
            $object->setAttributeRequired($attribute, false);
            $object->setAttributeVisible($attribute, false);
		}
        $object->addAttribute('DocumentFile', 'FILE', translate('Файл'), true, false, text(2282), 1);

		if ( $object->getAttributeType('Type') != '' ) {
            $typeObject = $object->getAttributeObject('Type');
            $typeIt = $typeObject->getAll();
            if ( $typeIt->count() > 0 ) {
                $object->setAttributeVisible('Type', true);
            }
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

            $options = array (
                'Type' => $_REQUEST['Type'],
                'State' => $_REQUEST['State']
            );
            $importObject = getFactory()->getObject(get_class($this->getObject()));
            $builder = new RequestImporterContentBuilder($importObject);
            $importer = new WikiImporter($importObject);
            $importer_it = $importer->getAll();

            while( !$importer_it->end() ) {
                $engineClass = $importer_it->get('EngineClassName');
                if ( class_exists($engineClass) ) {
                    $engine = new $engineClass;
                    $engine->setOptions($options);
                    if ( $engine->import($builder, $fileName, $fileContent, $this->getObject()->getEmptyIterator()) ) {
                        $documentIt = $builder->getDocumentIt();
                        echo json_encode(
                            array(
                                'Id' => $documentIt->getId(),
                                'Url' => getFactory()->getObject('PMReport')->getExact('allissues')->getUrl(
                                            'ids='.\TextUtils::buildIds($documentIt->idsToArray())
                                         )
                            )
                        );
                        return true;
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
			return false;
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

    function getHint() {
        return '';
    }
}