<?php
include_once "FilterWebMethod.php";

class FilterObjectMethod extends FilterWebMethod
{
 	var $object;
 	var $has_all;
 	var $parmvalue;
 	var $it = null;
 	var $idfieldname;
 	var $none_title;
 	var $use_uid;
 	var $has_none;
 	private $rowsVisibilityLimit = 15;
 	private $has_any = true;
 	private $lazyload = false;
 	private $lazyloadurl = '';

 	function FilterObjectMethod( $object = null, $title = '', $parmvalue = '', $has_all = true )
 	{
 		global $_REQUEST;
 		
 		parent::FilterWebMethod();

 		if ( is_object($object) )
 		{
 			if ( is_a($object, 'Metaobject') ) 
 			{
		 		$this->object = $object;
		 		$this->lazyloadurl = getSession()->getApplicationUrl($object) . 'methods.php?method=FilterObjectMethod&class='.get_class($object);
 			}
 			else 
 			{
		 		$this->it = $object;
 				$this->object = $object->object;
 			}
 		}
 		
 		$this->has_all = $has_all;
 		$this->has_none = true;
 		$this->parmvalue = $parmvalue;
 		$this->use_uid = true;
 		
 		if ( is_object($this->object) )
	 		$this->idfieldname = $this->object->getClassName().'Id'; 
 		
 		if ( is_object($this->object) )
 		{
	 		$this->title = $title != '' 
	 			? $title : translate($this->object->getDisplayName());
	 			
	 		if ( $this->parmvalue == '' )
				$this->parmvalue = strtolower(get_class($this->object));

	 		$this->has_any = !$this->object instanceof CacheableSet;
 		}

 		if ( $this->parmvalue == '' ) $this->parmvalue = $_REQUEST['object'];

 		$this->none_title = translate('<нет значения>');
	}
 	
 	function getModule() {
 		return '';
 	}

 	function setIdFieldName( $field ) {
 		$this->idfieldname = $field;
 	}
 	
 	function setNoneTitle( $title ) {
 	    $this->none_title = $title;
 	}
 	
  	function setHasAll( $has_all ) {
 	    $this->has_all = $has_all;
 	}

 	function setHasAny( $flag ) {
 	    $this->has_any = $flag;
    }

    function getHasAny() {
        $this->has_any;
    }

 	function getHasAll() {
        return $this->has_all;
    }
 	
 	function setHasNone( $has_none ) {
 	    $this->has_none = $has_none;
 	}
 	
 	function setUseUid( $use_uid ) {
 	    $this->use_uid = $use_uid;
 	}

 	function setLazyLoad( $value ) {
 	    $this->lazyload = $value;
    }

    function getLazyLoad() {
 	    return $this->lazyload;
    }

    function getLazyLoadUrl() {
 	    return $this->lazyloadurl;
    }

	function getCaption() {
		return $this->title;
	}

	function getValues()
	{
		$values = array();

		if ( !getFactory()->getAccessPolicy()->can_read($this->object) ) return $values;

		$uid = new ObjectUID;
		
		if ( !is_object($this->it) )
		{
			$registry = $this->object->getRegistry();
			$parms = array();
            if ( $this->getLazyLoad() ) {
                $registry->setLimit(30);
            }
			if ( $this->object->getVpdValue() != '' ) {
                $parms[] = new EntityProjectPersister();
                $parms[] = new FilterVpdPredicate();
            }
	 		$this->it = $registry->Query($parms);
		}

		$selected_values = \TextUtils::parseItems(
            $this->parseFilterValue($this->getValue(), $this->object)
        );

		while ( !$this->it->end() )
		{
			$display_name = $this->use_uid ? $uid->getUidTitle($this->it) : ' '.$this->it->getDisplayName();
			if ( mb_strlen($display_name) > 80 ) {
                $display_name = TextUtils::getWords($display_name, 8);
            }
			$item_value = $this->it->get($this->idfieldname);

			if ( $item_value == '' ) 
			{
				$this->it->moveNext();
				continue;
			}

            $selected_values = array_filter(
                $selected_values,
                function($item) use ($item_value) {
                    return $item != $item_value;
                }
            );

			if ( !isset($values[$display_name]) )
			{
				$values[$display_name] = array($item_value);
			}
			elseif ( !in_array($item_value, $values[$display_name]) )
			{
				$values[$display_name][] = $item_value;
			}
				
			$this->it->moveNext();
		}

		array_walk( $values, function(&$value) {
				$value = is_array($value) ? ' '.join(',', $value) : '';
		});

		$values = array_flip($values);
		$itemsCount = count($values);

		array_walk( $values, function(&$value) {
				$value = trim($value);
		});

		if ( $this->object->getEntityRefName() == 'cms_User' ) {
            $values = array_merge(
                array (
                    'user-id' => text(2480),
                    'user-tasks' => text(3205)
                ),
                $values
            );
        }
		if ( $this->has_none ) {
			$values = array_merge( array ( 'none' => $this->none_title ), $values );
		}
        if ( $this->has_any ) {
            $values = array_merge( array ( 'any' => text(2689) ), $values );
        }
		if ( $this->has_all ) {
			$values = array_merge( array ( 'all' => translate('Все') ), $values );
		}
		if ( $itemsCount > $this->rowsVisibilityLimit ) {
			$values = array_merge( array ( 'search' => array( 'uid' => 'search') ), $values );
		}
        if ( $this->object->getPage() != '?' ) {
            $values = array_merge(
                $values,
                array (
                    '_options' => array( 'uid' => 'options', 'href' => $this->object->getPage() )
                )
            );
        }

        if ( count($selected_values) < 1 ) return $values;
 		if ( count(array_intersect($selected_values, array('', 'all', 'none'))) > 0 ) return $values;

 		$registry = new ObjectRegistrySQL($this->object);
		$object_it = $registry->Query(
				array (
						$this->it->getIdAttribute() == $this->idfieldname
							? new FilterInPredicate($selected_values)
							: new FilterAttributePredicate($this->idfieldname, $selected_values),
						new FilterVpdPredicate()
				)
		);
		while ( !$object_it->end() ) {
			$values[' '.$object_it->get($this->idfieldname)] = $uid->getUidTitle($object_it);
			$object_it->moveNext();
		}

		return $values;
	}

	function getValueText( $value ) {
        return $this->object->getExact($value)->getDisplayName();
    }

    function getValuesText( $values )
    {
        $items = array();
        $it = $this->object->getExact($values);
        while( !$it->end() ) {
            $items[] = $it->getDisplayName();
            $it->moveNext();
        }
        return $items;
    }

	function getValueParm() {
		return $this->parmvalue;
	}

    function parseFilterValue($value, $context)
    {
        $value = preg_replace('/user-id/i', getSession()->getUserIt()->getId(), $value);

        $value = preg_replace_callback('/(user\-tasks)/i', function($match) use($context) {
            if ( $context->getEntityRefName() == 'pm_Task' ) {
                $attribute = 'Assignee';
            }
            else if ( $context->getEntityRefName() == 'pm_ChangeRequest' ) {
                $attribute = 'Owner';
            }
            else {
                return '';
            }
            $aggregate = new AggregateBase( $attribute, '1', 'COUNT' );
            $context->addAggregate($aggregate);
            $it = $context->getAggregated();

            $users = array();
            while( !$it->end() ) {
                if ( $it->get($aggregate->getAggregateAlias()) > 0 ) {
                    $users[] = $it->get($attribute);
                }
                $it->moveNext();
            }
            return join(',', $users);
        }, $value);

        return $value;
    }

	function execute_request()
    {
        $method = new AutocompleteWebMethod();
        $method->execute_request();
    }
}