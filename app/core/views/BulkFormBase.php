<?php
include_once "FormAsync.php";
		
class BulkFormBase extends AjaxForm
{
 	function getCommandClass()
 	{
		return 'bulkcomplete';
 	}

 	function extendModel()
    {
        parent::extendModel();
        foreach( getFactory()->getPluginsManager()->getPluginsForSection(getSession()->getSite()) as $plugin ) {
            $plugin->interceptMethodBulkFormExtendModel($this);
        }
    }

	function getAttributes()
	{
		$attributes = $this->getActionAttributes();
		$this->visibleAttributes = $attributes;
		return array_merge( array('operation'), $attributes, array('ids') );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return ''; 	
			case 'operation':
				return translate('Действие');

			default:
			    if ( !$this->showAttributeCaption() ) return '';
				return parent::getName( $attribute );
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return 'custom'; 	

			case 'operation':
				return 'custom';

			default:
				if ( is_object($this->getForm()) ) return 'custom';
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return join(',',$this->getIt()->idsToArray());

			case 'operation':
				return htmlentities(
                    str_replace('=ids', '='.\TextUtils::buildIds($this->getIds()), $_REQUEST['operation'])
                );

			default:
			    $value = htmlentities($_REQUEST[$attribute]);
			    if ( $value != '' ) return $value;

			    if ( $this->getObject()->IsReference($attribute) ) {
			        $ref = $this->getObject()->getAttributeObject($attribute);
			        if ( $ref instanceof Project ) return $value;
                }

                $persistedData = array_unique($this->getIt()->fieldToArray($attribute));
                if ( count($persistedData) == 1 ) return array_shift($persistedData);

				return $value;
		}
	}
	
 	function getDescription( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'operation': return ' ';
 			case 'ids': return '';
 			
 			default:
 			    $text = parent::getDescription( $attr );
                if ( $this->getAttributeType($attr) == 'char' ) return $text;
 				return $text.' ';
 		}
 	}
	
	function getForm()
	{
		if ( !is_object($this->form) ) {
			$this->form = $this->buildForm();
			if ( is_object($this->form) ) {
                $this->form->setPage($this->getPage());
                $this->form->buildForm();
                $this->setObject($this->form->getObject());
				$this->form->setObjectIt(
                    $this->form->getObject()->createCachedIterator(
                        $this->getIt()->getRowset()));
                $this->form->getRenderParms();
			}
		}
		return $this->form;
	}

	protected function buildForm() {
		return null;
	}
	
	function getActionAttributes()
	{
		$attributes = array();

		$match = preg_match('/^Attribute(.+)$/mi', $_REQUEST['operation'], $attributes);
		if ( $match ) {
			$attributes = preg_split('/:/', $attributes[1]);
			$this->object->setAttributeVisible(array_shift(array_values($attributes)), true);
			return $attributes;
		}

		$match = preg_match('/^Method:(.+)$/mi', $_REQUEST['operation'], $attributes);
		if ( $match )
		{
			$parms = preg_split('/:/', $attributes[1]);

			$method = $parms[0]; 
			array_shift($parms);
			
			$attrs = array();
			if ( count($parms) > 0 )
			{
				foreach( $parms as $parm )
				{
					$pair = preg_split('/=/', $parm);
					$attrs[$pair[0]] = $pair[1];
				}
			}

			$attributes = array();
			foreach( $attrs as $attribute => $value )
			{
				if ( $value != '' ) continue;
				$attributes[] = $attribute;
			    $this->object->setAttributeVisible( $attribute, true );
			}

            if ( $this->getMethod() == 'BulkDeleteWebMethod' && $attrs['class'] != '' )  {
                $className = getFactory()->getClass($attrs['class']);
                if ( class_exists($className) ) {
                    $this->setObject(getFactory()->getObject($className));
                }
            }

            return $attributes;
		}
		
		return $attributes;
	}
	
	function IsAttributeRequired( $attribute )
	{
		switch( $attribute )
		{
			case 'ids':
			case 'operation':
				return false;

			default:
				return parent::IsAttributeRequired( $attribute );
		}
	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
		    case 'operation':
		    	return $this->getAttributeValue($attribute) == '';
			case 'ids':
				return true;
		    default:
		    	return parent::IsAttributeVisible($attribute) || in_array($attribute, $this->visibleAttributes);
		}
	}
	
	function IsAttributeModifiable( $attr )
	{
	    $system_attributes =
				array_merge(
						$this->getObject()->getAttributesByGroup('system'),
						$this->getObject()->getAttributesByGroup('nonbulk')
				);
	    if ( in_array($attr, $system_attributes) ) return false;
	    if ( !$this->getObject()->hasAttribute($attr) ) return true;
	    return parent::IsAttributeModifiable( $attr );
	}

	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute ) 
		{
			case 'ids':
				$this->drawIds( $value );
				break;
				
			default:
				$form = $this->getForm();
				if ( is_object($form) )
				{
					$field = $form->createField($attribute);
                    if ( !is_object($field) ) return parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );

					$field->setId($form->getId().$attribute);
					$field->SetName($attribute);
					$field->SetValue($value);
					$field->SetTabIndex($tab_index);
					$field->SetRequired($this->getObject()->IsAttributeRequired($attribute));
					
					if ( is_a($field, 'FieldForm') ) {
						echo '<span id="'.$field->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;">';
							$field->render($this->getView());
						echo '</span>';
					}
					else {
						$field->render($this->getView());
					}
				}
				else {
					parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
				}
		}
	}
	
	function getIt()
	{
	    if ( is_object($this->it) ) {
	        return $this->it->object->createCachedIterator($this->it->getRowset());
        }

	    $ids = $this->getIds();
	    if ( count($ids) < 1 ) {
	        return $this->getObject()->getEmptyIterator();
        }

	    $this->it = $this->getObject()->getExact($ids);
	    return $this->it->object->createCachedIterator($this->it->getRowset());
	}

	function getIds() {
		return array_unique(
		    \TextUtils::parseIds($_REQUEST['ids'])
        );
	}
	
	function drawIds( $value )
	{
		global $_SERVER;
		
		$uid = new ObjectUID;
		$object = $this->getObject();

		$it = $this->getIt();

		if ( $it->count() < 1 ) {
            echo '<br/>';
            echo '<div class="alert alert-error">'.$this->getEmptyIteratorMessage().'</div>';
            echo '<br/>';
            return;
        }

        echo '<br/>';
		echo '<label><b>'.text(1303).'</b></label>';
		
		echo '<input type="hidden" name="ids" value="'.$value.'">';
		echo '<input type="hidden" name="object" value="'.strtolower(get_class($object)).'">';
        echo '<input type="hidden" name="form" value="'.get_class($this->getForm()).'">';
			
		$it->moveFirst();
		while ( !$it->end() && $it->getPos() < 7 )
		{
			echo '<div class="line">';
				$uid->drawUidInCaption($it);
			echo '</div>';

			$it->moveNext();
		}
		
		if ( $it->getPos() + 1 < $it->count() )
		{
			echo '<div class="line">';
				echo str_replace('%1', $it->count() - $it->getPos(), text(1064));
			echo '</div>';
		}
		
		echo '<br/>';
	}
	
	protected function showAttributeCaption()
	{
		return !preg_match('/Attribute(.+)/mi', $this->getAttributeValue('operation'), $match);		
	}

	function getMethod() {
		preg_match('/Method:(.+)/mi', $_REQUEST['operation'], $attributes);
		return array_shift(preg_split('/:/', $attributes[1]));
	}

	function getHintId() {
		return $this->getMethod();
	}

	function getEmptyIteratorMessage() {
 	    return text(2530);
    }

    function createFieldObject( $attribute_type, $name )
    {
        switch ( $attribute_type )
        {
            case 'char':
                return !$this->showAttributeCaption()
                    ? new FieldYesNo()
                    : parent::createFieldObject( $attribute_type, $name );
            default:
                return parent::createFieldObject( $attribute_type, $name );
        }
    }

	private $form = null;
	private $visibleAttributes = array();
}