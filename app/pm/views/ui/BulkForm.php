<?php
use Devprom\ProjectBundle\Service\Email\CommentNotificationService;
include_once SERVER_ROOT_PATH."core/views/BulkFormBase.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/views/comments/FieldCheckNotifications.php";

class BulkForm extends BulkFormBase
{
 	function getCommandClass()
 	{
		return 'bulkcompleteproject';
 	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'TransitionComment':
				return translate('Комментарий'); 	
				
 			case 'Watchers':
 				return translate('Наблюдатели');

            case 'Tag':
                return '';

			default:
				return parent::getName( $attribute );
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'TransitionComment':
				return 'wysiwyg';

			case 'Watchers':
            case 'TransitionNotification':
            case 'RemoveTag':
            case 'Project':
				return 'custom';
				
			default:
				if ( $this->getObject()->IsReference($attribute) && $this->getObject()->getAttributeObject($attribute) instanceof PMCustomDictionary )
				{
					return 'custom';
				}
				
				return parent::getAttributeType( $attribute );
		}
	}

	function getActionAttributes()
	{
		$match = preg_match('/Transition(.+)/mi', $_REQUEST['operation'], $attributes);
		if ( $match )
		{
			$object = $this->getIt()->object;
			$_REQUEST['Transition'] = trim($attributes[1]);

			$system_attributes =
                array_merge(
                    $object->getAttributesByGroup('system'),
                    $object->getAttributesByGroup('nonbulk')
                );

			$formData = array();
            foreach( $object->getAttributes() as $attribute => $data )
            {
                if ( in_array($attribute, $system_attributes) ) continue;

                $actualData = array_unique($this->getIt()->fieldToArray($attribute));
                if ( count($actualData) > 1 && !in_array('', $actualData) ) {
                    $formData[$attribute] = join(',', $actualData);
                }
            }

            $model_builder = new WorkflowTransitionAttributesModelBuilder(
                getFactory()->getObject('Transition')->getExact(trim($attributes[1])),
                array(),
                $formData
			);
		    $model_builder->build($object);
		    
		    $ref_names = array();
			foreach( $object->getAttributes() as $attribute => $data )
			{
				if ( in_array($attribute, $system_attributes) ) continue;
				if ( !$this->IsAttributeVisible($attribute) ) continue;

				$ref_names[] = $attribute;
			}
		    return $ref_names;
		}
		
		return parent::getActionAttributes();
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute ) 
		{
 			case 'Watchers':
				$field = new FieldWatchers( $this->getObject() );
				$field->SetId($attribute);
				$field->SetName('value');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				echo '<span id="'.$field->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;">';
				    $field->draw();
				echo '</span>';
				break;

            case 'TransitionNotification':
                $options = new CommentNotificationService($this->getIt());
                $field = new FieldCheckNotifications();
                $field->setEmails($options->getEmails());
                $field->setPrivate(
                    $options->getPrivate(
                        getFactory()->getObject('Transition')->getExact($_REQUEST['Transition'])
                    )
                );
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetTabIndex($tab_index);
                $field->draw();
                break;

            case 'Project':
                $field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessibleActive'));
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->SetRequired(true);
                $field->draw();
                break;

            case 'RemoveTag':
                $it = $this->getIt();
                switch( $it->object->getEntityRefName() ) {
                    case 'pm_ChangeRequest':
                        $tags = array_unique(getFactory()->getObject('RequestTag')->getRegistry()->Query(
                                        array(
                                            new FilterAttributePredicate('Request', $it->idsToArray())
                                        )
                                    )->fieldToArray('Tag'));
                        break;
                    case 'WikiPage':
                        $tags = array_unique(getFactory()->getObject('WikiTag')->getRegistry()->Query(
                                        array(
                                            new FilterAttributePredicate('Wiki', $it->idsToArray())
                                        )
                                    )->fieldToArray('Tag'));
                        break;
                    default:
                        $tags = array_unique(getFactory()->getObject('CustomTag')->getRegistry()->Query(
                            array(
                                new FilterAttributePredicate('ObjectClass', strtolower(get_class($it->object)))
                            )
                        )->fieldToArray('Tag'));
                }

                $tagIt = getFactory()->getObject('Tag')->getExact($tags);
                $field = new FieldDictionary($tagIt);
                $field->setId($attribute);
                $field->setName($attribute);
                $field->setTabIndex($tab_index);
                $field->setMultiple(true);
                $field->draw();
                break;

			default:
				if ( $this->getObject()->IsReference($attribute) ) {
					$ref_object = $this->getObject()->getAttributeObject($attribute);
					if ( $ref_object instanceof PMCustomDictionary ) {
						$field = new FieldCustomDictionary($this->getObject(), $attribute);
						$field->SetId($attribute);
						$field->SetName($attribute);
						$field->SetValue($value == '' ? $this->getObject()->getDefaultAttributeValue($attribute) : $value);
						$field->SetTabIndex($tab_index);
						$field->draw();
						return;
					}
				}

				if ( $this->getAttributeType($attribute) == 'wysiwyg' ) {
					$field = new FieldWYSIWYG();
					$field->setObject($this->getObject());
					$editor = $field->getEditor();
					$editor->setMode( WIKI_MODE_NORMAL );
					$field->setCssClassName( 'wysiwyg-text' );
					$field->SetId($attribute);
					$field->SetName($attribute);
					$field->SetTabIndex($tab_index);
					$field->draw();
					return;
				}

				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		}
	}

	function getHint()
	{
		switch( $this->getMethod() ) {
			case 'BulkDeleteWebMethod':
				return preg_replace('/%1/', getFactory()->getObject('PMReport')->getExact('project-log')->getUrl(), text(2210));
            default:
                $methodName = strtolower($this->getMethod());
                if ( $methodName != '' ) {
                    $resource_it = getFactory()->getObject('ContextResource')->getExact($methodName);
                    if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');
                }
		}
		return parent::getHint();
	}
}