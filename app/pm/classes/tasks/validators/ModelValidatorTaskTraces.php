<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorTaskTraces extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array $parms )
	{
        if ( $parms['ObjectClass'] != 'Task' ) return "";
	    if ( $parms['Task'] == '' || $parms['ObjectId'] == '' ) return "";

        if ( $parms['Task'] == $parms['ObjectId'] ) {
            return text(2916);
        }

        $registry = $object->getRegistryBase();

        // serach for cycles from left to right across chain
        $checkIds = array($parms['ObjectId']);
        while( count($checkIds) > 0 ) {
            $it = $registry->Query(array(
                    new FilterAttributePredicate('Task', $checkIds),
                    new FilterAttributePredicate('ObjectClass', 'Task')
                ));
            $checkIds = $it->fieldToArray('ObjectId');
            if ( in_array($parms['Task'], $checkIds) ) {
                return text(2916);
            }
        }

        // serach for cycles from right to left across chain
        $checkIds = array($parms['Task']);
        while( count($checkIds) > 0 ) {
            $it = $registry->Query(array(
                new FilterAttributePredicate('ObjectId', $checkIds),
                new FilterAttributePredicate('ObjectClass', 'Task')
            ));
            $checkIds = $it->fieldToArray('Task');
            if ( in_array($parms['ObjectId'], $checkIds) ) {
                return text(2916);
            }
        }

        return "";
	}
}