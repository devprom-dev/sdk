<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBaselineService;
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporter.php";

class ImportDocumentForm extends PMPageForm
{
    protected function extendModel()
    {
    	parent::extendModel();

		$visible = array();
		$object = $this->getObject();
        $system = $object->getAttributesByGroup('system');

        $object->setAttributeRequired('Caption', false);
		foreach( $object->getAttributes() as $attribute => $info ) {
			if ( in_array($attribute, $visible) ) continue;
            if ( in_array($attribute, $system) ) continue;
            if ( $object->IsAttributeRequired($attribute) ) continue;
            $object->setAttributeVisible($attribute, false);
		}

		if ( $_REQUEST['ParentPage'] != '' ) {
            $object->setAttributeVisible('ParentPage', true);
            $parentPageIt = $object->getExact($_REQUEST['ParentPage']);
            if ( $parentPageIt->get('ParentPage') == '' ) {
                $object->addAttributeGroup('PageType', 'system');
            }
        }

		$object->addAttribute('DocumentFile', 'FILE', translate('Файл'), true, false, text(2218), 1);
        $object->addAttribute('Format', 'VARCHAR', '', false, false);

        if ( !in_array('PageType', $system) ) {
            $typeIt = $object->getAttributeObject('PageType')->getAll();
            if ( $typeIt->count() > 0 ) {
                $object->setAttributeVisible('PageType', true);
            }
        }
    }
    
	function createFieldObject( $name )
	{
		switch ( $name )
		{
            case 'DocumentVersion':
                $field = new FieldAutoCompleteObject( getFactory()->getObject('Baseline') );
                $field->setAppendable();
                return $field;

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
                'State' => $_REQUEST['State'],
                'DocumentVersion'  => $_REQUEST['DocumentVersion'],
            );

			$importObject = getFactory()->getObject(get_class($this->getObject()));
			if ( $_REQUEST['Format'] == 'list' ) {
                $builder = new WikiImporterListBuilder($importObject);
            }
            else {
                $builder = new WikiImporterContentBuilder($importObject);

            }
			$importer = new WikiImporter($importObject);
            $importer_it = $importer->getAll();


            while( !$importer_it->end() ) {
                $engineClass = $importer_it->get('EngineClassName');
                if ( class_exists($engineClass) ) {
                    $engine = new $engineClass;
                    $engine->setOptions($options);
                    if ( $engine->import($builder, $fileName, $fileContent, $parent_it) )
                    {
                        $documentIt = $engine->getDocumentIt();
                        if ( $_REQUEST['DocumentVersion'] != ''  ) {
                            $service = new WikiBaselineService(getFactory(), getSession());
                            $service->storeInitialBaseline($documentIt);
                        }
                        $this->redirectOnAdded($documentIt);
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