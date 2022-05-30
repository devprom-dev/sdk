<?php
include_once SERVER_ROOT_PATH."pm/views/ui/BulkForm.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";
include_once SERVER_ROOT_PATH."pm/views/wiki/fields/FieldWikiPageAttributeDictionary.php";

class WikiBulkForm extends BulkForm
{
	function getIt()
	{
		$iterator = parent::getIt();
		if ( $iterator->count() < 1 ) return $iterator;

		if ( strpos($_REQUEST['operation'], 'BulkDeleteWebMethod') === false ) {
            return $this->object->getRegistry()->useImportantPersistersOnly()->Query(
                array (
                    new FilterInPredicate($iterator->idsToArray()),
                    new SortDocumentClause()
                )
            );
        }
        else {
            return $this->object->getRegistry()->useImportantPersistersOnly()->Query(
                array (
                    join(',',array_unique($iterator->fieldToArray('ParentPage'))) != ''
                        ? new ParentTransitiveFilter($iterator->idsToArray())
                        : new SortDocumentClause(),
                    new FilterAttributePredicate('DocumentId', $iterator->fieldToArray('DocumentId')),
                    new SortDocumentClause()
                )
            );
        }
	}
	
 	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
            case 'Tag':
 			case 'Version':
 			case 'ParentPage':
 		    case 'Project':
            case 'Feature':
            case 'Branch':
            case 'CopyAttributes':
            case 'ExportTemplate':
 		    	return 'custom';
 			case 'CopyOption':
            case 'UseNumbering':
            case 'UsePaging':
            case 'ExportChildren':
            case 'ExportParents':
            case 'UseSyntax':
            case 'UseUID':
 				return 'char';
 			case 'Description':
 				return 'largetext';
            case 'File':
                return 'file';
            default:
 				return parent::getAttributeType( $attr );
 		}
 	}

 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Project':
 				return translate('Проект');
 			case 'Description':
 				return translate('Описание');
            case 'Branch':
                return translate('Бейзлайн');
            case 'CopyAttributes':
                return translate('Атрибуты');
            case 'UseNumbering':
                return text(2523);
            case 'UsePaging':
                return text(2524);
            case 'File':
                return text(2525);
            case 'ExportChildren':
                return text(2526);
            case 'ExportParents':
                return text(3142);
            case 'UseUID':
                return text(3011);
            case 'UseSyntax':
                return text(3012);
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function getDescription( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Description':
 				return ' ';

            case 'ExportTemplate':
                $module_it = getFactory()->getObject('Module')->getExact('dicts-exporttemplate');
                return preg_replace('/%2/',$module_it->getDisplayName(),
                        preg_replace('/%1/',$module_it->getUrl(),text(2527))
                    ).'<hr/>';

            case 'File':
                return text(2528);

            case 'CopyAttributes':
                return text(2944);

            default:
 				return parent::getDescription( $attr );
 		}
 	}
 	
	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
            case 'UseNumbering':
            case 'UsePaging':
            case 'UseUID':
            case 'UseSyntax':
                return 'Y';

            case 'ExportChildren':
                $pageIt = $this->getIt();
                if ( $pageIt->count() == 1 ) return 'Y';
                while( !$pageIt->end() ) {
                    if ( $pageIt->get('ParentPage') != '' ) return 'N';
                    $pageIt->moveNext();
                }
                return 'Y';

            case 'ExportParents':
                $pageIt = $this->getIt();
                while( !$pageIt->end() ) {
                    if ( $pageIt->get('ParentPage') != '' ) return 'Y';
                    $pageIt->moveNext();
                }
                return 'N';

            case 'Project':
                return getSession()->getProjectIt()->getId();

            default:
		    	return parent::getAttributeValue( $attribute );
		}
	}

    function getAttributeClass($attribute)
    {
        switch( $attribute ) {
            case 'ExportTemplate':
                return getFactory()->getObject('ExportTemplate');
            default:
                return parent::getAttributeClass($attribute);
        }
    }


    function drawCustomAttribute( $attribute, $value, $tab_index, $view )
 	{
 		switch ( $attribute )
 		{
 			case 'Version':
				$field = new FieldAutoCompleteObject( getFactory()->getObject('Baseline') );
				
				$field->setAppendable(); 
				$field->setRequired();
 				
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->draw();

				if ( $_REQUEST['Snapshot'] > 0 ) {
				    echo '<input type="hidden" name="Snapshot" value="'.$_REQUEST['Snapshot'].'">';
                }
				break;
				
 			case 'Project':
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessibleActive'));
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->setDefault($this->getAttributeValue($attribute));
				$field->draw();
				break;

            case 'ExportTemplate':
                $template = $this->getAttributeClass($attribute);
                $field = new FieldDictionary($template);
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);

                $options = array();
                $templateIt = $template->getAll();
                while( !$templateIt->end() ) {
                    $options[$templateIt->getId()] =
                        explode('-', array_shift(
                            \TextUtils::parseItems($templateIt->getHtmlDecoded('Options'))));
                    $templateIt->moveNext();
                }
                $field->setDefault(JsonWrapper::encode($options));

                $field->setScript("setFormCheckboxes($('#modal-form form'),this);");
                $field->draw();
                break;

 			case 'ParentPage':
			    $object = getFactory()->getObject(get_class($this->getObject()));
		        $object->addFilter( new FilterBaseVpdPredicate() );
			    
				$field = new FieldHierarchySelectorAppendable( $object );
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
                $field->setCrossProject(false);
				
				$field->draw();
				$field->drawScripts();
				break;

            case 'Feature':
                $field = new FieldHierarchySelector(getFactory()->getObject('Feature'));
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->draw();
                $field->drawScripts();
                break;

            case 'Tag':
                $field = new FieldAutoCompleteObject( getFactory()->getObject('Tag') );
                $field->SetId($attribute);
                $field->SetName('value');
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->setAppendable();
                $field->draw();
                break;

            case 'Branch':
                $documentIt = $this->getIt()->getRef('DocumentId');
                $objectIt = getFactory()->getObject('WikiPageBaseline')->getRegistry()->Query(
                    array(
                        new WikiPageBaselineUIDPredicate($documentIt->get('UID')),
                        new FilterAttributePredicate('Type', 'branch')
                    )
                );
                $field = new FieldDictionary($objectIt);
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->draw();
                break;

            case 'CopyAttributes':
                $field = new FieldWikiPageAttributeDictionary($this->getObject());
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue('Caption,Content,State,PageType,Author,Importance,Estimation,Tags,Attachments');
                $field->setMultiple(true);
                $field->SetTabIndex($tab_index);
                $field->setAttributes(array('attributes-multicolumn'));
                $field->draw();
                break;

			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
 		}
 	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
			case 'Version':
				return $this->IsAttributeModifiable($attribute);

			case 'Description':
            case 'ExportParents':
				return true;

            case 'CopyOption':
                return false;

            case 'ExportTemplate':
                return strpos($_REQUEST['operation'], 'MSWord') !== false
                    && getFactory()->getObject('ExportTemplate')->getRegistry()->Count(
                            array(
                                new FilterVpdPredicate()
                            )
                        ) > 0;

            case 'ExportChildren':
                $pageIt = $this->getIt();
                while( !$pageIt->end() ) {
                    if ( $pageIt->get('ParentPage') != '' ) return true;
                    $pageIt->moveNext();
                }
                return false;

			default:
				return parent::IsAttributeVisible( $attribute );
		}
	}
 	
 	function IsAttributeModifiable( $attr )
	{
		switch ( $attr )
		{
			case 'PageType':
			case 'Project':
			case 'ParentPage':
			case 'Version':
				return true;
				
			case 'ReferenceName':
			case 'Content':
			case 'IsTemplate':
			case 'IsDraft':
			case 'IsArchived':
			case 'ContentEditor':
			case 'UserField1':
			case 'UserField2':
			case 'UserField3':
			    return false;
				
			default:
				return parent::IsAttributeModifiable( $attr );
		}
	}

	function IsAttributeRequired( $attribute )
	{
		switch( $attribute )
		{
			case 'Version':
				return true;
				
			default:
				return parent::IsAttributeRequired( $attribute );
		}
	}
}