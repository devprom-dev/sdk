<?php

include_once SERVER_ROOT_PATH."pm/views/ui/BulkForm.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class WikiBulkForm extends BulkForm
{
	function getSnapshotObject()
	{
		return null;
	}
	
	function getIt()
	{
		$iterator = parent::getIt();

		if ( strpos($_REQUEST['operation'], 'BulkDeleteWebMethod') === false ) {
            return $this->object->getRegistry()->Query(
                array (
                    new FilterInPredicate($iterator->idsToArray()),
                    new SortDocumentClause()
                )
            );
        }
        else {
            return $this->object->getRegistry()->Query(
                array (
                    new ParentTransitiveFilter($iterator->idsToArray()),
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
 			case 'Snapshot':
 			case 'ParentPage':
 		    case 'Project':
            case 'Feature':
 		    	return 'custom';
 			case 'CopyOption':
            case 'UseNumbering':
            case 'UsePaging':
            case 'ExportChildren':
 				return 'char';
 			case 'Description':
 				return 'largetext';
            case 'File':
                return 'file';
            case 'ExportTemplate':
                return 'object';
            default:
 				return parent::getAttributeType( $attr );
 		}
 	}

 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
            case 'Tag':
                return translate('Тэг');
 			case 'CopyOption':
 				return text(1726);
 			case 'Project':
 				return translate('Проект');
 			case 'Description':
 				return translate('Описание');
            case 'UseNumbering':
                return text(2523);
            case 'UsePaging':
                return text(2524);
            case 'File':
                return text(2525);
            case 'ExportChildren':
                return text(2526);
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function getDescription( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'CopyOption':
 				return text(1727);

 			case 'Description':
 				return ' ';

            case 'ExportTemplate':
                $module_it = getFactory()->getObject('Module')->getExact('dicts-exporttemplate');
                return preg_replace('/%2/',$module_it->getDisplayName(),
                        preg_replace('/%1/',$module_it->getUrl(),text(2527))
                    ).'<hr/>';

            case 'File':
                return text(2528);

            default:
 				return parent::getDescription( $attr );
 		}
 	}
 	
	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
		    case 'CopyOption': 
		    	return 'N';

            case 'UseNumbering':
            case 'UsePaging':
                return 'Y';

            case 'ExportChildren':
                $pageIt = $this->getIt();
                if ( $pageIt->count() == 1 ) return 'Y';
                while( !$pageIt->end() ) {
                    if ( $pageIt->get('ParentPage') != '' ) return 'N';
                    $pageIt->moveNext();
                }
                return 'Y';

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
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 			case 'Snapshot':
				
 				$snapshot = $this->getSnapshotObject(); 
 				$snapshot->addFilter( new FilterAttributePredicate('ObjectId', $this->getIt()->idsToArray()) );
 				
 				$field = new FieldDictionary( $snapshot );
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);

				if ( $this->showAttributeCaption() ) {
					echo translate('Версия документа');
				}
				$field->draw();
				
				break;
				
 			case 'Version':
				
				$field = new FieldAutoCompleteObject( getFactory()->getObject('Baseline') );
				
				$field->setAppendable(); 
				$field->setRequired();
 				
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->draw();
				
				break;
				
 			case 'Project':
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessibleActive'));
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->setDefault($this->getAttributeValue($attribute));

				if ( $this->showAttributeCaption() ) {
					echo $this->getName($attribute);
				}
				$field->draw();
 								
				break;
				
 			case 'ParentPage':

			    $object = $model_factory->getObject(get_class($this->getObject()));
		        $object->addFilter( new FilterBaseVpdPredicate() );
			    
				$field = new FieldHierarchySelector( $object );
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
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

                if ( $this->showAttributeCaption() ) {
                    echo $this->getName($attribute);
                }
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
			case 'Snapshot':
				if ( $this->getIt()->count() > 1 ) return false;
				if ( !is_object($this->getSnapshotObject()) ) return false;
 				return $this->getSnapshotObject()->getRegistry()->Query(
 						array ( new FilterAttributePredicate('ObjectId', $this->getIt()->getId()) )
 					)->count() > 0;

			case 'Version':
				return $this->IsAttributeModifiable($attribute);

			case 'CopyOption':
			case 'Description':
				return true;

            case 'RemoveTag':
                return false;

            case 'ExportTemplate':
                return getFactory()->getObject('ExportTemplate')->getRecordCount() > 0;

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
			case 'Snapshot':
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