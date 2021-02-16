<?php
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class FilterTraceWebMethod extends FilterWebMethod
{
    private $object = null;

    function __construct( $object )
    {
        $this->object = $object;
        parent::__construct();
    }

    function getCaption() {
 		return translate('Трассировка');
 	}

 	function getValues()
 	{
  		$values = array ( 'all' => translate('Все') );

  		$attributes = array_diff(
  		    array_merge(
                $this->object->getAttributesByGroup('trace'),
                $this->object->getAttributesByGroup('trace-vertical')
            ),
            $this->object->getAttributesByGroup('system')
        );
        foreach( $attributes as $attribute ) {
            $values[$attribute . ':yep'] = sprintf(text(2906), $this->object->getAttributeUserName($attribute));
            $values[$attribute . ':none'] = sprintf(text(2907), $this->object->getAttributeUserName($attribute));
        }

 		return $values;
	}

 	function getValueParm() {
 		return 'coverage';
 	}
}