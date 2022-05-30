<?php
include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';
include_once SERVER_ROOT_PATH.'pm/views/issues/FieldMergeStrategyDictionary.php';

class RequestBulkForm extends BulkForm
{
    private $formClass;

    function __construct( $object, $formClass )
    {
        parent::__construct($object);
        $this->formClass = $formClass;
    }

    function buildForm()
 	{
		$object = $this->getObject();

		$object->addAttribute('TransitionComment', 'WYSIWYG', translate('Комментарий'), false);
        $object->addAttribute('SourceIssue', 'REF_RequestId', translate('Пожелание'), false);
        $object->addAttribute('MergeType', 'REF_RequestId', text(2819), false);
        $object->addAttribute('MasterIssue', 'REF_RequestId', text(2820), false);

        $class_name = $this->formClass;
 		return new $class_name($object);
 	}

	function getActionAttributes()
	{
		$attributes = parent::getActionAttributes();

		if ( in_array('BlockReason', $attributes) ) {
			$attributes[] = 'CreateLinked';
            $attributes[] = 'TransitionComment';
		}

		return $attributes;
	}

	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Tag':
            case 'Watchers':
 			case 'Iteration':
 			case 'LinkType':
            case 'CreateLinked':
 				return 'custom';
            case 'IncrementType':
                return 'object';
 			default:
 				return parent::getAttributeType( $attr );
 		}
 	}

 	function getAttributeClass($attribute)
    {
        switch ( $attribute )
        {
            case 'IncrementType':
                return getFactory()->getObject('RequestType');
            default:
                return parent::getAttributeClass($attribute);
        }
    }

    function getName( $attr )
 	{
 		switch ( $attr )
 		{
            case 'Watchers':
                return '';
 			case 'LinkType':
 				return translate('Тип связи');
            case 'IncrementType':
                return translate('Тип');
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
            case 'RemoveWatchers':
			case 'Tasks':
		    	return false;
            case 'CreateLinked':
                return true;
		    default:
		    	return parent::IsAttributeVisible( $attribute );
		}
	}

    function IsAttributeRequired( $attribute )
    {
        switch ( $attribute )
        {
            case 'IncrementType':
                return true;
            default:
                return parent::IsAttributeRequired( $attribute );
        }
    }

 	function IsAttributeModifiable( $attr )
	{
	    switch ( $attr ) 
	    {
            case 'CreateLinked':
            case 'SourceIssue':
                return true;
	        default:
	            return parent::IsAttributeModifiable( $attr );
	    }
	}

 	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
 	{
 		switch ( $attribute )
 		{
 			case 'Iteration':
 			    $iteration = getFactory()->getObject('IterationRecent');
 			    $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
 			    $field = new FieldAutoCompleteObject( $iteration );
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->draw();
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

            case 'Watchers':
                $field = new FieldAutoCompleteObject( getFactory()->getObject('IssueAuthor') );
                $field->SetId($attribute);
                $field->SetName('value');
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->setAppendable();
                $field->draw();
                break;

 			case 'LinkType':
 				$type = getFactory()->getObject('RequestLinkType');
				$field = new FieldAutoCompleteObject($type);
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($type->getByRef('ReferenceName', 'implemented')->getId());
				$field->SetTabIndex($tab_index);
				$field->draw();
				break;

            case 'CreateLinked':
                $method = new ObjectCreateNewWebMethod($this->getObject());
                $url = $method->getJSCall(
                    array(
                        'IssueLinked' => array_shift($this->getIds()),
                        'LinkType' => getFactory()->getObject('RequestLinkType')->getByRef('ReferenceName', 'blocks')->getId()
                    )
                );
                echo '<div class="btn-group">';
                    echo '<a class="btn btn-sm btn-success" href="'.$url.'">';
                        echo '<i class="icon-plus icon-white"></i> '.$this->getObject()->getDisplayName();
                    echo '</a>';
                echo '</div>';
                echo '<div class="clearfix"></div>';
                echo '<br/>';
                break;

            case 'MergeType':
                $field = new FieldMergeStrategyDictionary();
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue('1');
                $field->setDefault('1');
                $field->setNullOption(false);
                $field->SetTabIndex($tab_index);
                $field->SetRequired(true);
                $field->draw();
                break;

            case 'MasterIssue':
                $field = new FieldDictionary($this->getIt());
                $field->SetId($attribute);
                $field->SetName($attribute);
                $field->SetValue($this->getIt()->getId());
                $field->SetTabIndex($tab_index);
                $field->setNullOption(false);
                $field->SetRequired(true);
                $field->draw();
                break;

			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
 		}
 	}
}
 