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

		if ( $this->getObject() instanceof WikiPageTemplate ) return $iterator;
		if ( strpos($_REQUEST['operation'], 'BulkDeleteWebMethod') === false ) return $iterator;

		return $this->object->getRegistry()->Query(
				array (
						new WikiRootTransitiveFilter($iterator->idsToArray())
				)
		);
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
 				return 'char';
 		    	
 			case 'Description':
 				return 'largetext';
 				
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
		    	
		    default:
		    	return parent::getAttributeValue( $attribute );
		}
	}

 	function drawCustomAttribute( $attribute, $value, $tab_index )
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
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessible'));
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->setDefault(getSession()->getProjectIt()->getId());

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
 				parent::drawCustomAttribute( $attribute, $value, $tab_index );
 		}
 	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
			case 'Snapshot':
				if ( $this->getIt()->count() > 1 ) return false;
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