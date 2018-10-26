<?php

class FieldReferenceAttribute extends Field
{
	private $actions = null;
	private $reload = false;
	private $attribute = '';
	private $attributeObject = null;
	private $moreActions = array();
	private $extraClass = '';

	function __construct( $object_it, $attribute, $attributeObject = null, $moreActions = array(), $reload = false, $extraClass = '' )
    {
		$this->reload = $reload;
        $this->object_it = $object_it;
        $this->attribute = $attribute;
        $this->attributeObject = is_object($attributeObject)
            ? $attributeObject
            : $this->object_it->object->getAttributeObject($attribute);
        $this->moreActions = $moreActions;
		$this->actions = $this->buildActions();
        $this->extraClass = $extraClass;
	}

	function getActions() {
		return $this->actions;
	}

	function getObjectIt() {
		return $this->object_it;
	}

	function setObjectIt( $object_it ) {
		$this->object_it = $object_it;
	}

	function draw( $view = null )
	{
		foreach( $this->actions as $key => $action )
		{
			$method = $action['method'];
			$method->setObjectIt($this->object_it);
			$this->actions[$key]['url'] = $method->getJSCall();
		}

        $uid = new ObjectUID;
        $priorityIt = $this->object_it->getRef($this->attribute, $this->attributeObject);

        if ( count($this->moreActions) > 0 ) {
            $this->actions = array_merge($this->actions, array(array()), $this->moreActions);
        }

		echo $view->render('pm/AttributeButton.php', array (
			'data' => $uid->getUidTitle($priorityIt),
			'items' => $this->actions,
			'extraClass' => $this->extraClass,
			'random' => $this->attribute . $this->object_it->getId(),
            'title' => $this->object_it->object->getAttributeUserName($this->attribute)
		));
	}

	protected function buildActions()
	{
		$actions = array();
        $empty_it = $this->object_it;

        $priorityIt = $this->attributeObject->getVpdValue() == ''
            ? $this->attributeObject->getAll()
            : $this->attributeObject->getRegistry()->Query(
                    array(
                        new FilterVpdPredicate($this->object_it->get('VPD'))
                    )
                );

		while( !$priorityIt->end() )
		{
			$method = new ModifyAttributeWebMethod($empty_it, $this->attribute, $priorityIt->getId());
			if ( $method->hasAccess() ) {
				if ( !$this->reload ) {
					$method->setCallback( "donothing" );
				}
				else {
					$method->setCallback( "function() {window.location.reload();}" );
				}
				$actions[$priorityIt->getId()] = array(
                    'name' => $priorityIt->getDisplayName(),
                    'method' => $method
				);
			}
            $priorityIt->moveNext();
		}

		if ( !array_key_exists('', $actions) && !$empty_it->object->IsAttributeRequired($this->attribute) ) {
            $method = new ModifyAttributeWebMethod($empty_it, $this->attribute, '');
            if ( $method->hasAccess() ) {
                if ( !$this->reload ) {
                    $method->setCallback( "donothing" );
                }
                else {
                    $method->setCallback( "function() {window.location.reload();}" );
                }
                $actions[0] = array(
                    'name' => text(2536),
                    'method' => $method
                );
            }
        }

		return $actions;
	}
}
