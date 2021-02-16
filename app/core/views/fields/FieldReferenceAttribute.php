<?php

class FieldReferenceAttribute extends Field
{
	private $attribute = '';
	private $attributeObject = null;
	private $moreActions = array();
	private $extraClass = '';
	private $lovObject = null;

	function __construct( $object_it, $attribute, $attributeObject, $moreActions = array(), $extraClass = '' )
    {
        $this->object_it = $object_it;
        $this->attribute = $attribute;
        $this->attributeObject = $attributeObject;
        $this->lovObject = $this->attributeObject;
        $this->moreActions = $moreActions;
        $this->extraClass = $extraClass;
	}

	function getObjectIt() {
		return $this->object_it;
	}

	function setObjectIt( $object_it ) {
		$this->object_it = $object_it;
	}

	function setLovObject( $object ) {
	    $this->lovObject = $object;
    }

    function getLovObject() {
	    return $this->lovObject;
    }

    function getAttribue() {
	    return $this->attribute;
    }

    protected function getLovIterator()
    {
        return $this->lovObject->getVpdValue() == ''
            ? $this->lovObject->getAll()
            : $this->lovObject->getRegistry()->Query(
                array(
                    new FilterVpdPredicate($this->object_it->get('VPD'))
                )
            );
    }

	function draw( $view = null )
	{
        $actions = $this->buildActions();
		foreach( $actions as $key => $action )
		{
			$method = $action['method'];
			$method->setObjectIt($this->object_it);
            $actions[$key]['url'] = $method->getJSCall();
		}

        $uid = new ObjectUID;
		if ( $this->object_it->get($this->attribute) == '' ) {
		    if ( $this->attributeObject instanceof RequestType ) {
                $priorityIt = $this->attributeObject->getAll();
                $priorityIt->moveToId('');
            }
		    else {
                $priorityIt = $this->attributeObject->getEmptyIterator();
            }
        }
		else {
		    if ( $this->attributeObject instanceof MetaobjectCacheable ) {
		        $priorityIt = $this->attributeObject->getExact($this->object_it->get($this->attribute));
            }
		    else {
                $registry = getFactory()->getObject($this->attributeObject->getEntityRefName())->getRegistry();
                $priorityIt = $registry->Query(
                    array(
                        new FilterInPredicate($this->object_it->get($this->attribute))
                    )
                );
            }
        }

        if ( $this->readOnly() ) {
            echo '<span style="white-space: nowrap;">' . $uid->getUidTitle($priorityIt) . '</span>';
        }
        else {
            if ( count($this->moreActions) > 0 ) {
                $actions = array_merge($actions, array(array()), $this->moreActions);
            }

            echo $view->render('pm/AttributeButton.php', array (
                'data' => $uid->getUidTitle($priorityIt),
                'items' => $actions,
                'extraClass' => $this->extraClass,
                'random' => $this->attribute . $this->object_it->getId(),
                'title' => $this->object_it->object->getAttributeUserName($this->attribute),
                'id' => $this->getId()
            ));
        }
	}

	protected function buildActions()
	{
		$actions = array();
        $empty_it = $this->object_it;

        $priorityIt = $this->getLovIterator();
		while( !$priorityIt->end() )
		{
			$method = new ModifyAttributeWebMethod($empty_it, $this->attribute, $priorityIt->getId());
			if ( $method->hasAccess() ) {
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
                $actions[0] = array(
                    'name' => text(2536),
                    'method' => $method
                );
            }
        }

		return $actions;
	}
}
